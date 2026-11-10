<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

/**
 * The avatar image of a user, only when there is one.
 *
 * Gravatar answers d=404 with a 404 when the address has no avatar. Letting the browser find that out
 * leaves a failed request in the console, so the server asks once (a HEAD request) and remembers the
 * answer. An avatar URL that is not Gravatar's (a local avatar plugin) is used as is.
 */
final class Avatar
{
    /**
     * How long an answer is remembered: an avatar found, none found, Gravatar not reachable.
     */
    private const FOUND_TTL = WEEK_IN_SECONDS;

    private const MISSING_TTL = DAY_IN_SECONDS;

    private const UNKNOWN_TTL = HOUR_IN_SECONDS;

    /**
     * URL of the user's avatar image, or '' when avatars are off or the user has none.
     *
     * @param \WP_User $user User.
     * @param int $size Size in pixels.
     * @return string
     */
    public static function url(\WP_User $user, int $size = 192): string
    {
        if (! get_option('show_avatars')) {
            return '';
        }

        $url = (string) get_avatar_url($user->ID, ['size' => $size, 'default' => '404']);

        if ($url === '') {
            return '';
        }

        $host = (string) wp_parse_url($url, PHP_URL_HOST);

        if (! str_ends_with($host, 'gravatar.com')) {
            return $url;
        }

        return self::exists($url) ? $url : '';
    }

    /**
     * Whether Gravatar has an image behind a d=404 URL; asked once and cached per address.
     *
     * @param string $url Gravatar URL with d=404.
     * @return bool
     */
    private static function exists(string $url): bool
    {
        $path = (string) wp_parse_url($url, PHP_URL_PATH);
        $key = 'ars_petal_gravatar_' . md5($path);
        $cached = get_transient($key);

        if ($cached !== false) {
            return $cached === 'yes';
        }

        $response = wp_remote_head(add_query_arg(['s' => 1, 'd' => '404'], 'https://secure.gravatar.com' . $path), ['timeout' => 3, 'redirection' => 0]);
        $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);

        if ($code === 200) {
            set_transient($key, 'yes', self::FOUND_TTL);

            return true;
        }

        set_transient($key, 'no', $code === 404 ? self::MISSING_TTL : self::UNKNOWN_TTL);

        return false;
    }
}
