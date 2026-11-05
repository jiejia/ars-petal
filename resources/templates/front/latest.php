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
?>
<section class="mt-16" x-data="rail" aria-labelledby="latest-title">
  <div class="flex items-end justify-between gap-3">
    <div class="flex min-w-0 flex-wrap items-center gap-x-4 gap-y-2">
      <?php Template::render('front/heading', ['id' => 'latest-title', 'strong' => _x('Latest', 'home heading, bold part', 'ars-petal'), 'light' => _x('episodes', 'home heading, light part', 'ars-petal')]); ?>
    </div>
    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
      <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="btn-white h-8 shrink-0 gap-1 whitespace-nowrap rounded-lg px-2.5 text-xs sm:h-10 sm:gap-2 sm:rounded-xl sm:px-4 sm:text-sm sm:mr-1" aria-label="<?php esc_attr_e('All episodes', 'ars-petal'); ?>"><?php esc_html_e('All', 'ars-petal'); ?></a>
      <?php if (count($episodes) > 3) : ?>
        <button type="button" class="edge-arrow" @click="by(-1)" aria-label="<?php esc_attr_e('Scroll left', 'ars-petal'); ?>"><?php Icon::render('arrow-left', 'size-4'); ?></button>
        <button type="button" class="edge-arrow" @click="by(1)" aria-label="<?php esc_attr_e('Scroll right', 'ars-petal'); ?>"><?php Icon::render('arrow-right', 'size-4'); ?></button>
      <?php endif; ?>
    </div>
  </div>
  <div x-ref="rail" class="rail mt-6">
    <?php foreach ($episodes as $episode) : ?>
      <?php Template::render('cards/episode', ['episode' => $episode, 'class' => 'w-[80%] shrink-0 sm:w-[calc((100%-1.5rem)/2)] lg:w-[calc((100%-3rem)/3)]']); ?>
    <?php endforeach; ?>
  </div>
</section>
