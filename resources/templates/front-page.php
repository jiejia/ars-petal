<?php
/**
 * Home page, laid out like a component library's landing page: the featured carousel, the big two-weight
 * headline with the tilted app windows, the latest episodes rail, huge numbers, topic color cards, recent
 * posts and the subscribe block. Without A Ripple Song the same sections show posts and categories.
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
use Jiejia\ArsPetal\View\Format;

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
    $tags = Podcast::tags();
    $stats = Podcast::stats();

    Template::render('front/hero', ['items' => array_slice($episodes, 0, 3)]);
    Template::render('front/intro', [
        'latest' => $latest,
        'topics' => array_map(static fn (array $tag): \WP_Term => $tag['term'], $tags),
        'topicUrl' => [Podcast::class, 'tagUrl'],
    ]);
    Template::render('front/latest', ['episodes' => $episodes]);
    Template::render('front/numbers', ['numbers' => array_values(array_filter([
        ['value' => number_format_i18n($stats['episodes']), 'label' => _n('episode', 'episodes', $stats['episodes'], 'ars-petal')],
        $stats['duration'] > 0 ? ['value' => max(1, (int) round($stats['duration'] / 60)) . '′', 'label' => __('average length', 'ars-petal')] : null,
        $stats['plays'] > 0 ? ['value' => Format::compact($stats['plays']), 'label' => _n('play', 'plays', $stats['plays'], 'ars-petal')] : null,
        $tags !== [] ? ['value' => number_format_i18n(count($tags)), 'label' => _n('topic', 'topics', count($tags), 'ars-petal')] : null,
    ]))]);
    Template::render('front/topics', [
        'podcast' => true,
        'topics' => array_map(static fn (array $tag): array => $tag + ['url' => Podcast::tagUrl($tag['term'])], array_slice($tags, 0, 4)),
    ]);
    Template::render('front/posts', ['posts' => get_posts(['numberposts' => 3])]);
} else {
    $posts = get_posts(['numberposts' => 9]);
    $categories = get_categories(['orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true]);
    $count = (int) wp_count_posts()->publish;

    Template::render('front/hero', ['items' => array_slice($posts, 0, 3)]);
    Template::render('front/intro', [
        'latest' => null,
        'topics' => $categories,
        'topicUrl' => static fn (\WP_Term $term): string => (string) get_category_link($term),
    ]);
    Template::render('front/posts', ['posts' => array_slice($posts, 3, 6) ?: $posts]);
    Template::render('front/numbers', ['numbers' => array_values(array_filter([
        ['value' => number_format_i18n($count), 'label' => _n('post', 'posts', $count, 'ars-petal')],
        count($categories) > 1 ? ['value' => number_format_i18n(count($categories)), 'label' => _n('category', 'categories', count($categories), 'ars-petal')] : null,
    ]))]);
    Template::render('front/topics', [
        'podcast' => false,
        'topics' => array_map(static fn (\WP_Term $term): array => ['term' => $term, 'count' => (int) $term->count, 'url' => (string) get_category_link($term)], array_slice($categories, 0, 4)),
    ]);
}

Template::render('front/subscribe');

TemplateLoader::part('footer');
