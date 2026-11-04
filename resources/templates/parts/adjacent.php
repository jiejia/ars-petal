<?php
/**
 * Previous / next item of the same post type, as cards with a cover; a missing one is a dashed placeholder.
 *
 * @var string $newest Text when there is no newer item.
 * @var string $oldest Text when there is no older item.
 * @var bool $number Whether the items are episodes (numbers, "Previous episode").
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Covers;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;

$number ??= false;
$older = get_previous_post();
$newer = get_next_post();
$sides = [
    ['post' => $older, 'rel' => 'prev', 'label' => $number ? __('Previous episode', 'ars-petal') : __('Previous post', 'ars-petal'), 'empty' => $oldest, 'icon' => 'arrow-left'],
    ['post' => $newer, 'rel' => 'next', 'label' => $number ? __('Next episode', 'ars-petal') : __('Next post', 'ars-petal'), 'empty' => $newest, 'icon' => 'arrow-right'],
];
?>
<nav class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2" aria-label="<?php esc_attr_e('More', 'ars-petal'); ?>">
  <?php foreach ($sides as $side) :
      $post = $side['post'];
      ?>
    <?php if ($post instanceof \WP_Post) :
        $episode = Episode::from($post);
        $cover = $episode ? $episode->cover('thumbnail') : Covers::post($post, 'thumbnail');
        ?>
      <a href="<?php echo esc_url(get_permalink($post)); ?>" rel="<?php echo esc_attr($side['rel']); ?>" class="panel-sm group flex items-center gap-4 p-4 transition-colors hover:bg-base-200">
        <?php Template::render('parts/cover', ['url' => $cover, 'alt' => '', 'class' => ($episode ? 'size-16' : 'aspect-[4/3] w-20') . ' shrink-0 rounded-2xl object-cover']); ?>
        <span class="min-w-0 flex-1">
          <span class="mute flex items-center gap-1 text-xs"><?php Icon::render($side['icon'], 'size-3.5'); ?><?php echo esc_html($side['label']); ?><?php if ($episode) : ?><span class="mx-1 opacity-40">/</span>EP.<?php echo (int) $episode->number(); ?><?php endif; ?></span>
          <span class="mt-1 line-clamp-2 font-semibold leading-snug group-hover:underline"><?php echo esc_html(wp_strip_all_tags(get_the_title($post))); ?></span>
        </span>
      </a>
    <?php else : ?>
      <div class="flex items-center gap-4 rounded-xl border-2 border-dashed border-base-content/20 p-4 text-base-content/40" aria-disabled="true">
        <span class="grid size-16 shrink-0 place-items-center rounded-2xl border-2 border-dashed border-base-content/20"><?php Icon::render($side['icon'], 'size-5'); ?></span>
        <span class="min-w-0 flex-1"><span class="flex items-center gap-1 text-xs"><?php echo esc_html($side['label']); ?></span><span class="mt-1 block font-semibold leading-snug"><?php echo esc_html($side['empty']); ?></span></span>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
