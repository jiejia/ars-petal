<?php
/**
 * Blog post (and any other single item without its own template). Left sidebar: latest posts and the
 * categories. Main column: the post card (image, meta, title, excerpt, author, share), the content card
 * with the tags, previous / next and the comments.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Covers;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\View\Format;
use Jiejia\ArsPetal\View\Topics;

TemplateLoader::part('header');

while (have_posts()) :
    the_post();
    $categories = get_the_category();
    $minutes = Format::readingMinutes();
    $author = get_userdata((int) get_the_author_meta('ID'));
    $isPost = get_post_type() === 'post';
    $image = Covers::post(get_post());
    $latest = $isPost ? get_posts(['numberposts' => 3, 'post__not_in' => [get_the_ID()]]) : [];
    $topics = $isPost ? array_map(static fn (\WP_Term $term): array => [
        'label' => $term->name,
        /* translators: %s: number of posts. */
        'meta' => sprintf(_n('%s post', '%s posts', (int) $term->count, 'ars-petal'), number_format_i18n((int) $term->count)),
        'url' => (string) get_category_link($term),
        'current' => in_array($term->term_id, wp_list_pluck($categories, 'term_id'), true),
        'icon' => Topics::icon($term),
        'tile' => Topics::block($term),
    ], get_categories(['orderby' => 'count', 'order' => 'DESC', 'number' => 6])) : [];
    $separator = '<span class="mx-1 opacity-40">/</span>';
    ?>
<article <?php post_class(); ?>>
  <div class="mt-2 grid grid-cols-1 items-start gap-6 <?php echo $isPost ? 'lg:grid-cols-[18rem_minmax(0,1fr)] lg:gap-8' : ''; ?>">
    <?php if ($isPost) : ?>
      <div class="order-2 min-w-0 space-y-3 lg:sticky lg:top-24 lg:order-1">
        <?php if ($latest !== []) : ?>
          <aside class="panel-sm p-4 lg:p-5" aria-label="<?php esc_attr_e('Latest posts', 'ars-petal'); ?>">
            <h2 class="display text-xl"><?php esc_html_e('Latest posts', 'ars-petal'); ?></h2>
            <ul class="mt-4 space-y-2">
              <?php foreach ($latest as $item) : ?>
                <?php Template::render('cards/post-row', ['post' => $item, 'compact' => true]); ?>
              <?php endforeach; ?>
            </ul>
          </aside>
        <?php endif; ?>
        <?php Template::render('parts/side-list', ['title' => __('Categories', 'ars-petal'), 'items' => $topics, 'wrap' => true]); ?>
      </div>
    <?php endif; ?>

    <div class="order-1 min-w-0 lg:order-2">
      <header class="panel p-4 sm:p-5">
        <div class="flex flex-col gap-5 sm:flex-row sm:gap-6">
          <?php if ($image !== '') : ?>
            <?php Template::render('parts/cover', ['url' => $image, 'alt' => '', 'class' => 'aspect-[4/3] w-full shrink-0 self-start rounded-2xl object-cover sm:w-56', 'lazy' => false]); ?>
          <?php endif; ?>
          <div class="flex min-w-0 flex-1 flex-col <?php echo $image !== '' ? '' : 'p-1 sm:p-3'; ?>">
            <p class="mute text-sm">
              <?php if ($categories !== []) : ?>
                <a href="<?php echo esc_url(get_category_link($categories[0])); ?>" class="hover:text-base-content"><?php echo esc_html($categories[0]->name); ?></a><?php echo wp_kses_post($separator); ?>
              <?php endif; ?>
              <?php echo esc_html(Format::date()); ?><?php echo wp_kses_post($separator); ?><?php echo esc_html(sprintf(/* translators: %d: minutes. */ _n('%d min read', '%d min read', $minutes, 'ars-petal'), $minutes)); ?>
            </p>
            <h1 class="display mt-1.5 text-balance text-2xl sm:text-3xl"><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
              <p class="mute mt-2 line-clamp-2 leading-relaxed"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
            <div class="mt-auto flex flex-wrap items-center gap-3 pt-4">
              <?php if ($author) : ?>
                <a href="<?php echo esc_url(get_author_posts_url($author->ID)); ?>" class="group flex items-center gap-3">
                  <?php Template::render('parts/avatar', ['user' => $author, 'class' => 'size-10 rounded-full', 'text' => 'text-base']); ?>
                  <span class="min-w-0">
                    <span class="block text-sm font-semibold leading-tight group-hover:underline"><?php echo esc_html($author->display_name); ?></span>
                    <?php if ($author->description !== '') : ?>
                      <span class="mute block max-w-56 truncate text-xs"><?php echo esc_html($author->description); ?></span>
                    <?php endif; ?>
                  </span>
                </a>
              <?php endif; ?>
              <div class="ml-auto rounded-full bg-base-200 p-1"><?php Template::render('parts/share'); ?></div>
            </div>
          </div>
        </div>
      </header>

      <div class="panel mt-4 p-4 sm:p-8">
        <div class="entry-content">
          <?php the_content(); ?>
          <?php wp_link_pages(); ?>
        </div>
        <?php Template::render('parts/tags', ['terms' => get_the_tags() ?: [], 'inline' => true]); ?>
      </div>

      <?php if ($isPost) : ?>
        <?php Template::render('parts/adjacent', ['newest' => __('This is the newest post', 'ars-petal'), 'oldest' => __('This is the first post', 'ars-petal')]); ?>
      <?php endif; ?>

      <?php if (comments_open() || get_comments_number()) : ?>
        <?php comments_template(); ?>
      <?php endif; ?>
    </div>
  </div>
</article>
    <?php
endwhile;

TemplateLoader::part('footer');
