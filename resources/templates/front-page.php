<?php
/**
 * Home page, laid out like a component library's landing page: a widget area on top (by default the featured carousel, the hosts
 * and guests, the latest and popular episodes), then topic color cards, recent posts and the subscribe block. Without A Ripple Song the same sections show posts and categories.
 * A static front page shows that page.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\Widgets\HomeArea;

if (get_option('show_on_front') === 'page' && ! is_home()) {
    require locate_template(TemplateLoader::DIRECTORY . 'page.php');

    return;
}

// The full post list (/blog/) is the blog home with a flag; page 2+ of the home page redirects there.
if (Blog::isRequest()) {
    require locate_template(TemplateLoader::DIRECTORY . 'index.php');

    return;
}

TemplateLoader::part('header');

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

TemplateLoader::part('footer');
