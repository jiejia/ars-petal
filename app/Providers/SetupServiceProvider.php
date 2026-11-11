<?php

namespace Jiejia\ArsPetal\Providers;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Abstracts\ServiceProviderAbstract;
use Jiejia\ArsPetal\Setup\LandingSetup;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\Setup\ThemeSetup;

/**
 * Registers theme supports, menus, the resources/templates template loader and the landing page setup.
 */
class SetupServiceProvider extends ServiceProviderAbstract
{
    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        (new ThemeSetup())->register();
        (new TemplateLoader())->register();
        (new LandingSetup())->register();
    }
}
