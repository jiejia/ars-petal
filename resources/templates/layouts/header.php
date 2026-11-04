<?php
/**
 * Site header, printed by TemplateLoader::part('header'): <head>, the monospace announcement bar, the
 * frosted sticky top bar (petal mark, centered icon menu with dropdown cards, ⌘K search, color scheme
 * swatch) with the search dialog, and the opening of the swup container. Everything outside
 * <main id="swup"> stays across swup page swaps; the menus are refreshed from the new page ([data-swup-sync]).
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Navigation\Menu;
use Jiejia\ArsPetal\Podcast\Podcast;

$menu = Menu::tree('primary');
$latest = Podcast::latest();
$newest = $latest ? null : (get_posts(['numberposts' => 1])[0] ?? null);
$schedule = Options::get('schedule');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('min-h-screen font-sans text-base-content'); ?>>
<?php wp_body_open(); ?>
<div class="swup-progress" aria-hidden="true"></div>
<a href="#swup" class="btn btn-sm sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70]"><?php esc_html_e('Skip to content', 'ars-petal'); ?></a>

<?php if ($latest || $newest) : ?>
  <div id="petal-announce" data-swup-sync class="announce mono px-4 py-2.5 text-center text-[0.8rem] sm:text-sm">
    <?php if ($schedule !== '') : ?>
      <?php echo esc_html($schedule); ?> ·
    <?php endif; ?>
    <?php if ($latest) : ?>
      <?php esc_html_e('New episode', 'ars-petal'); ?>
      <a href="<?php echo esc_url(get_permalink($latest->post)); ?>" class="font-medium hover:underline"><?php echo esc_html(sprintf('EP.%s %s', $latest->number(), wp_strip_all_tags(get_the_title($latest->post)))); ?></a> →
    <?php else : ?>
      <?php esc_html_e('New post', 'ars-petal'); ?>
      <a href="<?php echo esc_url(get_permalink($newest)); ?>" class="font-medium hover:underline"><?php echo esc_html(wp_strip_all_tags(get_the_title($newest))); ?></a> →
    <?php endif; ?>
  </div>
<?php endif; ?>

<header x-data="siteSearch" @open-search.window="open()" class="topbar sticky top-0 z-40">
  <div class="mx-auto flex h-16 max-w-6xl items-center gap-2 px-4 sm:px-6 lg:h-[4.5rem]">
    <?php if (has_custom_logo()) : ?>
      <?php the_custom_logo(); ?>
    <?php else : ?>
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex min-w-0 shrink-0 items-center gap-2.5" rel="home">
        <span class="mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span class="display truncate text-[1.35rem] !font-bold tracking-tight"><?php bloginfo('name'); ?></span>
      </a>
    <?php endif; ?>
    <?php if ($latest) : ?>
      <a href="<?php echo esc_url(get_permalink($latest->post)); ?>" class="mono mute ml-2 hidden items-center gap-1 rounded-lg px-2 py-1 text-xs hover:bg-base-200 sm:inline-flex" title="<?php esc_attr_e('Latest episode', 'ars-petal'); ?>">EP.<?php echo (int) $latest->number(); ?></a>
    <?php endif; ?>

    <nav id="petal-nav" data-swup-sync class="topnav mx-auto hidden items-center gap-1 lg:flex" aria-label="<?php esc_attr_e('Main menu', 'ars-petal'); ?>">
      <?php Template::render('parts/nav-desktop', ['items' => $menu]); ?>
    </nav>

    <div class="ml-auto flex items-center gap-1 lg:ml-0">
      <button type="button" class="search-box hidden xl:flex" @click="open()" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>">
        <?php Icon::render('search', 'mute size-4'); ?><span><?php esc_html_e('Search…', 'ars-petal'); ?></span><kbd class="kbd kbd-sm">/</kbd>
      </button>
      <button type="button" class="btn-plain size-10 rounded-xl xl:hidden" @click="open()" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>"><?php Icon::render('search', 'size-[1.15rem]'); ?></button>
      <button type="button" class="btn-plain h-10 gap-1.5 rounded-xl px-2" @click="$store.ui.toggleTheme()" :aria-label="$store.ui.dark ? '<?php echo esc_js(__('Switch to day mode', 'ars-petal')); ?>' : '<?php echo esc_js(__('Switch to night mode', 'ars-petal')); ?>'" aria-label="<?php esc_attr_e('Switch color scheme', 'ars-petal'); ?>">
        <span class="swatch" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span x-show="$store.ui.dark"><?php Icon::render('sun', 'size-4'); ?></span>
        <span x-show="!$store.ui.dark" x-cloak><?php Icon::render('moon', 'size-4'); ?></span>
      </button>
      <a href="<?php echo esc_url(Podcast::feedUrl()); ?>" data-no-swup class="btn-plain hidden h-10 gap-1.5 rounded-xl px-2.5 text-sm sm:inline-flex" aria-label="<?php esc_attr_e('RSS feed', 'ars-petal'); ?>"><?php Icon::render('rss', 'size-4'); ?>RSS</a>

      <div class="dropdown dropdown-end lg:hidden">
        <div tabindex="0" role="button" class="btn-plain size-10 rounded-xl" aria-label="<?php esc_attr_e('Open menu', 'ars-petal'); ?>"><?php Icon::render('menu', 'size-5'); ?></div>
        <ul id="petal-nav-mobile" data-swup-sync tabindex="0" class="dropdown-content menu z-50 mt-2 max-h-[70vh] w-64 flex-nowrap overflow-y-auto rounded-box border border-base-300 bg-base-100 p-2 shadow-xl">
          <?php Template::render('parts/nav-mobile', ['items' => $menu]); ?>
          <?php if (Podcast::isActive()) : ?>
            <li class="menu-title mt-2"><?php esc_html_e('Listen', 'ars-petal'); ?></li>
            <?php Template::render('parts/listen-links', ['style' => 'menu']); ?>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>

  <dialog x-ref="dialog" class="modal modal-top sm:modal-middle" @close="q = ''; results = []">
    <div class="modal-box max-w-2xl rounded-3xl border border-base-300 bg-base-100 p-3 shadow-2xl">
      <form x-ref="form" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search" class="flex items-center gap-3 rounded-2xl bg-base-200 px-4 py-3" @submit.prevent="submit()">
        <?php Icon::render('search', 'mute size-5 shrink-0'); ?>
        <label for="site-search" class="sr-only"><?php esc_html_e('Search', 'ars-petal'); ?></label>
        <input id="site-search" name="s" x-ref="input" x-model="q" @input="search()" type="search" autocomplete="off" placeholder="<?php echo esc_attr(Podcast::isActive() ? __('Search episodes and posts…', 'ars-petal') : __('Search posts…', 'ars-petal')); ?>" class="w-full border-0 bg-transparent text-lg outline-none">
        <kbd class="kbd kbd-sm hidden sm:inline-flex">esc</kbd>
      </form>
      <div class="mt-2 max-h-96 overflow-y-auto">
        <template x-for="result in results" :key="result.subtype + result.id">
          <a :href="result.url" @click="close()" class="flex items-center gap-3 rounded-2xl p-3 hover:bg-base-200">
            <span class="badge badge-sm shrink-0" :class="result.subtype === '<?php echo esc_js(Podcast::POST_TYPE); ?>' ? 'badge-primary' : 'badge-secondary'" x-text="label(result)"></span>
            <span class="min-w-0 flex-1 truncate font-medium" x-text="text(result.title)"></span>
          </a>
        </template>
        <a x-show="q && results.length" x-cloak href="#" @click.prevent="submit()" class="mt-1 flex items-center justify-center gap-1 rounded-2xl p-3 text-sm font-medium hover:bg-base-200"><?php esc_html_e('See all results', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
        <p x-show="loading" x-cloak class="px-5 py-8 text-center"><span class="loading loading-dots loading-sm"></span></p>
        <p x-show="q && !loading && !results.length" x-cloak class="mute px-5 py-8 text-center text-sm"><?php esc_html_e('Nothing found. Press Enter for a full search.', 'ars-petal'); ?></p>
        <p x-show="!q" class="mute px-5 py-8 text-center text-sm"><?php esc_html_e('Type to search', 'ars-petal'); ?></p>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button><?php esc_html_e('Close', 'ars-petal'); ?></button></form>
  </dialog>
</header>

<main id="swup" class="transition-lift mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:pt-8">
