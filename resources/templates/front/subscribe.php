<?php
/**
 * Home: the subscribe bar on a glow background: title and text on the left, the feed address with a copy
 * button on the right.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;

$feed = Podcast::feedUrl();
$schedule = Podcast::schedule();
/* translators: %s: release schedule, e.g. "Every Thu 21:00". */
$title = $schedule !== '' ? sprintf(__('%s, right on time', 'ars-petal'), $schedule) : __('Subscribe to the feed', 'ars-petal');
?>
<section class="wash relative mt-16 flex flex-col gap-8 overflow-hidden rounded-[2.5rem] px-6 py-14 sm:px-12 sm:py-16 lg:flex-row lg:items-center lg:justify-between lg:gap-16 lg:px-16 lg:py-24">
  <div class="min-w-0 lg:flex-1">
    <h2 class="display text-2xl min-[420px]:text-3xl !font-black leading-tight sm:text-4xl"><?php echo esc_html($title); ?></h2>
    <p class="mute mt-4 max-w-xl text-lg"><?php esc_html_e('Add the feed to your podcast app and every new episode arrives by itself.', 'ars-petal'); ?></p>
  </div>
  <div class="flex w-full flex-col gap-3 sm:flex-row lg:w-[30rem] lg:shrink-0" x-data="copyText('<?php echo esc_js($feed); ?>')">
    <label for="feed-url" class="sr-only"><?php esc_html_e('RSS feed', 'ars-petal'); ?></label>
    <input id="feed-url" type="text" readonly value="<?php echo esc_attr($feed); ?>" class="field mono h-16 min-w-0 flex-1 !rounded-full px-7 text-sm" @focus="$el.select()">
    <button type="button" class="btn-black h-16 shrink-0 cursor-pointer rounded-full px-10 text-base" @click="copy()">
      <span x-show="!copied"><?php Icon::render('copy', 'size-4'); ?></span><span x-show="copied" x-cloak><?php Icon::render('check', 'size-4'); ?></span>
      <span x-text="copied ? '<?php echo esc_js(__('Copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy', 'ars-petal')); ?>'"><?php esc_html_e('Copy', 'ars-petal'); ?></span>
    </button>
  </div>
</section>
