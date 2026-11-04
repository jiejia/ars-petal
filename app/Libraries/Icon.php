<?php

namespace Jiejia\ArsPetal\Libraries;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Theme;

/**
 * Inline Lucide icons from resources/assets/icons (copied there by `npm run build:icons`).
 *
 * Icons are printed as SVG markup, so they need no JavaScript and survive swup page swaps.
 */
final class Icon
{
    /**
     * Loaded SVG markup keyed by icon name.
     *
     * @var array<string,string>
     */
    private static array $cache = [];

    /**
     * Return an icon as inline SVG.
     *
     * @param string $name Lucide icon name, e.g. "play".
     * @param string $class CSS classes; the icon is sized by them and colored by currentColor.
     * @return string Empty when the icon was not copied.
     */
    public static function svg(string $name, string $class = 'size-5'): string
    {
        self::$cache[$name] ??= self::load($name);

        if (self::$cache[$name] === '') {
            return '';
        }

        $attributes = Html::attributes(['class' => $class !== '' ? $class : null, 'aria-hidden' => 'true', 'focusable' => 'false']);

        return (string) preg_replace('/^<svg\b/', '<svg' . $attributes, self::$cache[$name], 1);
    }

    /**
     * Print an icon as inline SVG.
     *
     * @param string $name Lucide icon name.
     * @param string $class CSS classes.
     * @return void
     */
    public static function render(string $name, string $class = 'size-5'): void
    {
        echo self::svg($name, $class); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG shipped with the theme.
    }

    /**
     * Read an icon file, without its license comment and Lucide's own class, at the prototype's thinner stroke.
     *
     * @param string $name Lucide icon name.
     * @return string
     */
    private static function load(string $name): string
    {
        $file = Theme::DIR . 'resources/assets/icons/' . sanitize_file_name($name) . '.svg';

        if (! is_readable($file)) {
            return '';
        }

        $svg = (string) preg_replace('/<!--.*?-->/s', '', (string) file_get_contents($file));
        $svg = (string) preg_replace('/\s+class="[^"]*"/', '', $svg, 1);
        $svg = str_replace('stroke-width="2"', 'stroke-width="1.75"', $svg);

        // One line, so icons can sit inside inline text without stray whitespace.
        return trim((string) preg_replace('/\s+/', ' ', $svg));
    }
}
