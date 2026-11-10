<?php

namespace Jiejia\ArsPetal\Setup;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;

/**
 * Theme supports, menus and small front-end defaults.
 */
final class ThemeSetup
{
    /**
     * Register setup hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'supports']);
        add_filter('excerpt_length', static fn (): int => 40);
        add_filter('excerpt_more', static fn (): string => '…');
        add_filter('language_attributes', [$this, 'colorScheme']);
    }

    /**
     * Declare theme supports and menu locations.
     *
     * @return void
     */
    public function supports(): void
    {
        $GLOBALS['content_width'] = 820;

        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']);
        // Shown at 124.273 × 29.5938 px (theme.css); cropped at 2x for high-density screens, ratio locked.
        add_theme_support('custom-logo', ['height' => 59, 'width' => 249, 'flex-width' => false, 'flex-height' => false]);
        add_editor_style('public/dist/editor.css');

        register_nav_menus([
            'primary' => __('Header menu', 'ars-petal'),
            'footer' => __('Footer menu', 'ars-petal'),
        ]);
    }

    /**
     * Print the color scheme on <html>: data-theme picks the daisyUI theme, data-themes lists the
     * light and dark ones for the toggle. The head script then applies the visitor's own choice.
     *
     * @param string $output Attributes of the html element.
     * @return string
     */
    public function colorScheme(string $output): string
    {
        if (is_admin()) {
            return $output;
        }

        return $output . sprintf(' data-theme="%s" data-themes="petal,petal-dark" data-scheme-default="%s"', esc_attr(Options::scheme() === 'dark' ? 'petal-dark' : 'petal'), esc_attr(Options::scheme()));
    }
}
