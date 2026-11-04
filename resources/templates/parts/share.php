<?php
/**
 * Share menu: copy link, Weibo, X.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;

$url = (string) get_permalink();
$title = wp_strip_all_tags(get_the_title());
?>
<div x-data="share('<?php echo esc_js($url); ?>')" class="dropdown">
  <div tabindex="0" role="button" class="btn-gray size-10 rounded-full !bg-transparent" aria-label="<?php esc_attr_e('Share', 'ars-petal'); ?>"><?php Icon::render('share-2'); ?></div>
  <ul tabindex="0" class="dropdown-content menu z-30 mt-2 w-48 rounded-2xl border border-base-300 bg-base-100 p-2 shadow-lg">
    <li>
      <button type="button" @click="copy()">
        <span x-show="!copied"><?php Icon::render('link', 'size-4'); ?></span>
        <span x-show="copied" x-cloak><?php Icon::render('check', 'size-4'); ?></span>
        <span x-text="copied ? '<?php echo esc_js(__('Link copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy link', 'ars-petal')); ?>'"><?php esc_html_e('Copy link', 'ars-petal'); ?></span>
      </button>
    </li>
    <li><a href="<?php echo esc_url(add_query_arg(['url' => rawurlencode($url), 'title' => rawurlencode($title)], 'https://service.weibo.com/share/share.php')); ?>" target="_blank" rel="noopener" data-no-swup><?php Icon::render('message-circle', 'size-4'); ?><?php esc_html_e('Share on Weibo', 'ars-petal'); ?></a></li>
    <li><a href="<?php echo esc_url(add_query_arg(['url' => rawurlencode($url), 'text' => rawurlencode($title)], 'https://x.com/intent/post')); ?>" target="_blank" rel="noopener" data-no-swup><?php Icon::render('at-sign', 'size-4'); ?><?php esc_html_e('Share on X', 'ars-petal'); ?></a></li>
  </ul>
</div>
