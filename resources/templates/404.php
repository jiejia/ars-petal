<?php
/**
 * Not found: a glow card with a huge 404, the search form and the way home.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;

TemplateLoader::part('header');
?>
<section class="wash relative mt-2 overflow-hidden rounded-[2rem] px-6 py-20 text-center sm:py-24">
  <p class="big-num text-8xl sm:text-9xl">404</p>
  <h1 class="mt-6 text-4xl sm:text-5xl"><span class="display !font-black"><?php esc_html_e('Page not found', 'ars-petal'); ?></span></h1>
  <p class="display-light mute mx-auto mt-4 max-w-md text-xl"><?php esc_html_e('This page does not exist or has moved. Try a search, or start from the home page.', 'ars-petal'); ?></p>
  <div class="mx-auto mt-8 max-w-sm text-left"><?php get_search_form(); ?></div>
  <div class="mt-6 flex flex-wrap justify-center gap-3">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-black h-12 rounded-full px-6"><?php esc_html_e('Home page', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
    <?php if (Podcast::isActive()) : ?>
      <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="btn-white h-12 rounded-full px-6"><?php Icon::render('audio-lines', 'size-4'); ?><?php esc_html_e('All episodes', 'ars-petal'); ?></a>
    <?php endif; ?>
  </div>
</section>
<?php
TemplateLoader::part('footer');
