<?php

namespace Jiejia\ArsPetal\Libraries;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Theme;

/**
 * Renders the partials in resources/templates.
 *
 * A child theme overrides a partial by shipping the same path, e.g. resources/templates/cards/episode.php.
 */
final class Template
{
    private const DIRECTORY = 'resources/templates/';

    /**
     * Print a partial.
     *
     * @param string $name Partial path without extension, relative to resources/templates, e.g. "cards/episode".
     * @param array<string,mixed> $data Variables made available to the partial.
     * @return void
     */
    public static function render(string $name, array $data = []): void
    {
        $file = locate_template(self::DIRECTORY . $name . '.php');

        if ($file === '') {
            $file = Theme::DIR . self::DIRECTORY . $name . '.php';
        }

        if (! is_file($file)) {
            throw new \InvalidArgumentException(sprintf('Template "%s" does not exist.', esc_html($name)));
        }

        // A static closure gives the partial its own scope: it only sees $data.
        (static function (string $__file, array $__data): void {
            extract($__data, EXTR_SKIP);
            require $__file;
        })($file, $data);
    }

    /**
     * Return a partial's output.
     *
     * @param string $name Partial path without extension.
     * @param array<string,mixed> $data Variables made available to the partial.
     * @return string
     */
    public static function capture(string $name, array $data = []): string
    {
        ob_start();
        self::render($name, $data);

        return (string) ob_get_clean();
    }
}
