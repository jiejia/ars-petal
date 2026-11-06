<?php
/**
 * The title bar above a list: a card with the page title, a count, a sort dropdown (links) and the
 * list / grid toggle (listView in app.js).
 *
 * @var string $title Title.
 * @var string $count Count text, e.g. "12 episodes".
 * @var array<int,array{label:string,url:string,current:bool}> $sorts Sort links.
 * @var bool $views Whether to show the list / grid toggle.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;

$count ??= '';
$sorts ??= [];
$views ??= true;
$active = array_values(array_filter($sorts, static fn (array $sort): bool => $sort['current']))[0] ?? null;
?>
<?php // Small screens: a title card (centered), and below it, outside the card, the sort (left) and the view toggle (right); from sm up, all in one card (see .petal-title-bar in theme.css). ?>
<div class="petal-title-bar">
  <div class="petal-title-bar-head">
    <h1 class="display min-w-0 truncate px-1 text-center text-lg sm:px-3 sm:text-left sm:text-xl"><?php echo esc_html($title); ?></h1>
    <?php if ($count !== '') : ?>
      <span class="mute px-1 text-sm"><?php echo esc_html($count); ?></span>
    <?php endif; ?>
  </div>
  <div class="petal-title-bar-tools">
  <?php if ($sorts !== []) : ?>
    <div class="dropdown">
      <div tabindex="0" role="button" class="flex h-9 cursor-pointer items-center gap-1.5 rounded-full bg-base-200 px-3.5 text-sm font-medium transition-colors hover:bg-base-300" aria-haspopup="menu" aria-label="<?php esc_attr_e('Sort', 'ars-petal'); ?>"><?php echo esc_html($active['label'] ?? $sorts[0]['label']); ?><?php Icon::render('chevron-down', 'size-3.5'); ?></div>
      <ul tabindex="0" class="dropdown-content menu z-30 mt-2 w-36 rounded-2xl border border-base-300 bg-base-100 p-2 shadow-lg">
        <?php foreach ($sorts as $sort) : ?>
          <li><a href="<?php echo esc_url($sort['url']); ?>"<?php echo $sort['current'] ? ' class="menu-active" aria-current="true"' : ''; ?>><?php echo esc_html($sort['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php if ($views) : ?>
    <div class="ml-auto flex rounded-full bg-base-200 p-0.5" role="group" aria-label="<?php esc_attr_e('View', 'ars-petal'); ?>">
      <button type="button" class="grid size-8 place-items-center rounded-full" :class="view === 'list' && 'bg-primary text-primary-content'" @click="setView('list')" :aria-pressed="view === 'list'" aria-label="<?php esc_attr_e('List view', 'ars-petal'); ?>"><?php Icon::render('list', 'size-4'); ?></button>
      <button type="button" class="grid size-8 place-items-center rounded-full" :class="view === 'grid' && 'bg-primary text-primary-content'" @click="setView('grid')" :aria-pressed="view === 'grid'" aria-label="<?php esc_attr_e('Grid view', 'ars-petal'); ?>"><?php Icon::render('layout-grid', 'size-4'); ?></button>
    </div>
  <?php endif; ?>
  </div>
</div>
