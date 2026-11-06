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
<section class="wash relative mt-16 flex flex-col gap-8 overflow-hidden rounded-[2.5rem] px-6 py-14 sm:px-12 sm:py-16 lg:flex-row lg:items-center lg:justify-between lg:gap-16 lg:px-16 lg:py-24">
  <div class="min-w-0 lg:flex-1">
    <h2 class="display text-2xl min-[420px]:text-3xl !font-black leading-tight sm:text-4xl"><?php echo esc_html($title !== '' ? $title : ($action !== '' ? __('Don’t want to miss one?', 'ars-petal') : __('Subscribe to the feed', 'ars-petal'))); ?></h2>
    <p class="mute mt-4 max-w-xl text-lg"><?php echo esc_html($text); ?></p>
  </div>
  <?php if ($action !== '') : ?>
    <form action="<?php echo esc_url($action); ?>" method="post" target="_blank" class="flex w-full flex-col gap-3 sm:flex-row lg:w-[30rem] lg:shrink-0">
      <label for="newsletter-email" class="sr-only"><?php esc_html_e('E-mail address', 'ars-petal'); ?></label>
      <input id="newsletter-email" name="<?php echo esc_attr($field); ?>" type="email" required placeholder="you@example.com" class="field h-16 min-w-0 flex-1 !rounded-full px-7">
      <button type="submit" class="btn-black h-16 shrink-0 cursor-pointer rounded-full px-10 text-base"><?php esc_html_e('Subscribe', 'ars-petal'); ?></button>
    </form>
  <?php else : ?>
    <div class="flex w-full flex-col gap-3 sm:flex-row lg:w-[30rem] lg:shrink-0" x-data="copyText('<?php echo esc_js($feed); ?>')">
      <label for="feed-url" class="sr-only"><?php esc_html_e('RSS feed', 'ars-petal'); ?></label>
      <input id="feed-url" type="text" readonly value="<?php echo esc_attr($feed); ?>" class="field mono h-16 min-w-0 flex-1 !rounded-full px-7 text-sm" @focus="$el.select()">
      <button type="button" class="btn-black h-16 shrink-0 cursor-pointer rounded-full px-10 text-base" @click="copy()">
        <span x-show="!copied"><?php Icon::render('copy', 'size-4'); ?></span><span x-show="copied" x-cloak><?php Icon::render('check', 'size-4'); ?></span>
        <span x-text="copied ? '<?php echo esc_js(__('Copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy', 'ars-petal')); ?>'"><?php esc_html_e('Copy', 'ars-petal'); ?></span>
      </button>
    </div>
  <?php endif; ?>
</section>
