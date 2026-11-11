<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Theme;

/**
 * The widget area at the top of the home page.
 *
 * While nothing is added to it, the home page shows the theme's own four widgets with their default settings: the banners,
 * the hosts and guests, the latest episodes as a rail and the most played ones as a list. Adding any widget replaces them.
 */
final class HomeArea
{
    public const ID = 'ars-petal-home-top';

    /**
     * Widget classes, in the order the default layout shows them.
     */
    private const WIDGETS = [HeroWidget::class, PeopleWidget::class, RailWidget::class, ListWidget::class];

    /**
     * Space between the widgets, as the home page sections have always been spaced; the first one sits close to the header.
     */
    private const WRAPPER = 'mt-16 first:mt-2';

    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('widgets_init', [$this, 'registerWidgets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin']);
        add_action('customize_controls_enqueue_scripts', [$this, 'enqueueAdmin']);
    }

    /**
     * Register the widget area and the widgets.
     *
     * @return void
     */
    public function registerWidgets(): void
    {
        foreach (self::WIDGETS as $class) {
            register_widget($class);
        }

        register_sidebar([
            'id' => self::ID,
            'name' => __('Home Top', 'ars-petal'),
            'description' => __('The top of the Landing page template. Leave it empty to show the banners, hosts and guests, latest episodes and popular episodes.', 'ars-petal'),
            'before_widget' => '<div id="%1$s" class="widget %2$s ' . self::WRAPPER . '">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="display mb-4 text-2xl">',
            'after_title' => '</h2>',
        ]);
    }

    /**
     * Print the area: the widgets added to it, else the default ones.
     *
     * @return void
     */
    public static function render(): void
    {
        echo '<div class="ars-petal-home-top">';

        if (is_active_sidebar(self::ID)) {
            dynamic_sidebar(self::ID);
        } else {
            // Without episodes the home page is a blog's: the banners, then the recent posts it prints itself.
            $defaults = Podcast::latest() !== null ? self::WIDGETS : [HeroWidget::class];

            foreach ($defaults as $class) {
                the_widget($class, $class::defaults(), ['before_widget' => '<div class="widget %s ' . self::WRAPPER . '">', 'after_widget' => '</div>']);
            }
        }

        echo '</div>';
    }

    /**
     * The "Select image" button of the banner widget: opens the media library and fills the image URL and alt text.
     *
     * @param string $hook Admin page, or '' in the Customizer.
     * @return void
     */
    public function enqueueAdmin(string $hook = ''): void
    {
        if ($hook !== '' && $hook !== 'widgets.php' && $hook !== 'customize.php') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(Theme::PREFIX . 'widgets', Theme::URL . 'resources/assets/admin/widgets.js', ['jquery', 'media-editor'], Theme::assetVersion('resources/assets/admin/widgets.js'), ['in_footer' => true]);
        wp_localize_script(Theme::PREFIX . 'widgets', 'petalWidgets', ['title' => __('Select banner image', 'ars-petal'), 'button' => __('Use this image', 'ars-petal')]);
    }
}
