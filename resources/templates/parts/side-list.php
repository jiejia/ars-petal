<?php
/**
 * A sidebar card of filter / navigation entries (topics, categories, types, pages, people): each entry has a
 * tile (image, colored icon or letter avatar), a name and a small line; the current one is black.
 * On small screens the entries scroll sideways in one row.
 *
 * @var string $title Card heading.
 * @var array<int,array{label:string,meta?:string,url:string,current?:bool,image?:string,icon?:string,tile?:string,user?:\WP_User}> $items Entries.
 * @var bool $wrap Whether the entries stay in a column on small screens too (e.g. under the main content).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;

if ($items === []) {
    return;
}

$wrap ??= false;
?>
<aside class="panel-sm min-w-0 p-3 lg:p-4" aria-label="<?php echo esc_attr($title); ?>">
  <h2 class="display text-xl <?php echo $wrap ? 'px-1 lg:px-0' : 'hidden lg:block'; ?>"><?php echo esc_html($title); ?></h2>
  <ul class="<?php echo $wrap ? 'mt-3 flex flex-col gap-1.5' : 'flex gap-2 overflow-x-auto lg:mt-3 lg:flex-col lg:gap-1.5 lg:overflow-visible'; ?>">
    <?php foreach ($items as $item) :
        $current = ! empty($item['current']);
        ?>
      <li class="shrink-0">
        <a href="<?php echo esc_url($item['url']); ?>" class="flex items-center gap-3 rounded-2xl p-1.5 pr-5 text-left transition-colors lg:pr-3 <?php echo $current ? 'bg-neutral text-neutral-content' : 'bg-base-200/70 hover:bg-base-200'; ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>>
          <?php if (! empty($item['user'])) : ?>
            <?php Template::render('parts/avatar', ['user' => $item['user'], 'class' => 'size-10 rounded-full', 'text' => 'text-base']); ?>
          <?php elseif (! empty($item['image'])) : ?>
            <img src="<?php echo esc_url($item['image']); ?>" alt="" class="size-10 shrink-0 rounded-xl">
          <?php else : ?>
            <span class="grid size-10 shrink-0 place-items-center rounded-xl <?php echo esc_attr($item['tile'] ?? 'bg-base-100 text-base-content'); ?>"><?php Icon::render($item['icon'] ?? 'layout-grid', 'size-5'); ?></span>
          <?php endif; ?>
          <span class="min-w-0">
            <span class="block truncate text-sm font-semibold"><?php echo esc_html($item['label']); ?></span>
            <?php if (($item['meta'] ?? '') !== '') : ?>
              <span class="block truncate text-xs opacity-60"><?php echo esc_html($item['meta']); ?></span>
            <?php endif; ?>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</aside>
