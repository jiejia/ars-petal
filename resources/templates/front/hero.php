<?php
/**
 * Home: the featured carousel on a soft glow block. Episodes get a play button with their duration;
 * posts a read button. Slides 3.7:1 on large screens, as in the prototype.
 *
 * @var array<int,\Jiejia\ArsPetal\Podcast\Episode|\WP_Post|array{image:string,url:string,alt:string}> $items Featured / latest episodes or
 *     posts, or custom banners (an image, its link and alt text).
 * @var bool $featured Whether $items are the featured ones; the badge then says "Featured", else "Latest".
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Covers;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;
use Jiejia\ArsPetal\View\Format;

if ($items === []) {
    return;
}

$featured ??= false;
$badge = $featured ? _x('Featured', 'home carousel badge', 'ars-petal') : _x('Latest', 'home carousel badge', 'ars-petal');

// Full class names, so Tailwind finds them.
$colors = [
    ['badge' => 'badge-secondary', 'line' => 'hover:decoration-secondary'],
    ['badge' => 'badge-primary', 'line' => 'hover:decoration-primary'],
    ['badge' => 'badge-accent', 'line' => 'hover:decoration-accent'],
];
$count = count($items);
?>
<section class="wash relative overflow-hidden rounded-[2rem] lg:aspect-[3.7/1]" x-data="heroCarousel" aria-roledescription="carousel" aria-label="<?php esc_attr_e('Featured', 'ars-petal'); ?>" @mouseenter="pause()" @mouseleave="play()" @focusin="pause()" @focusout="play()">
  <div x-ref="track" class="carousel h-full w-full" @scroll.passive="sync()" @touchstart.passive="pause(); count > 1 && swipe.start($event)" @touchend.passive="count > 1 && swipe.end($event); play()">
    <?php foreach ($items as $index => $item) :
        if (is_array($item)) : ?>
      <article class="petal-item carousel-item w-full lg:h-full" :inert="active !== <?php echo (int) $index; ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr(sprintf(/* translators: 1: slide number, 2: number of slides. */ __('Slide %1$d of %2$d', 'ars-petal'), $index + 1, $count)); ?>">
        <?php $image = '<img src="' . esc_url($item['image']) . '" alt="' . esc_attr($item['alt']) . '"' . ($index > 0 ? ' loading="lazy"' : '') . ' class="aspect-[2/1] h-full w-full object-cover sm:aspect-[3/1] lg:aspect-auto">'; ?>
        <?php if ($item['url'] !== '') : ?>
          <a href="<?php echo esc_url($item['url']); ?>" class="block h-full w-full"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?></a>
        <?php else : ?>
          <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>
        <?php endif; ?>
      </article>
        <?php continue;
        endif;
        $episode = $item instanceof Episode ? $item : null;
        $post = $episode ? $episode->post : $item;
        $url = (string) get_permalink($post);
        $color = $colors[$index % count($colors)];
        $tag = $episode ? $episode->tag() : (get_the_category($post->ID)[0] ?? null);
        $meta = $episode
            ? implode(' · ', array_filter(['EP.' . $episode->number(), $tag ? $tag->name : '', $episode->duration() > 0 ? Format::duration($episode->duration()) : '']))
            : implode(' · ', array_filter([Format::date($post), $tag ? $tag->name : '']));
        $cover = $episode ? $episode->cover('medium_large') : Covers::post($post);
        $heading = $index === 0 ? 'h1' : 'h2';
        ?>
      <article class="petal-item carousel-item w-full lg:h-full" :inert="active !== <?php echo (int) $index; ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr(sprintf(/* translators: 1: slide number, 2: number of slides. */ __('Slide %1$d of %2$d', 'ars-petal'), $index + 1, $count)); ?>">
        <div class="flex w-full items-center justify-between gap-8 px-6 pb-12 pt-8 sm:px-10 lg:pb-8">
          <div class="relative z-10 min-w-0 flex-1 text-center sm:text-left">
            <p class="flex flex-wrap items-center justify-center gap-2 text-sm sm:justify-start">
              <span class="badge <?php echo esc_attr($color['badge']); ?> font-medium"><?php echo esc_html($badge); ?></span>
              <span class="mono mute"><?php echo esc_html($meta); ?></span>
            </p>
            <<?php echo tag_escape($heading); ?> class="display mt-3 line-clamp-2 text-balance text-3xl sm:text-4xl"><a href="<?php echo esc_url($url); ?>" class="hover:underline hover:decoration-4 hover:underline-offset-8 <?php echo esc_attr($color['line']); ?>"><?php echo esc_html(get_the_title($post)); ?></a></<?php echo tag_escape($heading); ?>>
            <p class="display-light mute mx-auto mt-3 line-clamp-2 max-w-lg text-lg leading-snug sm:mx-0"><?php echo esc_html(get_the_excerpt($post)); ?></p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 sm:justify-start lg:mt-5">
              <?php if ($episode && $episode->playable()) : ?>
                <?php
                Template::render('parts/play-button', [
                    'episode' => $episode,
                    // Fixed width so swapping Play / Pause does not move the button next to it.
                    'class' => 'btn-black h-12 w-32 justify-center rounded-full px-0 text-[0.95rem]',
                    'icon' => 'size-4',
                    'label' => __('Play', 'ars-petal'),
                ]);
                ?>
                <a href="<?php echo esc_url($url); ?>" class="btn-white h-12 rounded-full px-6 text-[0.95rem]"><?php esc_html_e('Show notes', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
              <?php else : ?>
                <a href="<?php echo esc_url($url); ?>" class="btn-black h-12 rounded-full px-6 text-[0.95rem]"><?php echo esc_html($episode ? __('Show notes', 'ars-petal') : __('Read more', 'ars-petal')); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
              <?php endif; ?>
            </div>
          </div>
          <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="hidden shrink-0 rotate-2 rounded-2xl bg-base-100 p-1.5 shadow-xl shadow-base-content/10 transition hover:rotate-0 sm:block">
            <?php Template::render('parts/cover', ['url' => $cover, 'alt' => '', 'class' => 'aspect-[4/3] w-56 rounded-xl object-cover md:w-64 lg:w-72', 'lazy' => $index > 0]); ?>
          </a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php if ($count > 1) : ?>
    <div class="absolute inset-x-0 bottom-4 z-10 flex justify-center gap-1.5 lg:bottom-5" role="group" aria-label="<?php esc_attr_e('Choose a slide', 'ars-petal'); ?>">
      <?php for ($i = 0; $i < $count; $i++) : ?>
        <button type="button" class="h-2 rounded-full transition-all duration-300" :class="active === <?php echo (int) $i; ?> ? 'w-7 bg-primary' : 'w-2 bg-base-content/20 hover:bg-base-content/40'" :aria-current="active === <?php echo (int) $i; ?>" @click="go(<?php echo (int) $i; ?>)" aria-label="<?php echo esc_attr(sprintf(/* translators: %d: slide number. */ __('Slide %d', 'ars-petal'), $i + 1)); ?>"></button>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</section>
