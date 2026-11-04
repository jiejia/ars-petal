<?php
/**
 * Post (or page) card: 16:10 image, category badge, reading time, title and excerpt.
 *
 * @var \WP_Post $post
 * @var string $heading Heading tag, h2 or h3.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Format;

$heading ??= 'h3';
$categories = $post->post_type === 'post' ? get_the_category($post->ID) : [];
$url = get_permalink($post);
$minutes = Format::readingMinutes($post);
?>
<article class="group min-w-0">
  <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="zoom block rounded-[1.25rem]">
    <?php Template::render('parts/cover', ['url' => (string) get_the_post_thumbnail_url($post, 'medium_large'), 'alt' => '', 'class' => 'aspect-[16/10] w-full object-cover']); ?>
  </a>
  <p class="mt-4 flex items-center gap-2 text-xs">
    <?php if (is_sticky($post->ID)) : ?>
      <span class="badge badge-sm badge-primary"><?php esc_html_e('Featured', 'ars-petal'); ?></span>
    <?php endif; ?>
    <?php if ($categories !== []) : ?>
      <a href="<?php echo esc_url(get_category_link($categories[0])); ?>" class="badge badge-sm"><?php echo esc_html($categories[0]->name); ?></a>
    <?php endif; ?>
    <span class="mono mute"><?php echo esc_html(sprintf(/* translators: %d: minutes. */ _n('%d min read', '%d min read', $minutes, 'ars-petal'), $minutes)); ?></span>
  </p>
  <<?php echo tag_escape($heading); ?> class="display mt-2 text-lg !font-bold leading-snug"><a href="<?php echo esc_url($url); ?>" class="hover:underline"><?php echo esc_html(get_the_title($post) ?: __('(no title)', 'ars-petal')); ?></a></<?php echo tag_escape($heading); ?>>
  <p class="mute mt-1.5 line-clamp-2 text-sm leading-relaxed"><?php echo esc_html(get_the_excerpt($post)); ?></p>
</article>
