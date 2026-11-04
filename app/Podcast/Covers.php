<?php

namespace Jiejia\ArsPetal\Podcast;

use Jiejia\ArsPetal\Theme;

defined('ABSPATH') || exit;

/**
 * The theme's stand-in covers (resources/assets/images/covers) for episodes and blog posts without their own.
 *
 * Episodes use every image except blog-* (and show.*, the podcast cover); blog posts use the blog-* images.
 * Picked by post ID rather than at random per request, so a post keeps the same cover on every page,
 * in the player and between visits.
 */
final class Covers
{
    private const DIRECTORY = 'resources/assets/images/covers/';

    private const TYPES = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'avif'];

    private const POST_PREFIX = 'blog-';

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
        return self::pick(array_values(array_filter(self::files(), static fn (string $name): bool => ! str_starts_with($name, self::POST_PREFIX))), $postId);
    }

    /**
     * The image of a blog post: its featured image, else one of the blog-* stand-ins.
     * Other post types (pages) get only their featured image.
     *
     * @param \WP_Post $post Post.
     * @param string $size Image size for the featured image.
     * @return string
     */
    public static function post(\WP_Post $post, string $size = 'medium_large'): string
    {
        $image = (string) get_the_post_thumbnail_url($post, $size);

        if ($image !== '' || $post->post_type !== 'post') {
            return $image;
        }

        return self::pick(array_values(array_filter(self::files(), static fn (string $name): bool => str_starts_with($name, self::POST_PREFIX))), $post->ID);
    }

    /**
     * One file of a list by post ID, as a URL.
     *
     * @param array<int,string> $files File names.
     * @param int $postId Post ID.
     * @return string
     */
    private static function pick(array $files, int $postId): string
    {
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
