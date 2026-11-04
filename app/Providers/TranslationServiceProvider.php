<?php

namespace Jiejia\ArsPetal\Providers;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Abstracts\ServiceProviderAbstract;
use Jiejia\ArsPetal\Theme;

/**
 * Loads the theme translations bundled in resources/lang.
 */
class TranslationServiceProvider extends ServiceProviderAbstract
{
    /**
     * Register the translation loader hook.
     *
     * @return void
     */
    public function register(): void
    {
        // Priority 0 so the strings are ready before other after_setup_theme callbacks (menu names) use them.
        add_action('after_setup_theme', [$this, 'loadTextdomain'], 0);
    }

    /**
     * Load the theme textdomain; files in wp-content/languages/themes take precedence.
     *
     * @return void
     */
    public function loadTextdomain(): void
    {
        load_theme_textdomain(Theme::SLUG, Theme::DIR . 'resources/lang');
    }
}
