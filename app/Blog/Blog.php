<?php

namespace Jiejia\ArsPetal\Blog;

defined('ABSPATH') || exit;

/**
 * The post list, wherever Settings > Reading puts it: the home page with "Your latest posts", else
 * the chosen "Posts page". Also the ?petal_sort=old order of the list and its category / tag archives.
 */
final class Blog
{
    /**
     * The sort order query var of the post list and the episode archive (prefixed, as the theme directory asks).
     */
    public const SORT_VAR = 'petal_sort';

    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_filter('query_vars', [$this, 'queryVars']);
        add_action('pre_get_posts', [$this, 'sortQuery']);
    }

    /**
     * Whether the post list has a page of its own: a static front page with a "Posts page".
     *
     * @return bool
     */
    public static function hasPage(): bool
    {
        return get_option('show_on_front') === 'page' && (int) get_option('page_for_posts') > 0;
    }

    /**
     * URL of the post list, or '' when a static front page has no posts page.
     *
     * @return string
     */
    public static function url(): string
    {
        if (get_option('show_on_front') !== 'page') {
            return home_url('/');
        }

        return self::hasPage() ? (string) get_permalink((int) get_option('page_for_posts')) : '';
    }

    /**
     * The menu label: the posts page title, else "Blog".
     *
     * @return string
     */
    public static function label(): string
    {
        return self::hasPage() ? get_the_title((int) get_option('page_for_posts')) : __('Blog', 'ars-petal');
    }

    /**
     * Whether any post is published, so a link to the list is worth showing.
     *
     * @return bool
     */
    public static function hasPosts(): bool
    {
        return (int) wp_count_posts()->publish > 0;
    }

    /**
     * Allow ?petal_sort=.
     *
     * @param array<int,string> $vars Public query vars.
     * @return array<int,string>
     */
    public function queryVars(array $vars): array
    {
        $vars[] = self::SORT_VAR;

        return $vars;
    }

    /**
     * The requested sort order of the post list: 'new' or 'old'.
     *
     * @return string
     */
    public static function sort(): string
    {
        return get_query_var(self::SORT_VAR) === 'old' ? 'old' : 'new';
    }

    /**
     * ?petal_sort=old on the post list and on category / tag archives.
     *
     * @param \WP_Query $query Query.
     * @return void
     */
    public function sortQuery(\WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query() || $query->is_search() || ! ($query->is_home() || $query->is_category() || $query->is_tag())) {
            return;
        }

        if (self::sort() === 'old') {
            $query->set('order', 'ASC');
        }
    }
}
