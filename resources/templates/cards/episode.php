<?php
/**
 * Episode card (home rail, archive grid, related lists): 4:3 cover with a white "▶ 14:12" pill,
 * a topic badge in the topic color, number and date, the title and optionally the excerpt.
 *
 * @var \Jiejia\ArsPetal\Podcast\Episode $episode
 * @var string $heading Heading tag, h2 or h3.
 * @var bool $excerpt Whether to show the excerpt.
 * @var string $class Extra classes, e.g. the rail widths.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\View\Format;
use Jiejia\ArsPetal\View\Topics;

$heading ??= 'h3';
$excerpt ??= true;
$class ??= '';
$tag = $episode->tag();
$url = get_permalink($episode->post);
$duration = $episode->duration() > 0 ? Format::duration($episode->duration()) : '';
?>
<article class="petal-item group min-w-0 <?php echo esc_attr($class); ?>">
  <div class="zoom relative rounded-[1.25rem]">
    <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="block">
      <?php Template::render('parts/cover', ['url' => $episode->cover(), 'alt' => '', 'class' => 'aspect-[4/3] w-full object-cover']); ?>
    </a>
    <?php if ($episode->playable() || $duration !== '') : ?>
      <span class="pill absolute bottom-3 left-3<?php echo $episode->playable() ? '' : ' !pl-3'; ?>">
        <?php Template::render('parts/play-button', ['episode' => $episode, 'class' => 'btn-black size-8 shrink-0 rounded-full', 'icon' => 'size-4']); ?>
        <span class="petal-when-idle"><?php echo esc_html($duration ?: __('Listen', 'ars-petal')); ?></span>
        <span class="petal-when-playing"><?php esc_html_e('Now playing', 'ars-petal'); ?></span>
      </span>
    <?php endif; ?>
    <?php // Playing indicator, bottom right of the cover (see .petal-eq-cover in theme.css). ?>
    <span class="petal-eq-cover absolute bottom-3 right-3 place-items-center drop-shadow-md" aria-hidden="true"><span class="eq"><i></i><i></i><i></i></span></span>
  </div>
  <p class="mt-4 flex items-center gap-2 text-xs">
    <?php if ($tag) : ?>
      <a href="<?php echo esc_url(Podcast::tagUrl($tag)); ?>" class="badge badge-soft badge-sm <?php echo esc_attr(Topics::badge($tag)); ?>"><?php echo esc_html($tag->name); ?></a>
    <?php endif; ?>
    <span class="mono mute"><?php echo esc_html(sprintf('EP.%s · %s', $episode->number(), Format::date($episode->post))); ?></span>
  </p>
  <<?php echo tag_escape($heading); ?> class="display mt-2 line-clamp-2 text-lg !font-bold leading-snug"><a href="<?php echo esc_url($url); ?>" class="hover:underline"><?php echo esc_html(get_the_title($episode->post)); ?></a></<?php echo tag_escape($heading); ?>>
  <?php if ($excerpt) : ?>
    <p class="mute mt-1.5 line-clamp-2 text-sm leading-relaxed"><?php echo esc_html(get_the_excerpt($episode->post)); ?></p>
  <?php endif; ?>
</article>
