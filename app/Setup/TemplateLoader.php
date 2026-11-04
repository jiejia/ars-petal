<?php

namespace Jiejia\ArsPetal\Setup;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Theme;

/**
 * Serves the page templates from resources/templates instead of the theme root.
 *
 * WordPress looks for every template of the hierarchy (single-ars_podcast.php, single.php, ...)
 * in resources/templates first; a file in the root of a child theme still wins over nothing.
 */
final class TemplateLoader
{
    public const DIRECTORY = 'resources/templates/';

    /**
     * Template types whose "{type}_template_hierarchy" filter WordPress applies.
     */
    private const TYPES = [
        '404', 'archive', 'attachment', 'author', 'category', 'date', 'embed', 'frontpage', 'home',
        'index', 'page', 'paged', 'privacypolicy', 'search', 'single', 'singular', 'tag', 'taxonomy',
    ];

    /**
     * Page templates selectable in the editor, path => name.
     */
    public const PAGE_TEMPLATES = [
        self::DIRECTORY . 'page-about.php' => 'About the show',
    ];

    /**
     * Register template hooks.
     *
     * @return void
     */
    public function register(): void
    {
        foreach (self::TYPES as $type) {
            add_filter("{$type}_template_hierarchy", [$this, 'hierarchy']);
        }

        add_filter('theme_page_templates', [$this, 'pageTemplates']);
        add_filter('comments_template', [$this, 'commentsTemplate']);
        add_filter('get_search_form', [$this, 'searchForm']);
    }

    /**
     * Look in resources/templates before the theme root.
     *
     * @param array<int,string> $templates Template file names, most specific first.
     * @return array<int,string>
     */
    public function hierarchy(array $templates): array
    {
        $moved = [];

        foreach ($templates as $template) {
            // A page template chosen in the editor is already a path below resources/templates.
            if (! str_starts_with($template, self::DIRECTORY)) {
                $moved[] = self::DIRECTORY . $template;
            }

            $moved[] = $template;
        }

        return $moved;
    }

    /**
     * Offer the theme's page templates, which WordPress does not find this deep.
     *
     * @param array<string,string> $templates Path => name.
     * @return array<string,string>
     */
    public function pageTemplates(array $templates): array
    {
        foreach (self::PAGE_TEMPLATES as $path => $name) {
            // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Names are listed in the constant above.
            $templates[$path] = __($name, 'ars-petal');
        }

        return $templates;
    }

    /**
     * Use resources/templates/comments.php for comments_template().
     *
     * @param string $file Comments template found by WordPress.
     * @return string
     */
    public function commentsTemplate(string $file): string
    {
        $theme = locate_template(self::DIRECTORY . 'comments.php');

        return $theme !== '' ? $theme : $file;
    }

    /**
     * Use resources/templates/parts/searchform.php for get_search_form().
     *
     * @return string
     */
    public function searchForm(): string
    {
        return Template::capture('parts/searchform');
    }

    /**
     * Print a layout part (header, footer) the way get_header() / get_footer() would.
     *
     * @param string $name "header" or "footer".
     * @return void
     */
    public static function part(string $name): void
    {
        // The standard actions, so plugins hooked to get_header / get_footer still run.
        do_action("get_{$name}", null, []);
        require locate_template(self::DIRECTORY . "layouts/{$name}.php") ?: Theme::DIR . self::DIRECTORY . "layouts/{$name}.php";
    }
}
