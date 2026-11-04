<?php
/**
 * Page links of the main query: round buttons, the current page black.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\View\Archive;

$items = Archive::pagination();

if ($items === []) {
    return;
}
?>
<nav class="mt-12 flex flex-wrap items-center justify-center gap-2" aria-label="<?php esc_attr_e('Pagination', 'ars-petal'); ?>">
  <?php foreach ($items as $item) : ?>
    <?php if ($item['icon'] !== '' && $item['url'] !== '') : ?>
      <a href="<?php echo esc_url($item['url']); ?>" class="btn-white size-11 rounded-full" aria-label="<?php echo esc_attr($item['label']); ?>"><?php Icon::render($item['icon'], 'size-5'); ?></a>
    <?php elseif ($item['icon'] !== '') : ?>
      <span class="btn-white size-11 rounded-full opacity-40" aria-hidden="true"><?php Icon::render($item['icon'], 'size-5'); ?></span>
    <?php elseif ($item['current']) : ?>
      <span class="btn-black size-11 rounded-full text-sm" aria-current="page"><?php echo esc_html($item['label']); ?></span>
    <?php elseif ($item['url'] !== '') : ?>
      <a href="<?php echo esc_url($item['url']); ?>" class="btn-white size-11 rounded-full text-sm"><?php echo esc_html($item['label']); ?></a>
    <?php else : ?>
      <span class="mute grid size-11 place-items-center text-sm"><?php echo esc_html($item['label']); ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
