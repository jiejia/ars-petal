<?php
/**
 * Search form (get_search_form()): a gray rounded field with the search button inside.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;

$type = get_query_var('post_type');
$type = is_string($type) ? $type : '';
?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 rounded-2xl bg-base-200 px-4 py-3 lg:mt-3">
  <label for="search-q" class="sr-only"><?php esc_html_e('Search', 'ars-petal'); ?></label>
  <input id="search-q" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr(Podcast::isActive() ? __('Search episodes and posts', 'ars-petal') : __('Search posts', 'ars-petal')); ?>" class="w-full min-w-0 border-0 bg-transparent text-sm outline-none">
  <?php if ($type !== '') : ?>
    <input type="hidden" name="post_type" value="<?php echo esc_attr($type); ?>">
  <?php endif; ?>
  <button type="submit" class="shrink-0" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>"><?php Icon::render('search', 'size-5'); ?></button>
</form>
