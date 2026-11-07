<?php
/**
 * Home widget: episodes as a horizontally scrolling rail of cards, with ‹ › buttons. With $loop, the buttons wrap around at
 * either end; without it they stop there.
 *
 * @var array<int,\Jiejia\ArsPetal\Podcast\Episode> $episodes Episodes.
 * @var string $id Heading ID, unique per widget.
 * @var string $strong Bold part of the heading.
 * @var string $light Light part of the heading.
 * @var bool $loop Whether the buttons wrap around.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;

if ($episodes === []) {
    return;
}

$loop ??= true;
?>
<section x-data="rail(<?php echo $loop ? 'true' : 'false'; ?>)" aria-labelledby="<?php echo esc_attr($id); ?>">
  <div class="flex items-end justify-between gap-3">
    <div class="flex min-w-0 flex-wrap items-center gap-x-4 gap-y-2">
      <?php Template::render('front/heading', ['id' => $id, 'strong' => $strong, 'light' => $light]); ?>
    </div>
    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
      <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="btn-white h-8 shrink-0 gap-1 whitespace-nowrap rounded-full px-3 text-xs sm:h-10 sm:gap-2 sm:px-4 sm:text-sm sm:mr-1" aria-label="<?php esc_attr_e('All episodes', 'ars-petal'); ?>"><?php esc_html_e('See all', 'ars-petal'); ?></a>
      <?php if (count($episodes) > 3) : ?>
        <button type="button" class="edge-arrow" @click="by(-1)" :disabled="!loop && atStart" aria-label="<?php esc_attr_e('Scroll left', 'ars-petal'); ?>"><?php Icon::render('arrow-left', 'size-4'); ?></button>
        <button type="button" class="edge-arrow" @click="by(1)" :disabled="!loop && atEnd" aria-label="<?php esc_attr_e('Scroll right', 'ars-petal'); ?>"><?php Icon::render('arrow-right', 'size-4'); ?></button>
      <?php endif; ?>
    </div>
  </div>
  <div x-ref="rail" class="rail mt-6" @scroll.passive="update()">
    <?php foreach ($episodes as $episode) : ?>
      <?php Template::render('cards/episode', ['episode' => $episode, 'class' => 'w-[80%] shrink-0 sm:w-[calc((100%-1.5rem)/2)] lg:w-[calc((100%-3rem)/3)]']); ?>
    <?php endforeach; ?>
  </div>
</section>
