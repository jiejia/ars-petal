<?php
/**
 * Search form, loaded by get_search_form() so it can be filtered.
 *
 * Two looks:
 * - The default: a gray rounded field with the search button inside (search page, 404, widgets).
 * - `get_search_form(['petal_variant' => 'modal'])`: the instant search field in the header dialog,
 *   driven by the Alpine component around it.
 *
 * @package ArsPetal
 *
 * @var array<string,mixed> $args Arguments passed to get_search_form().
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;

$variant = isset($args['petal_variant']) && $args['petal_variant'] === 'modal' ? 'modal' : 'default';
$label = isset($args['aria_label']) && is_string($args['aria_label']) && $args['aria_label'] !== '' ? $args['aria_label'] : __('Search', 'ars-petal');
?>
<?php if ($variant === 'modal') : ?>
  <form x-ref="form" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search" aria-label="<?php echo esc_attr($label); ?>" class="flex items-center gap-3 rounded-2xl bg-base-200 px-4 py-3 focus-within:ring-2 focus-within:ring-primary" @submit.prevent="submit()">
    <?php Icon::render('search', 'mute size-5 shrink-0'); ?>
    <label for="site-search" class="sr-only"><?php esc_html_e('Search', 'ars-petal'); ?></label>
    <input id="site-search" name="s" x-ref="input" x-model="q" @input="search()" type="search" autocomplete="off" placeholder="<?php echo esc_attr(Podcast::isActive() ? __('Search episodes and posts…', 'ars-petal') : __('Search posts…', 'ars-petal')); ?>" class="w-full border-0 bg-transparent text-lg outline-none">
    <kbd class="kbd kbd-sm hidden sm:inline-flex">esc</kbd>
  </form>
<?php else : ?>
  <?php
  $type = get_query_var('post_type');
  $type = is_string($type) ? $type : '';
  ?>
  <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($label); ?>" class="flex items-center gap-3 rounded-2xl bg-base-200 px-4 py-3 focus-within:ring-2 focus-within:ring-primary lg:mt-3">
    <label for="search-q" class="sr-only"><?php esc_html_e('Search', 'ars-petal'); ?></label>
    <input id="search-q" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr(Podcast::isActive() ? __('Search episodes and posts', 'ars-petal') : __('Search posts', 'ars-petal')); ?>" class="w-full min-w-0 border-0 bg-transparent text-sm outline-none">
    <?php if ($type !== '') : ?>
      <input type="hidden" name="post_type" value="<?php echo esc_attr($type); ?>">
    <?php endif; ?>
    <button type="submit" class="shrink-0" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>"><?php Icon::render('search', 'size-5'); ?></button>
  </form>
<?php endif; ?>
