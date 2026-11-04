<?php

namespace Jiejia\ArsPetal\Providers;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Abstracts\ServiceProviderAbstract;
use Jiejia\ArsPetal\Podcast\Integration;

/**
 * Registers the A Ripple Song integration: episode archive sorting and the theme's own episode header.
 */
class PodcastServiceProvider extends ServiceProviderAbstract
{
    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        (new Integration())->register();
    }
}
