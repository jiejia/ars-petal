<?php

namespace Jiejia\ArsPetal\Podcast;

defined('ABSPATH') || exit;

/**
 * Collects the episodes whose play buttons a page prints.
 *
 * The plugin's player only knows the latest episodes; older ones (e.g. on archive page 5) are
 * sent along as JSON inside the swup container, and app.js registers them with ARippleSongPlayer.
 */
final class EpisodeRegistry
{
    /**
     * @var array<int,Episode>
     */
    private static array $episodes = [];

    /**
     * Remember an episode with a play button on this page.
     *
     * @param Episode $episode Episode.
     * @return void
     */
    public static function add(Episode $episode): void
    {
        self::$episodes[$episode->id()] = $episode;
    }

    /**
     * Print the collected episodes for app.js.
     *
     * @return void
     */
    public static function print(): void
    {
        if (self::$episodes === []) {
            return;
        }

        $data = array_values(array_map(static fn (Episode $episode): array => $episode->playerData(), self::$episodes));

        printf('<script type="application/json" data-petal-episodes>%s</script>', wp_json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
