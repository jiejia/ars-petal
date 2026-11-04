<?php

namespace Jiejia\ArsPetal\Setup;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Theme;

/**
 * Front-end styles and scripts: Tailwind CSS 4 + daisyUI 5 (public/dist/theme.css) and
 * Alpine.js + swup (public/dist/app.js), all built by `npm run build`.
 */
final class Assets
{
    public const HANDLE = Theme::PREFIX . 'app';

    /**
     * Google Fonts used by the design: Outfit headings (bold 800-900 mixed with light 300), Noto Sans SC as its fallback.
     */
    private const FONTS_URL = 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Noto+Sans+SC:wght@300;400;500;700;900&display=swap';

    /**
     * Register asset hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('wp_head', [$this, 'printSchemeScript'], 0);
        add_action('wp_head', [$this, 'preconnect'], 1);
        add_action('enqueue_block_editor_assets', [$this, 'editorFonts']);
    }

    /**
     * Enqueue the theme assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts versions itself.
        wp_enqueue_style(Theme::PREFIX . 'fonts', self::FONTS_URL, [], null);
        wp_enqueue_style(self::HANDLE, Theme::URL . 'public/dist/theme.css', [], Theme::assetVersion('public/dist/theme.css'));

        // Loaded on every page: with swup, a comment form can appear without a full page load.
        if (get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        wp_enqueue_script(self::HANDLE, Theme::URL . 'public/dist/app.js', [], Theme::assetVersion('public/dist/app.js'), ['in_footer' => true, 'strategy' => 'defer']);
        wp_add_inline_script(self::HANDLE, 'window.PETAL = ' . wp_json_encode($this->config()) . ';', 'before');
    }

    /**
     * Fonts inside the block editor.
     *
     * @return void
     */
    public function editorFonts(): void
    {
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts versions itself.
        wp_enqueue_style(Theme::PREFIX . 'editor-fonts', self::FONTS_URL, [], null);
    }

    /**
     * Apply the visitor's saved light / dark choice before the page paints.
     *
     * @return void
     */
    public function printSchemeScript(): void
    {
        $script = "(function(){var r=document.documentElement,t=null;try{t=localStorage.getItem('petal:theme')}catch(e){}"
            . "if(!t&&r.dataset.schemeDefault==='system'){t=matchMedia('(prefers-color-scheme: dark)').matches?'petal-dark':'petal'}"
            . "if(t==='petal'||t==='petal-dark'){r.dataset.theme=t}})();";

        wp_print_inline_script_tag($script);
    }

    /**
     * Connect to Google Fonts early.
     *
     * @return void
     */
    public function preconnect(): void
    {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }

    /**
     * Data app.js starts with.
     *
     * @return array<string,mixed>
     */
    private function config(): array
    {
        return [
            'searchUrl' => rest_url('wp/v2/search'),
            'podcast' => Podcast::isActive(),
            'postType' => Podcast::POST_TYPE,
            'i18n' => [
                'episode' => __('Episode', 'ars-petal'),
                'post' => __('Post', 'ars-petal'),
                'page' => __('Page', 'ars-petal'),
                'copied' => __('Link copied', 'ars-petal'),
                'nowPlaying' => __('Now playing', 'ars-petal'),
            ],
        ];
    }
}
