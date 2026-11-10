// Copies the Lucide icons the templates use into resources/assets/icons, where Libraries\Icon reads them.
// Add a name here when a template starts using a new icon, then run `npm run build:icons`.
import { copyFileSync, existsSync, mkdirSync } from 'node:fs';

const icons = [
  'archive', 'arrow-down-to-line', 'arrow-left', 'arrow-right', 'arrow-up-right', 'audio-lines',
  'book-audio', 'book-open', 'calendar', 'calendar-clock', 'check', 'chevron-down', 'chevron-left', 'chevron-right',
  'circle-help', 'clock', 'copy', 'file-text', 'flame', 'folder', 'globe', 'hash', 'headphones', 'heart',
  'house', 'info', 'layout-grid', 'link', 'list', 'mail', 'menu', 'messages-square', 'mic',
  'monitor', 'moon', 'music-2', 'newspaper', 'orbit', 'panel-left-open', 'pause', 'pen-tool', 'play', 'podcast', 'radio', 'rss', 'search',
  'search-x', 'send', 'sparkles', 'sun', 'tag', 'tv', 'user', 'users', 'x',
];

mkdirSync('resources/assets/icons', { recursive: true });

for (const name of icons) {
  const from = `node_modules/lucide-static/icons/${name}.svg`;

  if (!existsSync(from)) {
    console.error(`Missing Lucide icon: ${name}`);
    process.exitCode = 1;
    continue;
  }
  copyFileSync(from, `resources/assets/icons/${name}.svg`);
}
