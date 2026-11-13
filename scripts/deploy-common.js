import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { Client } from 'basic-ftp';

export const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
export const THEME_DIR = 'wordpress';

export function loadEnv(filePath) {
  if (!existsSync(filePath)) return;
  const text = readFileSync(filePath, 'utf8');
  for (const line of text.split('\n')) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;
    const eq = trimmed.indexOf('=');
    if (eq === -1) continue;
    const key = trimmed.slice(0, eq).trim();
    let value = trimmed.slice(eq + 1).trim();
    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'"))
    ) {
      value = value.slice(1, -1);
    }
    if (process.env[key] === undefined) process.env[key] = value;
  }
}

export function requireEnv() {
  const missing = ['FTP_HOST', 'FTP_USER', 'FTP_PASSWORD', 'FTP_REMOTE_DIR'].filter(
    (key) => !process.env[key],
  );
  if (missing.length) {
    throw new Error(
      `Missing ${missing.join(', ')} in .env. Copy .env.example to .env and fill in the FTP login.`,
    );
  }
}

export function ftpSecure() {
  const value = (process.env.FTP_SECURE || 'false').toLowerCase();
  if (value === 'implicit') return 'implicit';
  if (value === 'true' || value === 'explicit') return true;
  return false;
}

export function remotePath(localRel, remoteDir) {
  const rel = localRel.slice(THEME_DIR.length + 1).split(path.sep).join('/');
  const base = remoteDir.replace(/\/$/, '');
  return `${base}/${rel}`;
}

export async function withFtpClient(verbose, fn) {
  const client = new Client(120000);
  client.ftp.verbose = verbose;
  const secure = ftpSecure();
  try {
    await client.access({
      host: process.env.FTP_HOST,
      user: process.env.FTP_USER,
      password: process.env.FTP_PASSWORD,
      port: Number(process.env.FTP_PORT || (secure === 'implicit' ? 990 : 21)),
      secure,
    });
    return await fn(client);
  } finally {
    client.close();
  }
}
