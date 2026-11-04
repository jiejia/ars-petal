<?php
/**
 * Home: the subscribe bar on a glow background: title and text on the left, the e-mail field and button on the
 * right. The form posts to the newsletter service set in the Customizer; without one, the right side shows the
 * feed address with a copy button.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;

$action = Options::get('newsletter_action');
$field = Options::get('newsletter_field') ?: 'email';
$title = Options::get('newsletter_title');
$text = Options::get('newsletter_text') ?: ($action !== ''
    ? __('New episodes and full notes in your inbox. No ads, unsubscribe any time.', 'ars-petal')
    : __('Add the feed to your podcast app and every new episode arrives by itself.', 'ars-petal'));
$feed = Podcast::feedUrl();
$schedule = Podcast::schedule();

// The Customizer title wins; else the plugin's Release Schedule ("每周四 21:00，准时更新"); else a plain title.
if ($title === '' && $schedule !== '') {
    /* translators: %s: release schedule, e.g. "Every Thu 21:00". */
    $title = sprintf(__('%s, right on time', 'ars-petal'), $schedule);
}
?>
<section class="wash relative mt-8 flex flex-col gap-8 overflow-hidden rounded-[2rem] px-6 py-10 sm:px-10 lg:flex-row lg:items-center lg:justify-between lg:gap-12 lg:py-12">
  <div class="min-w-0">
    <h2 class="display text-2xl !font-black sm:text-3xl"><?php echo esc_html($title !== '' ? $title : ($action !== '' ? __('Don’t want to miss one?', 'ars-petal') : __('Subscribe to the feed', 'ars-petal'))); ?></h2>
    <p class="mute mt-2 max-w-xl"><?php echo esc_html($text); ?></p>
  </div>
  <?php if ($action !== '') : ?>
    <form action="<?php echo esc_url($action); ?>" method="post" target="_blank" class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-lg">
      <label for="newsletter-email" class="sr-only"><?php esc_html_e('E-mail address', 'ars-petal'); ?></label>
      <input id="newsletter-email" name="<?php echo esc_attr($field); ?>" type="email" required placeholder="you@example.com" class="field h-14 min-w-0 flex-1 !rounded-full px-6">
      <button type="submit" class="btn-black h-14 shrink-0 cursor-pointer rounded-full px-8"><?php esc_html_e('Subscribe', 'ars-petal'); ?></button>
    </form>
  <?php else : ?>
    <div class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-lg" x-data="copyText('<?php echo esc_js($feed); ?>')">
      <label for="feed-url" class="sr-only"><?php esc_html_e('RSS feed', 'ars-petal'); ?></label>
      <input id="feed-url" type="text" readonly value="<?php echo esc_attr($feed); ?>" class="field mono h-14 min-w-0 flex-1 !rounded-full px-6 text-sm" @focus="$el.select()">
      <button type="button" class="btn-black h-14 shrink-0 cursor-pointer rounded-full px-8" @click="copy()">
        <span x-show="!copied"><?php Icon::render('copy', 'size-4'); ?></span><span x-show="copied" x-cloak><?php Icon::render('check', 'size-4'); ?></span>
        <span x-text="copied ? '<?php echo esc_js(__('Copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy', 'ars-petal')); ?>'"><?php esc_html_e('Copy', 'ars-petal'); ?></span>
      </button>
    </div>
  <?php endif; ?>
</section>
