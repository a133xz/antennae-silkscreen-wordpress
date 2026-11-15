#!/usr/bin/env node
/**
 * Read-only FTP check: confirm deploy paths and theme folder (no uploads).
 *
 *   npm run verify
 *   npm run verify -- --file style.css
 *   npm run verify -- --within 30
 */

import { createWriteStream, existsSync, readFileSync, statSync, unlinkSync } from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { finished } from 'node:stream/promises';
import {
  THEME_DIR,
  loadEnv,
  remotePath,
  requireEnv,
  root,
  withFtpClient,
} from './deploy-common.js';

const THEME_HEADER = /Theme Name:\s*Silkscreen 2026/i;

function parseArgs(argv) {
  const args = { file: 'style.css', withinMinutes: 30, verbose: false };
  for (let i = 0; i < argv.length; i += 1) {
    const arg = argv[i];
    if (arg === '--verbose') args.verbose = true;
    else if (arg === '--file') {
      args.file = argv[i + 1];
      if (!args.file) throw new Error('--file needs a path under wordpress/, e.g. style.css');
      i += 1;
    } else if (arg === '--within') {
      const n = Number(argv[i + 1]);
      if (!Number.isFinite(n) || n <= 0) throw new Error('--within needs a positive number of minutes');
      args.withinMinutes = n;
      i += 1;
    } else if (arg === '--help' || arg === '-h') {
      args.help = true;
    } else {
      throw new Error(`Unknown argument: ${arg}`);
    }
  }
  return args;
}

function localRelForFile(fileArg) {
  const normalized = fileArg.replace(/\\/g, '/').replace(/^\/+/, '');
  if (normalized.startsWith(`${THEME_DIR}/`)) return normalized;
  return `${THEME_DIR}/${normalized}`;
}

function formatAge(ms) {
  const minutes = Math.round(ms / 60000);
  if (minutes < 1) return 'less than 1 minute ago';
  if (minutes === 1) return '1 minute ago';
  return `${minutes} minutes ago`;
}

async function downloadToTemp(client, remote) {
  const tmp = path.join(os.tmpdir(), `silkscreen-ftp-verify-${process.pid}-${path.basename(remote)}`);
  const dest = createWriteStream(tmp);
  await client.downloadTo(dest, remote);
  await finished(dest);
  return tmp;
}

async function main() {
  const args = parseArgs(process.argv.slice(2));
  if (args.help) {
    process.stdout.write(`Read-only FTP path and theme check (no uploads).

Usage
  npm run verify
  npm run verify -- --file style.css
  npm run verify -- --within 30

Uses the same .env and path rules as npm run deploy.
`);
    return;
  }

  loadEnv(path.join(root, '.env'));
  requireEnv();

  const localRel = localRelForFile(args.file);
  const localAbs = path.join(root, localRel);
  if (!existsSync(localAbs)) {
    throw new Error(`Local file missing: ${localRel}`);
  }

  const remoteDir = process.env.FTP_REMOTE_DIR.replace(/\/$/, '');
  const remoteFile = remotePath(localRel, process.env.FTP_REMOTE_DIR);
  const localStat = statSync(localAbs);

  process.stdout.write('FTP verify (read-only, no uploads)\n');
  process.stdout.write(`  host:       ${process.env.FTP_HOST}\n`);
  process.stdout.write(`  user:       ${process.env.FTP_USER}\n`);
  process.stdout.write(`  remote dir: ${remoteDir}\n`);
  process.stdout.write(`  local:      ${localRel} (${localStat.size} bytes)\n`);
  process.stdout.write(`  remote:     ${remoteFile}\n`);
  process.stdout.write(`  deploy would upload: ${localRel} -> ${remoteFile}\n\n`);

  const markers = ['style.css', 'functions.php', 'index.php'];
  let remoteModified = null;
  let remoteSize = null;
  let themeHeaderOk = null;
  let recentModifyOk = null;

  await withFtpClient(args.verbose, async (client) => {
    const home = await client.pwd();
    process.stdout.write(`Connected. FTP home: ${home}\n`);

    const listing = await client.list(remoteDir);
    const names = new Set(listing.filter((e) => e.isFile).map((e) => e.name));
    process.stdout.write(`Remote directory listing (${remoteDir}): ${listing.length} entries\n`);
    for (const name of markers) {
      process.stdout.write(`  ${names.has(name) ? 'OK' : 'MISSING'}  ${name}\n`);
    }
    if (!names.has('style.css')) {
      throw new Error(
        `Remote folder does not look like a WordPress theme (no style.css in ${remoteDir}). Check FTP_REMOTE_DIR.`,
      );
    }

    try {
      remoteSize = await client.size(remoteFile);
      process.stdout.write(`\nRemote ${path.basename(remoteFile)} size: ${remoteSize} bytes\n`);
    } catch {
      process.stdout.write(`\nCould not SIZE ${remoteFile} (server may not support SIZE on that path).\n`);
    }

    try {
      remoteModified = await client.lastMod(remoteFile);
      const ageMs = Date.now() - remoteModified.getTime();
      recentModifyOk = ageMs <= args.withinMinutes * 60 * 1000;
      process.stdout.write(
        `Remote ${path.basename(remoteFile)} modified: ${remoteModified.toISOString()} (${formatAge(ageMs)})\n`,
      );
      process.stdout.write(
        `Modified within last ${args.withinMinutes} min: ${recentModifyOk ? 'yes' : 'no'}\n`,
      );
    } catch (error) {
      process.stdout.write(`\nMDTM not available for ${remoteFile}: ${error.message}\n`);
      const styleEntry = listing.find((e) => e.name === path.basename(remoteFile));
      if (styleEntry?.modifiedAt) {
        remoteModified = styleEntry.modifiedAt;
        const ageMs = Date.now() - remoteModified.getTime();
        recentModifyOk = ageMs <= args.withinMinutes * 60 * 1000;
        process.stdout.write(
          `From directory listing: ${remoteModified.toISOString()} (${formatAge(ageMs)})\n`,
        );
        process.stdout.write(
          `Modified within last ${args.withinMinutes} min: ${recentModifyOk ? 'yes' : 'no'}\n`,
        );
      }
    }

    if (localRel.endsWith('style.css')) {
      const tmp = await downloadToTemp(client, remoteFile);
      try {
        const head = readFileSync(tmp, 'utf8').slice(0, 2048);
        themeHeaderOk = THEME_HEADER.test(head);
        process.stdout.write(
          `\nRemote style.css Theme Name "Silkscreen 2026": ${themeHeaderOk ? 'yes' : 'no'}\n`,
        );
      } finally {
        unlinkSync(tmp);
      }
    }

    if (remoteSize !== null) {
      const sizeMatch = remoteSize === localStat.size;
      process.stdout.write(
        `Local vs remote size (${path.basename(remoteFile)}): ${sizeMatch ? 'match' : 'DIFFER'} (local ${localStat.size}, remote ${remoteSize})\n`,
      );
    }
  });

  process.stdout.write('\nSummary\n');
  process.stdout.write('  Paths aligned with deploy: yes\n');
  process.stdout.write('  Theme folder markers: style.css present\n');
  if (themeHeaderOk !== null) {
    process.stdout.write(`  Correct theme (Silkscreen 2026): ${themeHeaderOk ? 'yes' : 'NO — wrong folder?'}\n`);
  }
  if (recentModifyOk !== null) {
    process.stdout.write(
      `  style.css touched in last ${args.withinMinutes} min: ${recentModifyOk ? 'yes' : 'no (expected if you have not deployed recently)'}\n`,
    );
  }
  process.stdout.write('\nNo files were uploaded.\n');

  if (themeHeaderOk === false) {
    process.exitCode = 1;
  }
}

main().catch((error) => {
  process.stderr.write(`${error.message}\n`);
  process.exitCode = 1;
});
