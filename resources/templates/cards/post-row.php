<?php
/**
 * Post (or page) row (blog list view, author page, search, sidebars): 4:3 image, category / date /
 * reading time, title and excerpt.
 *
 * @var \WP_Post $post
 * @var string $heading Heading tag.
 * @var bool $badge Whether to start the meta line with a "Blog" / "Page" badge (mixed search results).
 * @var bool $compact Sidebar size: small image, title only.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Covers;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Format;

$heading ??= 'h3';
$badge ??= false;
$compact ??= false;
$url = get_permalink($post);
$categories = $post->post_type === 'post' ? get_the_category($post->ID) : [];
$minutes = Format::readingMinutes($post);
$image = Covers::post($post, $compact ? 'thumbnail' : 'medium');
$title = get_the_title($post) ?: __('(no title)', 'ars-petal');
$meta = array_filter([
    $categories !== [] ? $categories[0]->name : '',
    Format::date($post),
    /* translators: %d: minutes. */
    sprintf(_n('%d min read', '%d min read', $minutes, 'ars-petal'), $minutes),
]);
$separator = '<span class="mx-1 opacity-40">/</span>';
?>
<?php if ($compact) : ?>
<li>
  <a href="<?php echo esc_url($url); ?>" class="flex items-center gap-3 rounded-2xl bg-base-200/70 p-2 transition-colors hover:bg-base-200">
    <?php Template::render('parts/cover', ['url' => $image, 'alt' => '', 'class' => 'aspect-[4/3] w-16 shrink-0 rounded-xl object-cover']); ?>
    <span class="min-w-0 flex-1">
      <span class="line-clamp-2 text-sm font-semibold leading-snug"><?php echo esc_html($title); ?></span>
      <span class="mute mt-0.5 block text-xs"><?php echo esc_html(Format::date($post)); ?></span>
    </span>
  </a>
</li>
<?php else : ?>
<li class="flex items-center gap-4 rounded-2xl p-2.5 transition-colors hover:bg-base-200">
  <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="shrink-0"><?php Template::render('parts/cover', ['url' => $image, 'alt' => '', 'class' => 'aspect-[4/3] w-28 rounded-2xl object-cover sm:w-36']); ?></a>
  <div class="min-w-0 flex-1">
    <p class="mute flex min-w-0 items-center text-xs sm:text-sm">
      <?php if ($badge) : ?>
        <span class="mr-2 shrink-0 rounded-full bg-base-200 px-2 py-0.5 text-[0.7rem] font-semibold text-base-content"><?php echo esc_html($post->post_type === 'page' ? __('Page', 'ars-petal') : __('Blog', 'ars-petal')); ?></span>
      <?php endif; ?>
      <?php if (is_sticky($post->ID)) : ?>
        <span class="badge badge-xs badge-primary mr-2 shrink-0"><?php esc_html_e('Featured', 'ars-petal'); ?></span>
      <?php endif; ?>
      <?php // Each part stays whole and the "/" ends its line, so on phones the line wraps between parts. ?>
      <span class="sm:truncate"><?php
        $last = count($meta) - 1;
        echo wp_kses(implode(' ', array_map(static fn (string $part, int $i): string => '<span class="whitespace-nowrap">' . esc_html($part) . ($i < $last ? $separator : '') . '</span>', array_values($meta), array_keys(array_values($meta)))), ['span' => ['class' => true]]);
        ?></span>
    </p>
    <<?php echo tag_escape($heading); ?> class="mt-0.5 font-semibold leading-snug sm:line-clamp-2"><a href="<?php echo esc_url($url); ?>" class="hover:underline"><?php echo esc_html($title); ?></a></<?php echo tag_escape($heading); ?>>
    <p class="mute mt-1 line-clamp-2 hidden text-sm leading-relaxed sm:block"><?php echo esc_html(get_the_excerpt($post)); ?></p>
    <a href="<?php echo esc_url(Format::readMoreUrl($post)); ?>" class="mt-1.5 hidden items-center gap-1 text-sm font-medium text-primary hover:underline sm:inline-flex"><?php esc_html_e('Read more', 'ars-petal'); ?><span class="screen-reader-text">: <?php echo esc_html($title); ?></span><?php Icon::render('arrow-right', 'size-3.5'); ?></a>
  </div>
</li>
<?php endif; ?>
