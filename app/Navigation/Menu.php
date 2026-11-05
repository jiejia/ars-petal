<?php

namespace Jiejia\ArsPetal\Navigation;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Libraries\Html;
use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * The nav menus as trees, so templates can print the icon top bar, the mega dropdown, the nested mobile
 * menu and the footer columns. Before a menu is assigned, a sensible fallback menu is used.
 *
 * Icons: give a menu item the CSS class "icon-{lucide-name}" (Menus → Screen Options → CSS Classes), e.g.
 * "icon-audio-lines". The icon must be listed in bin/copy-icons.mjs.
 *
 * @phpstan-type Item array{label:string,url:string,current:bool,icon:string,children:array<int,mixed>}
 */
final class Menu
{
    /**
     * Icons of the fallback menu items.
     */
    private const FALLBACK_ICONS = ['home' => 'house', 'episodes' => 'audio-lines', 'blog' => 'newspaper', 'page' => 'sparkles'];

    /**
     * Register menu hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Nothing to hook: menus are read as trees by templates.
    }

    /**
     * The name of the menu assigned to a location, or '' when none is assigned.
     *
     * @param string $location Menu location.
     * @return string
     */
    public static function name(string $location): string
    {
        $locations = get_nav_menu_locations();
        $menu = isset($locations[$location]) ? wp_get_nav_menu_object($locations[$location]) : false;

        return $menu instanceof \WP_Term ? $menu->name : '';
    }

    /**
     * The items of a menu location as a tree, or the fallback menu.
     *
     * @param string $location Menu location.
     * @return array<int,Item>
     */
    public static function tree(string $location): array
    {
        if (! has_nav_menu($location)) {
            return self::fallback();
        }

        $locations = get_nav_menu_locations();
        $items = wp_get_nav_menu_items($locations[$location] ?? 0);

        if (! is_array($items) || $items === []) {
            return [];
        }

        // Adds current-menu-item / current-menu-ancestor classes, as wp_nav_menu() does.
        _wp_menu_item_classes_by_context($items);

        $nodes = [];
        $children = [];

        foreach ($items as $item) {
            $nodes[$item->ID] = [
                'label' => (string) $item->title,
                'url' => (string) $item->url,
                'current' => self::isCurrent($item),
                'icon' => self::icon($item),
                'children' => [],
            ];
            $children[(int) $item->menu_item_parent][] = (int) $item->ID;
        }

        $build = static function (int $parent) use (&$build, $nodes, $children): array {
            $tree = [];

            foreach ($children[$parent] ?? [] as $id) {
                $node = $nodes[$id];
                $node['children'] = $build($id);
                // A parent is current when one of its descendants is.
                $node['current'] = $node['current'] || self::anyCurrent($node['children']);
                $tree[] = $node;
            }

            return $tree;
        };

        return $build(0);
    }

    /**
     * Links shown before a menu is assigned: home, episodes (with an episode categories dropdown), blog and the top-level pages.
     * The blog link appears where the home page is not the post list: podcast mode, or a static front page.
     *
     * @return array<int,Item>
     */
    public static function fallback(): array
    {
        $items = [self::node(__('Home', 'ars-petal'), home_url('/'), is_front_page() && ! Blog::isRequest(), self::FALLBACK_ICONS['home'])];

        if (Podcast::isActive()) {
            $episodes = self::node(
                __('Podcast', 'ars-petal'),
                Podcast::archiveUrl(),
                is_post_type_archive(Podcast::POST_TYPE) || is_tax(Podcast::CATEGORY) || is_singular(Podcast::POST_TYPE),
                self::FALLBACK_ICONS['episodes']
            );
            $categories = array_slice(Podcast::categories(), 0, 8);

            if ($categories !== []) {
                $episodes['children'][] = self::node(__('All episodes', 'ars-petal'), Podcast::archiveUrl(), is_post_type_archive(Podcast::POST_TYPE));

                foreach ($categories as $category) {
                    $node = self::node($category['term']->name, Podcast::categoryUrl($category['term']), is_tax(Podcast::CATEGORY, $category['term']->term_id));

                    foreach (Podcast::categories($category['term']->term_id) as $child) {
                        $node['children'][] = self::node($child['term']->name, Podcast::categoryUrl($child['term']), is_tax(Podcast::CATEGORY, $child['term']->term_id));
                    }

                    $episodes['children'][] = $node;
                }
            }

            $items[] = $episodes;
        }

        if (Blog::url() !== '' && (Podcast::isActive() || ! Blog::routed()) && Blog::hasPosts()) {
            $items[] = self::node(Blog::label(), Blog::url(), Blog::isRequest() || (is_home() && ! is_front_page()) || is_singular('post'), self::FALLBACK_ICONS['blog']);
        }

        $exclude = [(int) get_option('page_on_front'), (int) get_option('page_for_posts'), (int) get_option('wp_page_for_privacy_policy')];
        $pages = get_pages(['parent' => 0, 'sort_column' => 'menu_order,post_title', 'number' => 6, 'exclude' => array_filter($exclude)]);

        foreach (array_slice($pages, 0, 3) as $page) {
            $items[] = self::node(get_the_title($page), (string) get_permalink($page), is_page($page->ID), self::FALLBACK_ICONS['page']);
        }

        return $items;
    }

    /**
     * Split the children of a top item into dropdown columns: children with their own children become
     * columns headed by their link; plain children are gathered in a first column under the item's label.
     *
     * @param array<string,mixed> $item Top-level item.
     * @return array<int,array{title:string,url:string,links:array<int,Item>}>
     */
    public static function columns(array $item): array
    {
        $plain = [];
        $columns = [];

        foreach ($item['children'] as $child) {
            if ($child['children'] === []) {
                $plain[] = $child;
            } else {
                $columns[] = ['title' => $child['label'], 'url' => $child['url'], 'links' => $child['children']];
            }
        }

        if ($plain !== []) {
            array_unshift($columns, ['title' => $item['label'], 'url' => '', 'links' => $plain]);
        }

        return $columns;
    }

    /**
     * Attributes of a link in a menu that never uses swup, e.g. podcast app links.
     *
     * @param string $url Link URL.
     * @return string
     */
    public static function externalAttributes(string $url): string
    {
        $external = ! str_starts_with($url, home_url());

        // Only href is a URL; esc_url would turn target="_blank" into "http://_blank".
        return ' href="' . esc_url($url) . '"' . Html::attributes(['data-no-swup' => true, 'rel' => $external ? 'noopener' : null, 'target' => $external ? '_blank' : null]);
    }

    /**
     * Whether a menu item is the current page.
     *
     * The post list is the blog home to core, so core marks a "Home" link current there; the theme
     * marks a link to the list instead (also on single posts).
     *
     * @param \WP_Post $item Menu item.
     * @return bool
     */
    private static function isCurrent(\WP_Post $item): bool
    {
        $classes = is_array($item->classes) ? $item->classes : [];
        $current = (bool) array_intersect($classes, ['current-menu-item', 'current_page_item']);

        if (Blog::routed() && $item->type === 'custom') {
            $url = untrailingslashit((string) $item->url);

            if ($url === untrailingslashit(home_url('/'))) {
                $current = is_front_page() && ! Blog::isRequest();
            } elseif ($url === untrailingslashit(Blog::url())) {
                $current = Blog::isRequest() || is_singular('post');
            }
        }

        return $current;
    }

    /**
     * The Lucide icon named by an "icon-*" CSS class of a menu item.
     *
     * @param \WP_Post $item Menu item.
     * @return string
     */
    private static function icon(\WP_Post $item): string
    {
        foreach (is_array($item->classes) ? $item->classes : [] as $class) {
            if (str_starts_with((string) $class, 'icon-')) {
                return sanitize_key(substr((string) $class, 5));
            }
        }

        return '';
    }

    /**
     * @param array<int,Item> $items Items.
     * @return bool
     */
    private static function anyCurrent(array $items): bool
    {
        foreach ($items as $item) {
            if ($item['current']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Item
     */
    private static function node(string $label, string $url, bool $current, string $icon = ''): array
    {
        return ['label' => $label, 'url' => $url, 'current' => $current, 'icon' => $icon, 'children' => []];
    }
}
