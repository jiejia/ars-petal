<?php
/**
 * Page: the site's pages in the left sidebar; a title bar and the content card on the right.
 * Always two columns, like the prototype; the sidebar lists the current page even when it is the only one.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Setup\TemplateLoader;

TemplateLoader::part('header');

while (have_posts()) :
    the_post();
    $sidebar = Template::capture('parts/pages-sidebar');
    ?>
<article <?php post_class('mt-2 ' . ($sidebar !== '' ? 'drawer lg:drawer-open lg:items-start lg:gap-8' : 'grid grid-cols-1')); ?>>
  <?php if ($sidebar !== '') : ?>
    <?php Template::render('parts/sidebar-start', ['class' => 'lg:top-[5.5rem]']); ?>
      <?php echo $sidebar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the partial. ?>
    <?php Template::render('parts/sidebar-end'); ?>
  <?php endif; ?>
  <div class="<?php echo $sidebar !== '' ? 'drawer-content' : 'min-w-0'; ?>">
    <div class="panel-sm mb-4 flex min-h-[4.5rem] items-center justify-center px-6 sm:justify-start">
      <h1 class="display min-w-0 text-center text-lg sm:text-left sm:text-xl"><?php echo esc_html(sprintf(/* translators: %s: page title. */ __('Page: %s', 'ars-petal'), wp_strip_all_tags(get_the_title()))); ?></h1>
    </div>
    <?php if (has_post_thumbnail()) : ?>
      <figure class="mb-4"><?php the_post_thumbnail('large', ['class' => 'aspect-[16/7] w-full rounded-[1.5rem] object-cover']); ?></figure>
    <?php endif; ?>
    <div class="panel p-4 sm:p-8">
      <div class="entry-content">
        <?php the_content(); ?>
        <?php wp_link_pages(); ?>
      </div>
    </div>
    <?php if (comments_open() || get_comments_number()) : ?>
      <?php comments_template(); ?>
    <?php endif; ?>
  </div>
</article>
    <?php
endwhile;

TemplateLoader::part('footer');
