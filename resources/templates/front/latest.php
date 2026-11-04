<?php
/**
 * Home: the latest episodes as a horizontally scrolling rail of cards, with ‹ › buttons.
 *
 * @var array<int,\Jiejia\ArsPetal\Podcast\Episode> $episodes Episodes.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;

if ($episodes === []) {
    return;
}

$schedule = Podcast::schedule();
?>
<section class="mt-8" x-data="rail" aria-labelledby="latest-title">
  <div class="flex items-end justify-between gap-4">
    <div class="flex min-w-0 flex-wrap items-center gap-x-4 gap-y-2">
      <?php Template::render('front/heading', ['id' => 'latest-title', 'strong' => _x('Latest', 'home heading, bold part', 'ars-petal'), 'light' => _x('episodes', 'home heading, light part', 'ars-petal')]); ?>
      <?php // The plugin's Release Schedule setting; hidden when none is set. ?>
      <?php if ($schedule !== '') : ?>
        <span class="mono mute inline-flex items-center gap-1.5 rounded-full border border-base-300 bg-base-200 px-3 py-1 text-xs"><?php Icon::render('calendar-clock', 'size-3.5'); ?><?php echo esc_html(sprintf(/* translators: %s: release schedule, e.g. "Every Thu 21:00". */ __('New episodes: %s', 'ars-petal'), $schedule)); ?></span>
      <?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="btn-white mr-1 hidden h-10 rounded-xl px-4 text-sm sm:inline-flex"><?php esc_html_e('All episodes', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
      <?php if (count($episodes) > 3) : ?>
        <button type="button" class="edge-arrow" @click="by(-1)" aria-label="<?php esc_attr_e('Scroll left', 'ars-petal'); ?>"><?php Icon::render('arrow-left', 'size-4'); ?></button>
        <button type="button" class="edge-arrow" @click="by(1)" aria-label="<?php esc_attr_e('Scroll right', 'ars-petal'); ?>"><?php Icon::render('arrow-right', 'size-4'); ?></button>
      <?php endif; ?>
    </div>
  </div>
  <div x-ref="rail" class="rail mt-4">
    <?php foreach ($episodes as $episode) : ?>
      <?php Template::render('cards/episode', ['episode' => $episode, 'class' => 'w-[80%] shrink-0 sm:w-[calc((100%-1.5rem)/2)] lg:w-[calc((100%-3rem)/3)]']); ?>
    <?php endforeach; ?>
  </div>
</section>
