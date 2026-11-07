<?php

namespace Jiejia\ArsPetal\Providers;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Abstracts\ServiceProviderAbstract;
use Jiejia\ArsPetal\Widgets\HomeArea;

/**
 * Registers the home page widgets and their widget area.
 */
class WidgetServiceProvider extends ServiceProviderAbstract
{
    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        (new HomeArea())->register();
    }
}
