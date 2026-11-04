<?php
/**
 * Empty list message in a card.
 *
 * @var string $message Message.
 * @var string $link Optional "start over" URL.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

$link ??= '';
?>
<div class="panel mx-auto mt-10 max-w-xl px-6 py-14 text-center">
  <p class="display text-2xl"><?php echo esc_html($message); ?></p>
  <?php if ($link !== '') : ?>
    <a href="<?php echo esc_url($link); ?>" class="btn-black mt-6 h-12 rounded-full px-7 text-sm"><?php esc_html_e('Clear filters', 'ars-petal'); ?></a>
  <?php endif; ?>
</div>
