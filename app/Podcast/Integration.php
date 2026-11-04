<?php

namespace Jiejia\ArsPetal\Podcast;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\View\Author;

/**
 * Hooks into A Ripple Song while it is active.
 */
final class Integration
{
    /**
     * Episodes per archive page; a multiple of the 3-column grid.
     */
    public const PER_PAGE = 12;

    /**
     * Archive sort orders, ?sort= value => query arguments.
     */
    public const SORTS = ['new', 'old', 'short'];

    /**
     * Register podcast hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // The theme prints play / favorite / play count in its own episode header.
        add_filter('a_ripple_song_episode_meta_enabled', '__return_false');
        // The footer player shows the same stand-in covers as the theme's cards.
        add_filter('a_ripple_song_player_episode_cover', [$this, 'playerCover'], 10, 2);
        add_filter('query_vars', [$this, 'queryVars']);
        add_action('pre_get_posts', [$this, 'archiveQuery']);
        add_action('pre_get_posts', [$this, 'authorQuery']);

        foreach (['save_post_' . Podcast::POST_TYPE, 'deleted_post', 'edited_post_tag', 'delete_post_tag'] as $hook) {
            add_action($hook, [Podcast::class, 'flushTags']);
        }
    }

    /**
     * Allow ?sort= on the episode archive and ?tab= on author pages.
     *
     * @param array<int,string> $vars Public query vars.
     * @return array<int,string>
     */
    public function queryVars(array $vars): array
    {
        $vars[] = 'sort';
        $vars[] = Author::QUERY_VAR;

        return $vars;
    }

    /**
     * Page size and sort order of the episode archive (also when filtered by ?tag=) and of episode category pages.
     *
     * @param \WP_Query $query Query.
     * @return void
     */
    public function archiveQuery(\WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query() || ! ($query->is_post_type_archive(Podcast::POST_TYPE) || $query->is_tax(Podcast::CATEGORY))) {
            return;
        }

        $query->set('posts_per_page', self::PER_PAGE);

        $sort = self::sort();

        if ($sort === 'old') {
            $query->set('order', 'ASC');
        }

        if ($sort === 'short') {
            // The NOT EXISTS clause keeps episodes without a duration in the result.
            $query->set('meta_query', [
                'relation' => 'OR',
                'duration' => ['key' => '_' . Podcast::POST_TYPE . '_duration', 'type' => 'NUMERIC'],
                ['key' => '_' . Podcast::POST_TYPE . '_duration', 'compare' => 'NOT EXISTS'],
            ]);
            $query->set('orderby', ['duration' => 'ASC', 'date' => 'DESC']);
        }
    }

    /**
     * Author page, episodes tab: list the episodes the person hosts or guests on instead of the
     * posts they wrote. The author is resolved first, so the page keeps its author and title.
     *
     * @param \WP_Query $query Query.
     * @return void
     */
    public function authorQuery(\WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query() || ! $query->is_author()) {
            return;
        }

        $user = Author::user($query);

        if (! $user || Author::tab($user) !== Author::EPISODES) {
            return;
        }

        $query->set('author', '');
        $query->set('author_name', '');
        $query->set('post_type', Podcast::POST_TYPE);
        $query->set('post__in', Podcast::episodeIdsOf($user->ID));
        $query->set('posts_per_page', self::PER_PAGE);
    }

    /**
     * The player cover of an episode, as the theme shows it.
     *
     * @param string $cover Cover chosen by the plugin.
     * @param \WP_Post $post Episode.
     * @return string
     */
    public function playerCover(string $cover, \WP_Post $post): string
    {
        $episode = Episode::from($post);

        return $episode ? $episode->cover('medium') : $cover;
    }

    /**
     * The requested archive sort order.
     *
     * @return string
     */
    public static function sort(): string
    {
        $sort = (string) get_query_var('sort');

        return in_array($sort, self::SORTS, true) ? $sort : 'new';
    }
}
