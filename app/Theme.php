<?php

namespace Jiejia\ArsPetal;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Contracts\ServiceProviderContract;
use Jiejia\ArsPetal\Providers\AssetServiceProvider;
use Jiejia\ArsPetal\Providers\BlogServiceProvider;
use Jiejia\ArsPetal\Providers\CommentServiceProvider;
use Jiejia\ArsPetal\Providers\CustomizerServiceProvider;
use Jiejia\ArsPetal\Providers\NavigationServiceProvider;
use Jiejia\ArsPetal\Providers\PodcastServiceProvider;
use Jiejia\ArsPetal\Providers\SetupServiceProvider;
use Jiejia\ArsPetal\Providers\TranslationServiceProvider;

/**
 * Main theme application class.
 */
class Theme
{
    public const SLUG = 'ars-petal';

    public const NAME = 'Petal';

    public const VERSION = '0.5.1';

    public const DIR = ARS_PETAL_DIR;

    public const URL = ARS_PETAL_URL;

    public const PREFIX = 'ars_petal_';

    /**
     * Service provider classes registered by this theme.
     *
     * @var array<int,class-string<ServiceProviderContract>>
     */
    private array $providers = [
        TranslationServiceProvider::class,
        SetupServiceProvider::class,
        CustomizerServiceProvider::class,
        AssetServiceProvider::class,
        NavigationServiceProvider::class,
        BlogServiceProvider::class,
        CommentServiceProvider::class,
        PodcastServiceProvider::class,
    ];

    /**
     * Service provider instances created during theme boot.
     *
     * @var array<int,ServiceProviderContract>
     */
    private array $providerInstances = [];

    /**
     * Return an asset version that changes whenever the file does, so edits bust the browser cache.
     *
     * @param string $path Path relative to the theme directory.
     * @return string
     */
    public static function assetVersion(string $path): string
    {
        $mtime = @filemtime(self::DIR . $path);

        return $mtime !== false ? (string) $mtime : self::VERSION;
    }

    /**
     * Create the theme application and boot its providers.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the theme lifecycle.
     *
     * @return void
     */
    public function init(): void
    {
        $this->registerProviders();
        $this->bootProviders();
    }

    /**
     * Register all configured service providers.
     *
     * @return void
     */
    private function registerProviders(): void
    {
        foreach ($this->providers as $providerClass) {
            // Keep provider instances so boot() runs on the same objects.
            $provider = new $providerClass();
            $provider->register();

            $this->providerInstances[] = $provider;
        }
    }

    /**
     * Boot all registered service providers.
     *
     * @return void
     */
    private function bootProviders(): void
    {
        foreach ($this->providerInstances as $provider) {
            // Allow providers to run logic after every provider is registered.
            $provider->boot();
        }
    }
}
