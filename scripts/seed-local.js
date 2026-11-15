import { existsSync } from 'node:fs';
import path from 'node:path';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');

const phpCandidates = [
  process.env.PHP_BIN,
  '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php',
  'php',
].filter(Boolean);

const php = phpCandidates.find((bin) => bin === 'php' || existsSync(bin));
if (!php) {
  console.error('PHP not found. Set PHP_BIN or run from Local’s site shell.');
  process.exit(1);
}

const wpLoad =
  process.env.WP_LOAD_PATH ||
  '/Users/antonio/Local Sites/localpress/app/public/wp-load.php';

const seedScript = existsSync(path.join(root, 'wordpress', 'seed', 'import-demo.php'))
  ? path.join('wordpress', 'seed', 'import-demo.php')
  : path.join('seed', 'import-demo.php');
const args = [seedScript, ...process.argv.slice(2)];
const env = {
  ...process.env,
  WP_LOAD_PATH: wpLoad,
  HOME: process.env.HOME || process.env.USERPROFILE || '',
};

execFileSync(php, args, { cwd: root, stdio: 'inherit', env });
