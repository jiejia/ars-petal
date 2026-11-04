<?php
/**
 * Page: the site's pages in the left sidebar; a title bar and the content card on the right.
 * With fewer than two pages there is no sidebar.
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
<article <?php post_class('mt-2 grid grid-cols-1 gap-6 lg:items-start lg:gap-8' . ($sidebar !== '' ? ' lg:grid-cols-[18rem_minmax(0,1fr)]' : '')); ?>>
  <?php if ($sidebar !== '') : ?>
    <div class="min-w-0 lg:sticky lg:top-[5.5rem]"><?php echo $sidebar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the partial. ?></div>
  <?php endif; ?>
  <div class="min-w-0">
    <div class="panel-sm mb-4 flex min-h-[4.5rem] items-center px-6">
      <h1 class="display min-w-0 text-lg sm:text-xl"><?php the_title(); ?></h1>
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
