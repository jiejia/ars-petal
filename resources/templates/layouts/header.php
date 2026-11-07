<?php
/**
 * Site header, printed by TemplateLoader::part('header'): <head>, the monospace announcement bar, the
 * frosted sticky top bar (petal mark, centered icon menu with dropdown cards, ⌘K search, sun / moon
 * button) with the search dialog, and the opening of the swup container. Everything outside
 * <main id="swup"> stays across swup page swaps; the menus are refreshed from the new page ([data-swup-sync]).
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Navigation\Menu;
use Jiejia\ArsPetal\Podcast\Podcast;

$menu = Menu::tree('primary');
$latest = Podcast::latest();
$newest = $latest ? null : (get_posts(['numberposts' => 1])[0] ?? null);
$schedule = Podcast::schedule();
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
    <nav id="petal-nav" data-swup-sync class="topnav mx-auto hidden items-center gap-1 lg:flex" aria-label="<?php esc_attr_e('Main menu', 'ars-petal'); ?>">
      <?php Template::render('parts/nav-desktop', ['items' => $menu]); ?>
    </nav>

    <div class="ml-auto flex items-center gap-1 lg:ml-0">
      <button type="button" class="search-box hidden xl:flex" @click="open()" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>">
        <?php Icon::render('search', 'mute size-4'); ?><span><?php esc_html_e('Search…', 'ars-petal'); ?></span><kbd class="kbd kbd-sm">/</kbd>
      </button>
      <button type="button" class="btn-plain size-10 rounded-xl xl:hidden" @click="open()" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>"><?php Icon::render('search', 'size-[1.15rem]'); ?></button>
      <?php // Cycles light -> dark -> follow system; the icon shows the current mode. ?>
      <button type="button" class="btn-plain size-10 rounded-xl" @click="$store.ui.toggleTheme()" :aria-label="{ light: '<?php echo esc_js(__('Day mode', 'ars-petal')); ?>', dark: '<?php echo esc_js(__('Night mode', 'ars-petal')); ?>', system: '<?php echo esc_js(__('Follow system', 'ars-petal')); ?>' }[$store.ui.mode]" aria-label="<?php esc_attr_e('Switch color scheme', 'ars-petal'); ?>">
        <span x-show="$store.ui.mode === 'light'" x-cloak><?php Icon::render('sun', 'size-[1.15rem]'); ?></span>
        <span x-show="$store.ui.mode === 'dark'" x-cloak><?php Icon::render('moon', 'size-[1.15rem]'); ?></span>
        <span x-show="$store.ui.mode === 'system'" x-cloak><?php Icon::render('monitor', 'size-[1.15rem]'); ?></span>
      </button>
      <?php // The podcast apps filled in under the plugin's Subscription Settings, then the RSS feed. ?>
      <div class="dropdown dropdown-end hidden sm:block">
        <div tabindex="0" role="button" class="btn btn-primary ml-1 h-10 min-h-0 gap-1.5 rounded-full border-0 px-5 text-sm font-semibold shadow-none" aria-haspopup="menu"><?php Icon::render('podcast', 'size-4'); ?><?php esc_html_e('Subscribe', 'ars-petal'); ?></div>
        <ul tabindex="0" class="dropdown-content menu z-50 mt-3 max-h-[70vh] w-64 flex-nowrap gap-0.5 overflow-y-auto rounded-3xl border border-base-300 bg-base-100 p-3 shadow-xl [&_svg]:size-5">
          <li class="menu-title text-sm font-medium"><?php echo esc_html(Podcast::isActive() ? __('Listen in a podcast app', 'ars-petal') : __('Follow', 'ars-petal')); ?></li>
          <?php Template::render('parts/listen-links', ['style' => 'menu']); ?>
        </ul>
      </div>

      <?php // Opens the menu drawer below the header (a fixed drawer inside the blurred header would be clipped to it). ?>
      <div class="lg:hidden">
        <label for="petal-menu" class="btn-plain size-10 cursor-pointer rounded-xl" aria-label="<?php esc_attr_e('Open menu', 'ars-petal'); ?>"><?php Icon::render('menu', 'size-5'); ?></label>
      </div>
    </div>
  </div>

  <dialog x-ref="dialog" class="modal modal-top" @close="q = ''; results = []">
    <div class="modal-box mx-auto mt-[10vh] max-w-2xl rounded-3xl border border-base-300 bg-base-100 p-3 shadow-2xl">
      <?php get_search_form(['petal_variant' => 'modal']); ?>
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

<?php // Small screens: the main menu in a daisyUI drawer from the right (https://daisyui.com/components/drawer/), above the WordPress admin bar (z-index 99999). ?>
<div class="drawer drawer-end lg:hidden">
  <input id="petal-menu" type="checkbox" class="drawer-toggle" aria-label="<?php esc_attr_e('Open menu', 'ars-petal'); ?>" x-data @keydown.escape.window="$el.checked = false">
  <div class="drawer-side z-[100001]" x-data @click="if ($event.target.closest('a')) document.getElementById('petal-menu').checked = false">
    <label for="petal-menu" class="drawer-overlay" aria-label="<?php esc_attr_e('Close', 'ars-petal'); ?>"></label>
    <nav class="flex min-h-full w-72 max-w-[85vw] flex-col bg-base-100 p-4" aria-label="<?php esc_attr_e('Main menu', 'ars-petal'); ?>">
      <?php // The name of the menu assigned to the header location; "Main menu" for the fallback menu. ?>
      <div class="mb-3 flex items-center justify-between gap-3 pl-2">
        <h2 class="display truncate text-xl"><?php echo esc_html(Menu::name('primary') ?: __('Main menu', 'ars-petal')); ?></h2>
        <label for="petal-menu" class="grid size-9 shrink-0 cursor-pointer place-items-center rounded-full hover:bg-base-200" aria-label="<?php esc_attr_e('Close', 'ars-petal'); ?>"><?php Icon::render('x', 'size-5'); ?></label>
      </div>
      <ul id="petal-nav-mobile" data-swup-sync class="menu w-full flex-nowrap p-0 text-base">
        <?php Template::render('parts/nav-mobile', ['items' => $menu]); ?>
        <?php if (Podcast::isActive()) : ?>
          <li class="menu-title mt-3"><?php esc_html_e('Listen', 'ars-petal'); ?></li>
          <?php Template::render('parts/listen-links', ['style' => 'menu']); ?>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</div>

<main id="swup" class="transition-lift mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:pt-8">
