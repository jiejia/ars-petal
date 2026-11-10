<?php

namespace Jiejia\ArsPetal\Branding;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;

/**
 * The site logo: either the uploaded image (core custom logo) or a bundled icon next to the site title.
 *
 * Icons live in resources/assets/logos as flat multi-color SVGs. Their shapes use the classes
 * lp / ls / la / lw / lc / lb (fill) and *-s (stroke), which theme.css maps to the daisyUI colors,
 * so they follow night mode. "petal" is the original CSS mark and has no SVG file.
 */
final class Logo
{
    /**
     * Icon slugs in picker order.
     */
    public const ICONS = ['petal', 'wave', 'ripple', 'mic', 'play', 'headphones', 'bubble', 'vinyl', 'broadcast', 'bloom', 'spark', 'blocks'];

    /**
     * Loaded SVG markup keyed by slug.
     *
     * @var array<string,string>
     */
    private static array $cache = [];

    /**
     * Translated icon names for the Customizer picker.
     *
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return [
            'petal' => __('Petal', 'ars-petal'),
            'wave' => __('Sound wave', 'ars-petal'),
            'ripple' => __('Ripple', 'ars-petal'),
            'mic' => __('Microphone', 'ars-petal'),
            'play' => __('Play', 'ars-petal'),
            'headphones' => __('Headphones', 'ars-petal'),
            'bubble' => __('Speech bubble', 'ars-petal'),
            'vinyl' => __('Vinyl', 'ars-petal'),
            'broadcast' => __('Broadcast', 'ars-petal'),
            'bloom' => __('Bloom', 'ars-petal'),
            'spark' => __('Spark', 'ars-petal'),
            'blocks' => __('Blocks', 'ars-petal'),
        ];
    }

    /**
     * Whether the header shows the uploaded image instead of icon + title.
     *
     * @return bool
     */
    public static function usesImage(): bool
    {
        return Options::logoMode() === 'image' && has_custom_logo();
    }

    /**
     * Return an icon's markup.
     *
     * @param string $slug Icon slug from ICONS.
     * @param string $class Extra CSS classes.
     * @return string
     */
    public static function icon(string $slug, string $class = ''): string
    {
        $slug = in_array($slug, self::ICONS, true) ? $slug : 'petal';

        if ($slug === 'petal') {
            return sprintf('<span class="%s" aria-hidden="true"><i></i><i></i><i></i><i></i></span>', esc_attr(trim('mark ' . $class)));
        }

        self::$cache[$slug] ??= self::load($slug);

        return (string) preg_replace('/^<svg\b/', sprintf('<svg class="%s" aria-hidden="true" focusable="false"', esc_attr(trim('logo-icon ' . $class))), self::$cache[$slug], 1);
    }

    /**
     * Read an icon file.
     *
     * @param string $slug Icon slug.
     * @return string
     */
    private static function load(string $slug): string
    {
        $file = ARS_PETAL_DIR . 'resources/assets/logos/' . $slug . '.svg';

        return is_readable($file) ? trim((string) file_get_contents($file)) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.
    }
}
