<?php
/**
 * Home page, laid out like a component library's landing page: the featured carousel, the hosts and guests, the latest and popular
 * episodes, topic color cards, recent posts and the subscribe block. Without A Ripple Song the same sections show posts and categories.
 * A static front page shows that page.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;

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
    $episodes = array_map(static fn (\WP_Post $post): Episode => new Episode($post), get_posts(['post_type' => Podcast::POST_TYPE, 'numberposts' => 8]));
    $categories = Podcast::categories();

    // Featured episodes first; without any, the latest three.
    $featured = Podcast::featured();
    Template::render('front/hero', ['items' => $featured ?: array_slice($episodes, 0, 3), 'featured' => $featured !== []]);
    Template::render('front/people');
    Template::render('front/latest', ['episodes' => $episodes]);
    Template::render('front/popular', ['episodes' => Podcast::popular(5)]);
    Template::render('front/numbers');
    Template::render('front/topics', [
        'podcast' => true,
        'topics' => array_map(static fn (array $category): array => $category + ['url' => Podcast::categoryUrl($category['term']), 'image' => Podcast::categoryImage($category['term'])], array_slice($categories, 0, 4)),
    ]);
    Template::render('front/posts', ['posts' => get_posts(['numberposts' => 3])]);
} else {
    $posts = get_posts(['numberposts' => 9]);
    $categories = get_categories(['orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true]);

    // Sticky posts (WordPress's own "featured" for posts) first; without any, the latest three.
    $sticky = array_filter(array_map('intval', (array) get_option('sticky_posts', [])));
    $featured = $sticky !== [] ? get_posts(['post__in' => $sticky, 'numberposts' => -1, 'ignore_sticky_posts' => true]) : [];
    Template::render('front/hero', ['items' => $featured ?: array_slice($posts, 0, 3), 'featured' => $featured !== []]);
    Template::render('front/posts', ['posts' => array_slice($posts, 3, 6) ?: $posts]);
    Template::render('front/topics', [
        'podcast' => false,
        'topics' => array_map(static fn (\WP_Term $term): array => ['term' => $term, 'count' => (int) $term->count, 'url' => (string) get_category_link($term)], array_slice($categories, 0, 4)),
    ]);
}

Template::render('front/subscribe');

TemplateLoader::part('footer');
