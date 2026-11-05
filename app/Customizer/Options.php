<?php

namespace Jiejia\ArsPetal\Customizer;

defined('ABSPATH') || exit;

/**
 * Typed access to the theme's Customizer options (theme mods).
 */
final class Options
{
    /**
     * Option defaults, theme mod name => value.
     */
    public const DEFAULTS = [
        'color_scheme' => 'light',
        'headline_strong' => '',
        'headline_light' => '',
        'newsletter_action' => '',
        'newsletter_field' => 'email',
        'newsletter_title' => '',
        'newsletter_text' => '',
        'footer_text' => '',
    ];

    /**
     * Read an option.
     *
     * @param string $name Option name from DEFAULTS.
     * @return string
     */
    public static function get(string $name): string
    {
        return trim((string) get_theme_mod($name, self::DEFAULTS[$name] ?? ''));
    }

    /**
     * Default color scheme: light, dark or system.
     *
     * @return string
     */
    public static function scheme(): string
    {
        $scheme = self::get('color_scheme');

        return in_array($scheme, ['dark', 'light', 'system'], true) ? $scheme : 'light';
    }
}
