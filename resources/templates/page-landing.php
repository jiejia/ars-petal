<?php
/**
 * "Landing page" page template, laid out like a component library's landing page: a widget area on top (by default the featured
 * carousel, the hosts and guests, the latest and popular episodes), then topic color cards, recent posts and the subscribe block.
 * Without A Ripple Song the same sections show posts and categories. Pick it for a page and set that page as the static
 * homepage under Settings > Reading; with "Your latest posts" the home page is the post list (index.php).
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Widgets\HomeArea;

get_header();

$latest = Podcast::latest();

if ($latest) {
    $categories = Podcast::categories();

    // The widget area at the top; the banners, hosts and guests, latest and popular episodes while it is empty.
    HomeArea::render();
    Template::render('front/numbers');
    Template::render('front/topics', [
        'podcast' => true,
        'topics' => array_map(static fn (array $category): array => $category + ['url' => Podcast::categoryUrl($category['term']), 'image' => Podcast::categoryImage($category['term'])], array_slice($categories, 0, 4)),
    ]);
    Template::render('front/posts', ['posts' => get_posts(['numberposts' => 3])]);
} else {
    $posts = get_posts(['numberposts' => 9]);
    $categories = get_categories(['orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true]);

    HomeArea::render();
    Template::render('front/posts', ['posts' => array_slice($posts, 3, 6) ?: $posts]);
    Template::render('front/topics', [
        'podcast' => false,
        'topics' => array_map(static fn (\WP_Term $term): array => ['term' => $term, 'count' => (int) $term->count, 'url' => (string) get_category_link($term)], array_slice($categories, 0, 4)),
    ]);
}

Template::render('front/subscribe');

get_footer();
