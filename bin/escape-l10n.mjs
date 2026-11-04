// Removes the raw control bytes that `wp i18n make-php` writes into resources/lang/*.l10n.php.
// WP-CLI writes the gettext separators as raw bytes: \x04 between a context and its msgid, and \0
// between plural forms. Theme Check reports them as "non-printable characters".
// Every such byte sits inside a single-quoted PHP string, so it is replaced by '."\xNN".'
// The string loads the same way. PHP then checks that the file still returns the same array.
// Run it with `npm run i18n`.
import { execFileSync } from 'node:child_process';
import { copyFileSync, readFileSync, readdirSync, rmSync, writeFileSync } from 'node:fs';

const dir = 'resources/lang';
const control = /[\x00-\x08\x0B\x0C\x0E-\x1F]/;

for (const name of readdirSync(dir).filter((file) => file.endsWith('.l10n.php'))) {
  const file = `${dir}/${name}`;
  const source = readFileSync(file, 'latin1');

  if (!control.test(source)) {
    continue;
  }

  const backup = `${file}.orig`;
  copyFileSync(file, backup);
  writeFileSync(file, source.replace(new RegExp(control, 'g'), (byte) => `'."\\x${byte.charCodeAt(0).toString(16).padStart(2, '0').toUpperCase()}".'`), 'latin1');

  const same = execFileSync('php', ['-r', 'echo (require $argv[1]) === (require $argv[2]) ? "1" : "0";', backup, file]).toString();

  if (same !== '1') {
    copyFileSync(backup, file);
    rmSync(backup);
    console.error(`Escaped ${file} no longer loads the same strings; left it unchanged.`);
    process.exit(1);
  }

  rmSync(backup);

  console.log(`Escaped ${file}`);
}
