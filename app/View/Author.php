<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * The author page's two lists: episodes the person hosts or guests on, and posts they wrote.
 *
 * ?petal_tab=episodes|posts picks the list; it falls back to the one with content, episodes first.
 * Podcast\Integration::authorQuery() swaps the main query to the episodes for that tab, so both
 * lists page with the normal pagination.
 */
final class Author
{
    public const QUERY_VAR = 'petal_tab';

    public const EPISODES = 'episodes';

    public const POSTS = 'posts';

    /**
     * The author of the page.
     *
     * @param \WP_Query|null $query Query, the main one by default.
     * @return \WP_User|null
     */
    public static function user(?\WP_Query $query = null): ?\WP_User
    {
        $user = ($query ?? $GLOBALS['wp_query'])->get_queried_object();

        return $user instanceof \WP_User ? $user : null;
    }

    /**
     * Number of items in each list.
     *
     * @param \WP_User $user Author.
     * @return array{episodes:int,posts:int}
     */
    public static function counts(\WP_User $user): array
    {
        static $cache = [];

        return $cache[$user->ID] ??= [
            self::EPISODES => count(Podcast::episodeIdsOf($user->ID)),
            self::POSTS => (int) count_user_posts($user->ID, 'post', true),
        ];
    }

    /**
     * "2 episodes · 1 post", or '' when the person has neither.
     *
     * @param \WP_User $user Author.
     * @return string
     */
    public static function summary(\WP_User $user): string
    {
        $counts = self::counts($user);

        return implode(' · ', array_filter([
            /* translators: %s: number of episodes. */
            $counts[self::EPISODES] > 0 ? sprintf(_nx('%s episode', '%s episodes', $counts[self::EPISODES], 'author page summary', 'ars-petal'), number_format_i18n($counts[self::EPISODES])) : '',
            /* translators: %s: number of posts. */
            $counts[self::POSTS] > 0 ? sprintf(_n('%s post', '%s posts', $counts[self::POSTS], 'ars-petal'), number_format_i18n($counts[self::POSTS])) : '',
        ]));
    }

    /**
     * The list shown: the requested one when it has items, else episodes, else posts.
     *
     * @param \WP_User $user Author.
     * @return string
     */
    public static function tab(\WP_User $user): string
    {
        $counts = self::counts($user);
        $requested = (string) get_query_var(self::QUERY_VAR);

        if (isset($counts[$requested]) && $counts[$requested] > 0) {
            return $requested;
        }

        return $counts[self::EPISODES] > 0 ? self::EPISODES : self::POSTS;
    }

    /**
     * Link to a list; the default one has no ?petal_tab=.
     *
     * @param \WP_User $user Author.
     * @param string $tab List.
     * @return string
     */
    public static function tabUrl(\WP_User $user, string $tab): string
    {
        $counts = self::counts($user);
        $default = $counts[self::EPISODES] > 0 ? self::EPISODES : self::POSTS;
        $url = get_author_posts_url($user->ID);

        return $tab === $default ? $url : add_query_arg(self::QUERY_VAR, $tab, $url);
    }
}
