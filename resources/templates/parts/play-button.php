<?php
/**
 * A play / pause button for the plugin's player ([data-ars-play]). Prints nothing when the episode
 * has no audio or the player is off on this page.
 *
 * @var \Jiejia\ArsPetal\Podcast\Episode $episode
 * @var string $class Button classes.
 * @var string $icon Icon classes.
 * @var bool|string $label Whether to show a text label next to the icon: true for Play / Resume, or a fixed text.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\EpisodeRegistry;

if (! $episode->playable()) {
    return;
}

EpisodeRegistry::add($episode);
$icon ??= 'size-5';
$label ??= false;
/* translators: 1: episode number, 2: episode title. */
$name = sprintf(__('Play EP.%1$s %2$s', 'ars-petal'), $episode->number(), wp_strip_all_tags(get_the_title($episode->post)));
?>
<button type="button" class="<?php echo esc_attr($class); ?>" data-ars-play="<?php echo (int) $episode->id(); ?>" data-ars-state="idle" aria-pressed="false" aria-label="<?php echo esc_attr($name); ?>">
  <span class="petal-when-idle"><?php Icon::render('play', $icon . ' ml-0.5 fill-current'); ?></span>
  <span class="petal-when-playing"><?php Icon::render('pause', $icon . ' fill-current'); ?></span>
  <?php if (is_string($label) && $label !== '') : ?>
    <span class="petal-when-idle"><?php echo esc_html($label); ?></span>
    <span class="petal-when-playing"><?php esc_html_e('Pause', 'ars-petal'); ?></span>
  <?php elseif ($label) : ?>
    <?php // "Resume" only once this episode has progress; the player also loads the latest episode paused at 0. ?>
    <span class="petal-when-idle" x-data x-text="$store.player.positionOf(<?php echo (int) $episode->id(); ?>) > 1 ? '<?php echo esc_js(__('Resume', 'ars-petal')); ?>' : '<?php echo esc_js(__('Play episode', 'ars-petal')); ?>'"><?php esc_html_e('Play episode', 'ars-petal'); ?></span>
    <span class="petal-when-playing"><?php esc_html_e('Pause', 'ars-petal'); ?></span>
  <?php endif; ?>
</button>
