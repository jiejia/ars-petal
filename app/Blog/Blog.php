<?php

namespace Jiejia\ArsPetal\Blog;

defined('ABSPATH') || exit;

/**
 * The full post list at /blog/ (?blog=1 with plain permalinks).
 *
 * The home page shows the latest episode or post plus a few recent ones, so the posts need a list
 * of their own. It is the main query's blog home with a flag, rendered by index.php. A site with a
 * static front page uses its own "Posts page" instead; a real page with the slug "blog" wins too.
 */
final class Blog
{
    public const QUERY_VAR = 'blog';

    private const SLUG = 'blog';

    /**
     * Register the route.
     *
     * @return void
     */
    public function register(): void
    {
        add_filter('query_vars', [$this, 'queryVars']);
        add_filter('request', [$this, 'request']);
        add_action('pre_get_posts', [$this, 'sortQuery']);
        add_action('template_redirect', [$this, 'redirectPagedHome']);
        add_filter('redirect_canonical', [$this, 'canonical']);
        add_filter('document_title_parts', [$this, 'documentTitle']);
        add_filter('body_class', [$this, 'bodyClass']);
    }

    /**
     * Whether the theme serves the post list itself (the front page shows latest posts).
     *
     * @return bool
     */
    public static function routed(): bool
    {
        return get_option('show_on_front') !== 'page';
    }

    /**
     * Whether the current request is the post list.
     *
     * @return bool
     */
    public static function isRequest(): bool
    {
        return self::routed() && (bool) get_query_var(self::QUERY_VAR);
    }

    /**
     * URL of the post list (a page of it), or '' when the site has none.
     *
     * @param int $page Page number.
     * @return string
     */
    public static function url(int $page = 1): string
    {
        if (! self::routed()) {
            $postsPage = (int) get_option('page_for_posts');

            return $postsPage > 0 ? (string) get_permalink($postsPage) : '';
        }

        // Plain permalinks, or a real page / post owns /blog/: the query var form.
        if (! get_option('permalink_structure') || self::slugTaken()) {
            return add_query_arg(array_filter([self::QUERY_VAR => 1, 'paged' => $page > 1 ? $page : null]), home_url('/'));
        }

        return user_trailingslashit(home_url(self::SLUG . ($page > 1 ? '/page/' . $page : '')));
    }

    /**
     * Whether a page or post with the slug "blog" exists (it keeps /blog/).
     *
     * @return bool
     */
    private static function slugTaken(): bool
    {
        static $taken = null;

        return $taken ??= get_page_by_path(self::SLUG) !== null || get_page_by_path(self::SLUG, OBJECT, 'post') !== null;
    }

    /**
     * The menu label: the posts page title, else "Blog".
     *
     * @return string
     */
    public static function label(): string
    {
        $postsPage = (int) get_option('page_for_posts');

        return ! self::routed() && $postsPage > 0 ? get_the_title($postsPage) : __('Blog', 'ars-petal');
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
     * Allow ?blog=1.
     *
     * @param array<int,string> $vars Public query vars.
     * @return array<int,string>
     */
    public function queryVars(array $vars): array
    {
        $vars[] = self::QUERY_VAR;

        if (! in_array('sort', $vars, true)) {
            $vars[] = 'sort';
        }

        return $vars;
    }

    /**
     * The requested sort order of the post list: 'new' or 'old'.
     *
     * @return string
     */
    public static function sort(): string
    {
        return get_query_var('sort') === 'old' ? 'old' : 'new';
    }

    /**
     * ?sort=old on the post list and on category / tag archives.
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

    /**
     * Pretty permalinks: /blog/ and /blog/page/2/ reach the catch-all rules as pagename=blog (or
     * name=blog with a /%postname%/ structure); turn them into the post list unless a page or post
     * with that slug exists.
     *
     * @param array<string,mixed> $vars Parsed query vars.
     * @return array<string,mixed>
     */
    public function request(array $vars): array
    {
        $slug = $vars['pagename'] ?? $vars['name'] ?? '';

        if ($slug !== self::SLUG || ! self::routed() || self::slugTaken()) {
            return $vars;
        }

        $paged = (int) ($vars['paged'] ?? 0);

        return array_filter([self::QUERY_VAR => 1, 'paged' => $paged > 1 ? $paged : null]);
    }

    /**
     * Page 2+ of the home page now lives in the post list.
     *
     * @return void
     */
    public function redirectPagedHome(): void
    {
        if (self::routed() && is_front_page() && is_paged() && ! self::isRequest()) {
            wp_safe_redirect(self::url((int) get_query_var('paged')), 301);
            exit;
        }
    }

    /**
     * Keep core from "correcting" the post list URL to the home page.
     *
     * @param string|false $url Redirect URL.
     * @return string|false
     */
    public function canonical(string|false $url): string|false
    {
        return self::isRequest() ? false : $url;
    }

    /**
     * "Blog – Site" instead of the home page title.
     *
     * @param array<string,string> $parts Title parts.
     * @return array<string,string>
     */
    public function documentTitle(array $parts): array
    {
        if (! self::isRequest()) {
            return $parts;
        }

        return array_filter(['title' => self::label(), 'page' => $parts['page'] ?? '', 'site' => (string) get_bloginfo('name')]);
    }

    /**
     * Mark the post list on <body>; core already adds "home blog" for it.
     *
     * @param array<int,string> $classes Body classes.
     * @return array<int,string>
     */
    public function bodyClass(array $classes): array
    {
        if (self::isRequest()) {
            $classes[] = 'petal-blog';
        }

        return $classes;
    }
}
