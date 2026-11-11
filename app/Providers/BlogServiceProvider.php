<?php

namespace Jiejia\ArsPetal\Providers;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Abstracts\ServiceProviderAbstract;
use Jiejia\ArsPetal\Blog\Blog;

/**
 * Registers the post list sort order (?sort=old).
 */
class BlogServiceProvider extends ServiceProviderAbstract
{
    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        (new Blog())->register();
    }
}
