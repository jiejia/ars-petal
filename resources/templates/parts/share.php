<?php
/**
 * Share button: copies the page link. Social network share buttons are plugin territory.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;

$url = (string) get_permalink();
?>
<div x-data="share('<?php echo esc_js($url); ?>')">
  <button type="button" @click="copy()" class="btn-gray size-10 rounded-full !bg-transparent" :aria-label="copied ? '<?php echo esc_js(__('Link copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy link', 'ars-petal')); ?>'" aria-label="<?php esc_attr_e('Copy link', 'ars-petal'); ?>" title="<?php esc_attr_e('Copy link', 'ars-petal'); ?>">
    <span x-show="!copied"><?php Icon::render('link'); ?></span>
    <span x-show="copied" x-cloak><?php Icon::render('check'); ?></span>
  </button>
</div>
