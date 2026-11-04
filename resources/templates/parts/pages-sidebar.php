<?php
/**
 * The pages sidebar of page templates: the site's top-level pages (and the current page's children),
 * the current one black.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

$current = get_queried_object_id();
$exclude = array_filter([(int) get_option('page_on_front'), (int) get_option('page_for_posts')]);
$pages = get_pages(['parent' => 0, 'sort_column' => 'menu_order,post_title', 'number' => 10, 'exclude' => $exclude]);
$children = get_pages(['parent' => $current, 'sort_column' => 'menu_order,post_title', 'number' => 10]);
$items = [];

foreach ($pages as $page) {
    $items[] = ['label' => get_the_title($page), 'meta' => wp_trim_words((string) $page->post_excerpt, 6, '…'), 'url' => (string) get_permalink($page), 'current' => $page->ID === $current, 'icon' => 'file-text'];

    if ($page->ID === $current || in_array($page->ID, get_post_ancestors($current), true)) {
        foreach ($children as $child) {
            $items[] = ['label' => get_the_title($child), 'url' => (string) get_permalink($child), 'current' => false, 'icon' => 'chevron-right'];
        }
    }
}

if ($items === []) {
    return;
}

Template::render('parts/side-list', ['title' => __('Pages', 'ars-petal'), 'items' => $items]);
