<?php

namespace Jiejia\ArsPetal\Podcast;

use Jiejia\ArsPetal\Theme;

defined('ABSPATH') || exit;

/**
 * The theme's stand-in covers (resources/assets/images/covers) for episodes without their own.
 *
 * Picked by post ID rather than at random per request, so an episode keeps the same cover on every
 * page, in the player and between visits. show.* is the podcast cover and is left out.
 */
final class Covers
{
    private const DIRECTORY = 'resources/assets/images/covers/';

    private const TYPES = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'avif'];

    /**
     * Cover file names, sorted.
     *
     * @var array<int,string>|null
     */
    private static ?array $files = null;

    /**
     * The stand-in cover URL for an episode, or '' when the folder is empty.
     *
     * @param int $postId Episode post ID.
     * @return string
     */
    public static function for(int $postId): string
    {
        $files = self::files();

        return $files === [] ? '' : Theme::URL . self::DIRECTORY . $files[$postId % count($files)];
    }

    /**
     * List the cover images once per request.
     *
     * @return array<int,string>
     */
    private static function files(): array
    {
        if (self::$files === null) {
            // No GLOB_BRACE: it is missing on some systems (Alpine / musl).
            $names = array_map('basename', glob(Theme::DIR . self::DIRECTORY . '*.*') ?: []);
            self::$files = array_values(array_filter(
                $names,
                static fn (string $name): bool => ! str_starts_with($name, 'show.')
                    && in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), self::TYPES, true)
            ));
            sort(self::$files);
        }

        return self::$files;
    }
}
