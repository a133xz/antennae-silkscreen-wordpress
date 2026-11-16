#!/usr/bin/env node
/**
 * Upload legacy-silkscreen/ to wp-content/themes/silkscreen (original one-page theme).
 * Does not touch silkscreen-2026.
 */

import path from 'path';
import fs from 'fs';
import { loadEnv, requireEnv, withFtpClient, root } from './deploy-common.js';

const LEGACY_DIR = 'legacy-silkscreen';

function legacyRemoteDir(remote2026Dir) {
  return remote2026Dir.replace(/silkscreen-2026\/?$/, 'silkscreen').replace(/\/$/, '');
}

async function main() {
  loadEnv(path.join(root, '.env'));
  requireEnv();

  const localDir = path.join(root, LEGACY_DIR);
  if (!fs.existsSync(localDir)) {
    throw new Error(`Missing ${LEGACY_DIR}/`);
  }

  const files = fs.readdirSync(localDir).filter((name) => {
    const abs = path.join(localDir, name);
    return fs.statSync(abs).isFile();
  });

  const remoteDir = legacyRemoteDir(process.env.FTP_REMOTE_DIR);
  process.stdout.write(`Legacy theme deploy → ${remoteDir}\n`);

  await withFtpClient(false, async (client) => {
    const home = await client.pwd();
    await client.cd(home);
    await client.ensureDir(remoteDir);

    for (const name of files) {
      const local = path.join(localDir, name);
      process.stdout.write(`  upload ${name}\n`);
      await client.uploadFrom(local, `${remoteDir}/${name}`);
    }
  });

  process.stdout.write('Done. Purge LiteSpeed cache after switching to the Silkscreen theme.\n');
}

main().catch((err) => {
  process.stderr.write(`${err.message}\n`);
  process.exitCode = 1;
});