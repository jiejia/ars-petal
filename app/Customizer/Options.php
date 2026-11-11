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
        'footer_text' => '',
        'logo_mode' => '',
        'logo_icon' => 'petal',
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

    /**
     * Logo style: "icon" (icon + site title) or "image" (the uploaded logo).
     * Never saved yet: "image" when a logo was uploaded before this option existed, so it keeps showing.
     *
     * @return string
     */
    public static function logoMode(): string
    {
        $mode = self::get('logo_mode');

        if (in_array($mode, ['icon', 'image'], true)) {
            return $mode;
        }

        return get_theme_mod('custom_logo') ? 'image' : 'icon';
    }

    /**
     * Logo icon slug from Branding\Logo::ICONS.
     *
     * @return string
     */
    public static function logoIcon(): string
    {
        $icon = self::get('logo_icon');

        return in_array($icon, \Jiejia\ArsPetal\Branding\Logo::ICONS, true) ? $icon : 'petal';
    }
}
