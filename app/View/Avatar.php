<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

/**
 * The avatar image URL of a user.
 *
 * Gravatar URLs ask for d=404, so a user without an avatar gets a failed image that parts/avatar removes,
 * leaving the letter. The visitor's browser loads the image, as with core's get_avatar(); the server
 * makes no request to Gravatar. An avatar URL that is not Gravatar's (a local avatar plugin) is used as is.
 */
final class Avatar
{
    /**
     * URL of the user's avatar image, or '' when avatars are off.
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

        return (string) get_avatar_url($user->ID, ['size' => $size, 'default' => '404']);
    }
}
