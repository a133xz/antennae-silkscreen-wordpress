#!/usr/bin/env node
/**
 * Upload changed WordPress theme files (wordpress/) over FTP.
 *
 *   npm run deploy
 *   npm run deploy -- --dry-run
 *   npm run deploy -- --since HEAD~1
 *   npm run deploy -- --all
 *
 * Credentials live in .env (see .env.example). Password is never printed.
 */

import { execFileSync } from 'node:child_process';
import { existsSync, statSync } from 'node:fs';
import path from 'node:path';
import {
  THEME_DIR,
  loadEnv,
  remotePath,
  requireEnv,
  root,
  withFtpClient,
} from './deploy-common.js';

function git(args) {
  return execFileSync('git', args, { cwd: root, encoding: 'utf8' }).trim();
}

function lines(text) {
  return text ? text.split('\n').filter(Boolean) : [];
}

function parseArgs(argv) {
  const args = { dryRun: false, all: false, since: null, verbose: false };
  for (let i = 0; i < argv.length; i += 1) {
    const arg = argv[i];
    if (arg === '--dry-run') args.dryRun = true;
    else if (arg === '--all') args.all = true;
    else if (arg === '--verbose') args.verbose = true;
    else if (arg === '--since') {
      args.since = argv[i + 1];
      if (!args.since) throw new Error('--since needs a git ref, for example HEAD~1');
      i += 1;
    } else if (arg === '--help' || arg === '-h') {
      args.help = true;
    } else {
      throw new Error(`Unknown argument: ${arg}`);
    }
  }
  return args;
}

function themeFiles({ all, since }) {
  if (all) {
    const tracked = lines(git(['ls-files', '--', THEME_DIR]));
    const untracked = lines(git(['ls-files', '--others', '--exclude-standard', '--', THEME_DIR]));
    return { upload: unique([...tracked, ...untracked]), deleted: [] };
  }

  const ref = since || 'HEAD';
  const upload = lines(git(['diff', '--name-only', '--diff-filter=ACMRT', ref, '--', THEME_DIR]));
  const untracked = lines(git(['ls-files', '--others', '--exclude-standard', '--', THEME_DIR]));
  const deleted = lines(git(['diff', '--name-only', '--diff-filter=D', ref, '--', THEME_DIR]));
  return { upload: unique([...upload, ...untracked]), deleted };
}

function unique(items) {
  return [...new Set(items)].filter((file) => {
    const abs = path.join(root, file);
    return existsSync(abs) && statSync(abs).isFile();
  });
}

async function uploadFiles(files, remoteDir, verbose) {
  await withFtpClient(verbose, async (client) => {
    const home = await client.pwd();

    for (const file of files) {
      const remote = remotePath(file, remoteDir);
      const remoteFolder = path.posix.dirname(remote);
      process.stdout.write(`upload ${file} -> ${remote}\n`);
      await client.cd(home);
      await client.ensureDir(remoteFolder);
      await client.uploadFrom(path.join(root, file), path.posix.basename(remote));
    }
  });
}

function printHelp() {
  process.stdout.write(`Upload changed files in ${THEME_DIR} to the WordPress theme directory.

Usage
  npm run deploy
  npm run deploy -- --dry-run
  npm run deploy -- --since HEAD~1
  npm run deploy -- --all

Default compares the working tree with HEAD, including new untracked files.
--since <ref> uploads files changed since that commit, including uncommitted edits.
--all uploads the whole theme.
Deleted local files are listed and left on the server.
`);
}

async function main() {
  const args = parseArgs(process.argv.slice(2));
  if (args.help) {
    printHelp();
    return;
  }

  loadEnv(path.join(root, '.env'));
  const { upload, deleted } = themeFiles(args);

  if (!upload.length && !deleted.length) {
    process.stdout.write('No theme changes to upload.\n');
    if (!args.since && !args.all) {
      process.stdout.write('Working tree matches HEAD. Use --since HEAD~1 for the last commit, or --all for the whole theme.\n');
    }
    return;
  }

  if (upload.length) {
    process.stdout.write(`${upload.length} file${upload.length === 1 ? '' : 's'} to upload:\n`);
    for (const file of upload) process.stdout.write(`  ${file}\n`);
  }
  if (deleted.length) {
    process.stdout.write('Left on the server (deleted locally):\n');
    for (const file of deleted) process.stdout.write(`  ${file}\n`);
  }

  if (args.dryRun) {
    process.stdout.write('Dry run. Nothing was uploaded.\n');
    return;
  }

  if (!upload.length) return;

  requireEnv();
  await uploadFiles(upload, process.env.FTP_REMOTE_DIR, args.verbose);
  process.stdout.write('Done.\n');
}

main().catch((error) => {
  process.stderr.write(`${error.message}\n`);
  process.exitCode = 1;
});
