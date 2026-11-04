<?php

namespace Jiejia\ArsPetal\Podcast;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Theme;

/**
 * Bridge to the A Ripple Song plugin.
 *
 * The theme is a podcast theme while the plugin's episode post type exists, and a plain blog theme
 * otherwise. Everything podcast-specific asks this class first, so nothing breaks without the plugin.
 */
final class Podcast
{
    public const POST_TYPE = 'ars_podcast';

    /**
     * Handle of the plugin's player script, enqueued only when the player shows on this page.
     */
    private const PLAYER_HANDLE = 'ars_player';

    private const TAGS_CACHE = Theme::PREFIX . 'episode_tags';

    /**
     * Whether the A Ripple Song plugin is active, i.e. its episode post type is registered.
     *
     * @return bool
     */
    public static function isActive(): bool
    {
        return post_type_exists(self::POST_TYPE);
    }

    /**
     * Whether the plugin's global player is on this page, so [data-ars-play] buttons work.
     *
     * Classic templates run after wp_head, so the enqueue decision is already made.
     *
     * @return bool
     */
    public static function playerActive(): bool
    {
        return self::isActive() && wp_script_is(self::PLAYER_HANDLE, 'enqueued');
    }

    /**
     * Resolve one of the plugin's classes, e.g. 'Settings\PodcastSetting'.
     *
     * @param string $name Class name relative to the plugin's Jiejia\ARippleSong\ namespace.
     * @return class-string|null Null when the plugin is inactive or the class is missing.
     */
    private static function pluginClass(string $name): ?string
    {
        if (! self::isActive()) {
            return null;
        }

        $class = '\\Jiejia\\ARippleSong\\' . $name;

        return class_exists($class) ? $class : null;
    }

    /**
     * Read a value of the plugin's podcast settings (title, cover, description, ...).
     *
     * @param string $key Setting key.
     * @return string Empty when the plugin or the value is missing.
     */
    public static function setting(string $key): string
    {
        $class = self::pluginClass('Settings\\PodcastSetting');

        if ($class === null) {
            return '';
        }

        $value = (new $class())->value($key);

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Return the podcast RSS feed, or the blog feed without the plugin.
     *
     * @return string
     */
    public static function feedUrl(): string
    {
        $class = self::pluginClass('Feeds\\PodcastFeed');

        return $class !== null ? (string) $class::url() : (string) get_feed_link();
    }

    /**
     * Where to listen: the platforms filled in under the plugin's Subscription Settings (in that
     * order), then the RSS feed. Without the plugin, only the site feed.
     *
     * @return array<int,array{label:string,url:string,icon:string}>
     */
    public static function listenLinks(): array
    {
        $class = self::pluginClass('Settings\\SubscriptionSetting');
        $links = [];

        if ($class !== null && method_exists($class, 'links')) {
            foreach ((new $class())->links() as $link) {
                $url = esc_url_raw((string) ($link['url'] ?? ''));

                if ($url !== '') {
                    $links[] = ['label' => (string) $link['label'], 'url' => $url, 'icon' => self::platformIcon((string) $link['key'])];
                }
            }
        }

        $links[] = ['label' => __('RSS feed', 'ars-petal'), 'url' => self::feedUrl(), 'icon' => 'rss'];

        return $links;
    }

    /**
     * A Lucide icon for a platform key; Lucide has no brand icons, so platforms share one per kind.
     *
     * @param string $key Platform key from the plugin.
     * @return string
     */
    private static function platformIcon(string $key): string
    {
        return match ($key) {
            'youtube', 'bilibili' => 'tv',
            'spotify', 'youtube_music', 'amazon_music', 'deezer', 'pandora', 'netease_music', 'qq_music' => 'music-2',
            'iheartradio', 'tunein', 'ximalaya', 'lizhi', 'qingting' => 'radio',
            'audible' => 'book-audio',
            'xiaoyuzhou' => 'orbit',
            'podcast_index', 'podchaser', 'pod_link' => 'link',
            default => 'podcast',
        };
    }

    /**
     * Return the episode archive URL, or '' without the plugin.
     *
     * @return string
     */
    public static function archiveUrl(): string
    {
        return self::isActive() ? (string) get_post_type_archive_link(self::POST_TYPE) : '';
    }

    /**
     * Return the newest published episode.
     *
     * @return Episode|null
     */
    public static function latest(): ?Episode
    {
        if (! self::isActive()) {
            return null;
        }

        $posts = get_posts(['post_type' => self::POST_TYPE, 'numberposts' => 1, 'post_status' => 'publish']);

        return $posts !== [] ? new Episode($posts[0]) : null;
    }

    /**
     * The most played published episodes, newest first among equal counts.
     *
     * @param int $limit Number of episodes.
     * @return array<int,Episode>
     */
    public static function popular(int $limit): array
    {
        if (! self::isActive()) {
            return [];
        }

        $key = '_' . self::POST_TYPE . '_play_count';
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => $limit,
            // An episode that was never played has no count yet; it still takes part, last.
            'meta_query' => [
                'relation' => 'OR',
                'plays' => ['key' => $key, 'type' => 'NUMERIC', 'compare' => 'EXISTS'],
                ['key' => $key, 'compare' => 'NOT EXISTS'],
            ],
            'orderby' => ['plays' => 'DESC', 'date' => 'DESC'],
        ]);

        return array_map(static fn (\WP_Post $post): Episode => new Episode($post), $posts);
    }

    /**
     * IDs of the published episodes a user hosts or guests on, newest first.
     *
     * The plugin stores members and guests as serialized ID arrays, so a LIKE narrows the query
     * and each match is checked exactly (i:1; also matches array keys).
     *
     * @param int $userId User ID.
     * @return array<int,int>
     */
    public static function episodeIdsOf(int $userId): array
    {
        static $cache = [];

        if (! self::isActive() || $userId <= 0) {
            return [];
        }

        if (isset($cache[$userId])) {
            return $cache[$userId];
        }

        $needle = 'i:' . $userId . ';';
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [
                'relation' => 'OR',
                ['key' => '_' . self::POST_TYPE . '_members', 'value' => $needle, 'compare' => 'LIKE'],
                ['key' => '_' . self::POST_TYPE . '_guests', 'value' => $needle, 'compare' => 'LIKE'],
            ],
        ]);

        $ids = [];

        foreach ($posts as $post) {
            $episode = new Episode($post);
            $people = array_map(static fn (\WP_User $user): int => $user->ID, array_merge($episode->members(), $episode->guests()));

            if (in_array($userId, $people, true)) {
                $ids[] = $post->ID;
            }
        }

        return $cache[$userId] = $ids;
    }

    /**
     * Show numbers for the home page: published episodes, average duration in seconds and total plays.
     *
     * @return array{episodes:int,duration:int,plays:int}
     */
    public static function stats(): array
    {
        static $stats = null;

        if (! self::isActive()) {
            return ['episodes' => 0, 'duration' => 0, 'plays' => 0];
        }

        if ($stats !== null) {
            return $stats;
        }

        global $wpdb;
        $sum = static fn (string $key, string $fn): float => (float) $wpdb->get_var($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $fn is AVG or SUM from this method.
            "SELECT {$fn}(CAST(m.meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} m INNER JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE p.post_type = %s AND p.post_status = 'publish' AND m.meta_key = %s AND m.meta_value > 0",
            self::POST_TYPE,
            '_' . self::POST_TYPE . '_' . $key
        ));

        return $stats = [
            'episodes' => (int) (wp_count_posts(self::POST_TYPE)->publish ?? 0),
            'duration' => (int) round($sum('duration', 'AVG')),
            'plays' => (int) $sum('play_count', 'SUM'),
        ];
    }

    /**
     * Return the tags used by episodes with their episode counts, most used first.
     *
     * Tag counts in WordPress include blog posts, so episodes are counted here and cached
     * until an episode changes.
     *
     * @return array<int,array{term:\WP_Term,count:int}>
     */
    public static function tags(): array
    {
        if (! self::isActive()) {
            return [];
        }

        $cached = get_transient(self::TAGS_CACHE);

        if (! is_array($cached)) {
            $ids = get_posts(['post_type' => self::POST_TYPE, 'numberposts' => -1, 'post_status' => 'publish', 'fields' => 'ids']);
            $terms = $ids === [] ? [] : wp_get_object_terms($ids, 'post_tag', ['fields' => 'all_with_object_id']);
            $cached = [];

            foreach (is_array($terms) ? $terms : [] as $term) {
                $cached[$term->term_id] = ($cached[$term->term_id] ?? 0) + 1;
            }

            arsort($cached);
            set_transient(self::TAGS_CACHE, $cached, DAY_IN_SECONDS);
        }

        $tags = [];

        foreach ($cached as $termId => $count) {
            $term = get_term((int) $termId, 'post_tag');

            if ($term instanceof \WP_Term) {
                $tags[] = ['term' => $term, 'count' => (int) $count];
            }
        }

        return $tags;
    }

    /**
     * Forget the cached tag counts.
     *
     * @return void
     */
    public static function flushTags(): void
    {
        delete_transient(self::TAGS_CACHE);
    }

    /**
     * The people behind the site: the hosts (members) of recent episodes with the plugin,
     * the authors of published posts without it.
     *
     * @return array<int,\WP_User>
     */
    public static function hosts(): array
    {
        if (! self::isActive()) {
            return get_users(['has_published_posts' => ['post'], 'number' => 6, 'orderby' => 'post_count', 'order' => 'DESC']);
        }

        $hosts = [];

        foreach (get_posts(['post_type' => self::POST_TYPE, 'numberposts' => 50, 'post_status' => 'publish']) as $post) {
            foreach ((new Episode($post))->members() as $user) {
                $hosts[$user->ID] = $user;
            }
        }

        return array_values($hosts);
    }

    /**
     * Return the URL of the episode archive filtered by a tag.
     *
     * @param \WP_Term $term Tag.
     * @return string
     */
    public static function tagUrl(\WP_Term $term): string
    {
        return (string) add_query_arg('tag', $term->slug, self::archiveUrl());
    }
}
