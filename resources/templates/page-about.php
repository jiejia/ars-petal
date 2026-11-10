<?php
/**
 * "About the show" page template: the pages sidebar; a glow card with the show intro, "start with the latest"
 * and the artwork (the featured image, else the theme's day / night illustration); the show in numbers;
 * the page content (format, FAQ with Details blocks, contact) and the hosts.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Theme;
use Jiejia\ArsPetal\View\Format;

get_header();

while (have_posts()) :
    the_post();
    $latest = Podcast::latest();
    $intro = has_excerpt() ? get_the_excerpt() : (Podcast::setting('description') ?: get_bloginfo('description'));
    $hosts = Podcast::hosts();
    $stats = Podcast::stats();
    $schedule = Podcast::schedule();
    $episodes = (int) $stats['episodes'];
    $plays = (int) $stats['plays'];
    $numbers = array_values(array_filter([
        $episodes > 0 ? ['value' => number_format_i18n($episodes), 'label' => _n('episode', 'episodes', $episodes, 'ars-petal')] : null,
        Podcast::tags() !== [] ? ['value' => number_format_i18n(count(Podcast::tags())), 'label' => _n('topic', 'topics', count(Podcast::tags()), 'ars-petal')] : null,
        $stats['duration'] > 0 ? ['value' => '~' . max(1, (int) round($stats['duration'] / 60)) . '′', 'label' => __('per episode', 'ars-petal')] : null,
        $plays > 0 ? ['value' => Format::compact($plays), 'label' => _n('play', 'plays', $plays, 'ars-petal')] : null,
    ]));
    $sidebar = Template::capture('parts/pages-sidebar');
    $images = Theme::URL . 'resources/assets/images/';
    ?>
<article <?php post_class('mt-2 ' . ($sidebar !== '' ? 'drawer lg:drawer-open lg:items-start lg:gap-8' : 'grid grid-cols-1')); ?>>
  <?php if ($sidebar !== '') : ?>
    <?php Template::render('parts/sidebar-start', ['class' => 'lg:top-[5.5rem]']); ?>
      <?php echo $sidebar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the partial. ?>
    <?php Template::render('parts/sidebar-end'); ?>
  <?php endif; ?>
  <div class="<?php echo $sidebar !== '' ? 'drawer-content' : 'min-w-0'; ?>">
    <section class="wash panel relative overflow-hidden px-6 pb-8 pt-10 sm:px-10 sm:pt-12">
      <div class="grid grid-cols-1 items-center gap-6 md:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]">
        <div class="relative z-10 min-w-0">
          <p class="mute text-sm font-medium"><?php echo esc_html(sprintf(/* translators: %s: site name. */ __('About %s', 'ars-petal'), get_bloginfo('name'))); ?></p>
          <h1 class="display mt-2 text-balance text-4xl sm:text-5xl"><?php the_title(); ?></h1>
          <?php if ($intro !== '') : ?>
            <p class="mute mt-5 max-w-md leading-relaxed"><?php echo esc_html($intro); ?></p>
          <?php endif; ?>
          <?php if ($latest) : ?>
            <div class="mt-7 flex max-w-md flex-col gap-3">
              <?php if ($latest->playable()) : ?>
                <?php Template::render('parts/play-button', ['episode' => $latest, 'class' => 'btn-black h-14 rounded-full text-lg', 'icon' => 'size-5', 'label' => __('Start with the latest', 'ars-petal')]); ?>
              <?php endif; ?>
              <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="inline-flex items-center justify-center gap-1 py-2 text-center font-semibold hover:underline"><?php esc_html_e('Browse all episodes', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
            </div>
          <?php endif; ?>
        </div>
        <?php if (has_post_thumbnail()) : ?>
          <?php the_post_thumbnail('large', ['class' => 'relative mx-auto aspect-square w-full max-w-sm rounded-2xl object-cover md:max-w-none']); ?>
        <?php else : ?>
          <img src="<?php echo esc_url($images . 'hero.svg'); ?>" alt="" class="only-light relative mx-auto aspect-square w-full max-w-sm rounded-2xl object-cover md:max-w-none">
          <img src="<?php echo esc_url($images . 'hero-dark.svg'); ?>" alt="" class="only-dark relative mx-auto aspect-square w-full max-w-sm rounded-2xl object-cover md:max-w-none">
        <?php endif; ?>
      </div>
    </section>

    <?php if (count($numbers) >= 2 || $schedule !== '') : ?>
      <section class="panel mt-4 grid grid-cols-2 divide-x divide-y divide-base-300 overflow-hidden md:grid-cols-4 md:divide-y-0" aria-label="<?php esc_attr_e('The show in numbers', 'ars-petal'); ?>">
        <?php foreach (array_slice($numbers, 0, $schedule !== '' ? 3 : 4) as $number) : ?>
          <div class="px-5 py-7 text-center"><p class="display text-3xl"><?php echo esc_html($number['value']); ?></p><p class="mute mt-1 text-sm"><?php echo esc_html($number['label']); ?></p></div>
        <?php endforeach; ?>
        <?php if ($schedule !== '') : ?>
          <div class="px-5 py-7 text-center"><p class="display truncate text-3xl"><?php Icon::render('calendar', 'mx-auto size-8'); ?></p><p class="mute mt-1 text-sm"><?php echo esc_html($schedule); ?></p></div>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if (trim((string) get_the_content()) !== '') : ?>
      <div class="panel mt-4 p-4 sm:p-8">
        <div class="entry-content"><?php the_content(); ?></div>
      </div>
    <?php endif; ?>

    <?php if ($hosts !== []) : ?>
      <section class="mt-8" id="hosts">
        <h2 class="display px-2 text-2xl"><?php echo esc_html(Podcast::isActive() ? __('Hosts', 'ars-petal') : __('Authors', 'ars-petal')); ?></h2>
        <div class="mt-4 grid grid-cols-1 gap-4">
          <?php foreach ($hosts as $user) : ?>
            <a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>" class="panel group flex flex-col gap-5 p-6 transition-colors hover:bg-base-200 sm:flex-row">
              <?php Template::render('parts/avatar', ['user' => $user, 'class' => 'size-28 rounded-2xl', 'text' => 'text-5xl']); ?>
              <span class="min-w-0">
                <span class="display block text-xl group-hover:underline"><?php echo esc_html($user->display_name); ?></span>
                <?php if ($user->description !== '') : ?>
                  <span class="mute mt-2 block leading-relaxed"><?php echo esc_html($user->description); ?></span>
                <?php endif; ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</article>
    <?php
endwhile;

get_footer();
