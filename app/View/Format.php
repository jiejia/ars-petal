<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

/**
 * Formats dates, durations and reading times the way the design shows them.
 */
final class Format
{
    /**
     * Publish date as 2026.10.21.
     *
     * @param int|\WP_Post|null $post Post.
     * @return string
     */
    public static function date(int|\WP_Post|null $post = null): string
    {
        return (string) get_the_date('Y.m.d', $post);
    }

    /**
     * Where a "Read more" link goes: the post, at its <!--more--> tag when it has one (the_content()
     * prints the matching #more-{ID} anchor there).
     *
     * @param \WP_Post $post Post.
     * @return string
     */
    public static function readMoreUrl(\WP_Post $post): string
    {
        $url = (string) get_permalink($post);

        return str_contains($post->post_content, '<!--more') ? $url . '#more-' . $post->ID : $url;
    }

    /**
     * Duration as 14:12 or 1:02:03.
     *
     * @param int $seconds Seconds.
     * @return string
     */
    public static function duration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $time = sprintf('%02d:%02d', intdiv($seconds % 3600, 60), $seconds % 60);

        return $hours > 0 ? $hours . ':' . $time : $time;
    }

    /**
     * Reading time in minutes: 400 CJK characters or 250 words a minute.
     *
     * @param int|\WP_Post|null $post Post.
     * @return int
     */
    public static function readingMinutes(int|\WP_Post|null $post = null): int
    {
        $text = wp_strip_all_tags((string) get_post_field('post_content', $post));
        $cjk = (int) preg_match_all('/[\x{3040}-\x{30ff}\x{3400}-\x{9fff}\x{ac00}-\x{d7af}]/u', $text);
        $words = str_word_count((string) preg_replace('/[\x{3040}-\x{30ff}\x{3400}-\x{9fff}\x{ac00}-\x{d7af}]/u', ' ', $text));

        return max(1, (int) ceil($cjk / 400 + $words / 250));
    }

    /**
     * A duration in whole minutes, e.g. "12 min".
     *
     * @param int $seconds Seconds.
     * @return string
     */
    public static function minutes(int $seconds): string
    {
        $minutes = max(1, (int) round($seconds / 60));

        /* translators: %d: minutes. */
        return sprintf(_n('%d min', '%d min', $minutes, 'ars-petal'), $minutes);
    }

    /**
     * A big number for the stats row: 86,420 stays exact, 1,234,567 becomes 1.2M.
     *
     * @param int $number Number.
     * @return string
     */
    public static function compact(int $number): string
    {
        return match (true) {
            $number >= 1000000 => rtrim(rtrim(number_format_i18n($number / 1000000, 1), '0'), '.,') . 'M',
            default => number_format_i18n($number),
        };
    }

    /**
     * The first character of a name, for letter avatars.
     *
     * @param string $name Name.
     * @return string
     */
    public static function initial(string $name): string
    {
        return mb_strtoupper(mb_substr(trim($name), 0, 1));
    }
}
