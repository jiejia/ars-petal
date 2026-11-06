<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * Titles, breadcrumbs and pagination of list pages.
 */
final class Archive
{
    /**
     * The heading of the current list page.
     *
     * @return string
     */
    public static function title(): string
    {
        return match (true) {
            is_search() => sprintf(/* translators: %s: search terms. */ __('Search: %s', 'ars-petal'), get_search_query()),
            is_post_type_archive(Podcast::POST_TYPE) => get_query_var('tag') !== '' ? '#' . single_term_title('', false) : __('All episodes', 'ars-petal'),
            Blog::isRequest(), is_home() && ! is_front_page() => Blog::label(),
            is_home() => __('Latest posts', 'ars-petal'),
            is_tag() => '#' . single_term_title('', false),
            is_category(), is_tax() => single_term_title('', false),
            is_author() => get_the_author_meta('display_name', (int) get_query_var('author')),
            is_year() => get_the_date('Y'),
            is_month() => get_the_date('Y.m'),
            is_day() => get_the_date('Y.m.d'),
            default => wp_strip_all_tags(get_the_archive_title()),
        };
    }

    /**
     * The description under the heading.
     *
     * @return string
     */
    public static function description(): string
    {
        return is_author() ? (string) get_the_author_meta('description', (int) get_query_var('author')) : wp_strip_all_tags((string) get_the_archive_description());
    }

    /**
     * Group posts by month, newest first as given, for the list view.
     *
     * @param array<int,\WP_Post> $posts Posts in display order.
     * @return array<int,array{label:string,items:array<int,\WP_Post>}>
     */
    public static function byMonth(array $posts): array
    {
        $groups = [];

        foreach ($posts as $post) {
            $key = (string) get_the_date('Y-m', $post);
            /* translators: Month heading of archive lists, a PHP date format. */
            $groups[$key] ??= ['label' => (string) get_the_date(_x('F Y', 'archive month heading', 'ars-petal'), $post), 'items' => []];
            $groups[$key]['items'][] = $post;
        }

        return array_values($groups);
    }

    /**
     * Sort orders, ?sort= value => label. Posts have no duration, so only episodes get "Shortest".
     *
     * @param bool $episodes Whether the list is of episodes.
     * @return array<string,string>
     */
    public static function sorts(bool $episodes = true): array
    {
        $sorts = [
            'new' => __('Newest', 'ars-petal'),
            'old' => __('Oldest', 'ars-petal'),
        ];

        return $episodes ? $sorts + ['short' => __('Shortest', 'ars-petal')] : $sorts;
    }

    /**
     * Breadcrumb trail of the current page; the last item has no URL.
     *
     * @return array<int,array{label:string,url:string}>
     */
    public static function breadcrumbs(): array
    {
        $items = [['label' => __('Home', 'ars-petal'), 'url' => home_url('/')]];

        if (is_singular(Podcast::POST_TYPE)) {
            $items[] = ['label' => __('All episodes', 'ars-petal'), 'url' => Podcast::archiveUrl()];
        } elseif (is_singular('post')) {
            if (Blog::url() !== '') {
                $items[] = ['label' => Blog::label(), 'url' => Blog::url()];
            }

            $category = get_the_category();

            if ($category !== []) {
                $items[] = ['label' => $category[0]->name, 'url' => (string) get_category_link($category[0])];
            }
        } elseif (is_page()) {
            foreach (array_reverse(get_post_ancestors(get_queried_object_id())) as $ancestor) {
                $items[] = ['label' => get_the_title($ancestor), 'url' => (string) get_permalink($ancestor)];
            }
        }

        $items[] = ['label' => is_singular() ? wp_strip_all_tags(get_the_title()) : self::title(), 'url' => ''];

        return $items;
    }

    /**
     * Page links of the main query as a daisyUI join.
     *
     * @return array<int,array{label:string,url:string,current:bool,icon:string}>
     */
    public static function pagination(): array
    {
        $links = paginate_links(['type' => 'array', 'mid_size' => 1, 'prev_next' => false]);

        if (! is_array($links)) {
            return [];
        }

        $current = max(1, (int) get_query_var('paged'));
        $total = (int) $GLOBALS['wp_query']->max_num_pages;
        $items = [['label' => __('Previous page', 'ars-petal'), 'url' => $current > 1 ? (string) get_pagenum_link($current - 1) : '', 'current' => false, 'icon' => 'chevron-left']];

        foreach ($links as $link) {
            preg_match('/href=["\']([^"\']+)/', $link, $href);
            $items[] = [
                'label' => trim(wp_strip_all_tags($link)),
                'url' => isset($href[1]) ? html_entity_decode($href[1]) : '',
                'current' => str_contains($link, 'current'),
                'icon' => '',
            ];
        }

        $items[] = ['label' => __('Next page', 'ars-petal'), 'url' => $current < $total ? (string) get_pagenum_link($current + 1) : '', 'current' => false, 'icon' => 'chevron-right'];

        return $items;
    }
}
