<?php

namespace Jiejia\ArsPetal\Setup;

defined('ABSPATH') || exit;

/**
 * Offers to make the Landing page template the home page, only with the user's consent (theme
 * directory rule 12): a dismissible notice on the Dashboard and Themes screens asks first, and only
 * its button creates the pages and changes Settings > Reading. Fresh sites also get the same setup
 * as Customizer starter content, which is applied only when the user publishes it there.
 */
final class LandingSetup
{
    private const TEMPLATE = TemplateLoader::DIRECTORY . 'page-landing.php';

    /**
     * Theme mod remembering that the notice was answered ("done" or "dismissed").
     */
    private const STATE = 'landing_notice';

    private const CREATE = 'ars_petal_landing_create';

    private const DISMISS = 'ars_petal_landing_dismiss';

    /**
     * Query arg of the redirect back to the screen the notice was on.
     */
    private const RESULT = 'ars_petal_landing';

    /**
     * Register the hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'starterContent']);
        add_action('admin_notices', [$this, 'notice']);
        add_action('admin_post_' . self::CREATE, [$this, 'create']);
        add_action('admin_post_' . self::DISMISS, [$this, 'dismiss']);
    }

    /**
     * Customizer starter content for fresh sites: a Home page with the Landing page template as the
     * static homepage, a Blog page as the posts page, and both in the header menu.
     *
     * @return void
     */
    public function starterContent(): void
    {
        add_theme_support('starter-content', [
            'posts' => [
                'home' => ['template' => self::TEMPLATE],
                'blog',
            ],
            'options' => [
                'show_on_front' => 'page',
                'page_on_front' => '{{home}}',
                'page_for_posts' => '{{blog}}',
            ],
            'nav_menus' => [
                'primary' => [
                    'name' => __('Header menu', 'ars-petal'),
                    'items' => ['page_home', 'page_blog'],
                ],
            ],
        ]);
    }

    /**
     * Whether the user may create the pages and change the reading settings.
     *
     * @return bool
     */
    private static function allowed(): bool
    {
        return current_user_can('manage_options') && current_user_can('publish_pages');
    }

    /**
     * Whether the static homepage already uses the Landing page template.
     *
     * @return bool
     */
    private static function isSetUp(): bool
    {
        $front = (int) get_option('page_on_front');

        return get_option('show_on_front') === 'page' && $front > 0 && get_page_template_slug($front) === self::TEMPLATE;
    }

    /**
     * The notice: the offer while unanswered, or the result right after the button.
     *
     * @return void
     */
    public function notice(): void
    {
        if (! self::allowed()) {
            return;
        }

        $result = isset($_GET[self::RESULT]) ? sanitize_key(wp_unslash($_GET[self::RESULT])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only picks which message to show.

        if ($result === 'created') {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s <a href="%s">%s</a> · <a href="%s">%s</a></p></div>',
                esc_html__('The landing page is now your homepage.', 'ars-petal'),
                esc_url(home_url('/')),
                esc_html__('View site', 'ars-petal'),
                esc_url(admin_url('widgets.php')),
                esc_html__('Arrange its sections', 'ars-petal')
            );

            return;
        }

        if ($result === 'failed') {
            printf('<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html__('The landing page could not be created. Please try again, or set it up under Pages and Settings > Reading.', 'ars-petal'));

            return;
        }

        $screen = get_current_screen();

        if (! $screen || ! in_array($screen->id, ['dashboard', 'themes'], true) || get_theme_mod(self::STATE, '') !== '' || self::isSetUp()) {
            return;
        }

        $postsPage = (int) get_option('page_for_posts') > 0;
        ?>
        <div class="notice notice-info">
          <p><strong><?php esc_html_e('Use the ARS Petal landing page as your homepage?', 'ars-petal'); ?></strong></p>
          <p>
            <?php
            echo esc_html(
                $postsPage
                    ? __('This creates a page named “Home” with the Landing page template and sets it as the homepage under Settings > Reading. Your posts page stays as it is.', 'ars-petal')
                    : __('This creates a page named “Home” with the Landing page template and a page named “Blog” for your posts, and sets them as the homepage and the posts page under Settings > Reading.', 'ars-petal')
            );
            ?>
          </p>
          <p>
            <a class="button button-primary" href="<?php echo esc_url(self::actionUrl(self::CREATE)); ?>"><?php esc_html_e('Create and set as homepage', 'ars-petal'); ?></a>
            <a class="button" href="<?php echo esc_url(self::actionUrl(self::DISMISS)); ?>"><?php esc_html_e('No, thanks', 'ars-petal'); ?></a>
          </p>
        </div>
        <?php
    }

    /**
     * Nonce-protected admin-post URL of a notice button.
     *
     * @param string $action CREATE or DISMISS.
     * @return string
     */
    private static function actionUrl(string $action): string
    {
        return wp_nonce_url(admin_url('admin-post.php?action=' . $action), $action);
    }

    /**
     * The "Create and set as homepage" button.
     *
     * @return void
     */
    public function create(): void
    {
        self::verify(self::CREATE);

        $home = wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => __('Home', 'ars-petal'),
            'page_template' => self::TEMPLATE,
        ], true);

        if (is_wp_error($home)) {
            self::back('failed');
        }

        $postsPage = (int) get_option('page_for_posts');

        if ($postsPage <= 0) {
            $blog = wp_insert_post([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_title' => __('Blog', 'ars-petal'),
            ], true);
            $postsPage = is_wp_error($blog) ? 0 : (int) $blog;
        }

        update_option('show_on_front', 'page');
        update_option('page_on_front', (int) $home);

        if ($postsPage > 0) {
            update_option('page_for_posts', $postsPage);
        }

        set_theme_mod(self::STATE, 'done');
        self::back('created');
    }

    /**
     * The "No, thanks" button.
     *
     * @return void
     */
    public function dismiss(): void
    {
        self::verify(self::DISMISS);
        set_theme_mod(self::STATE, 'dismissed');
        self::back('');
    }

    /**
     * Stop unless the nonce and the capabilities check out.
     *
     * @param string $action Nonce action.
     * @return void
     */
    private static function verify(string $action): void
    {
        check_admin_referer($action);

        if (! self::allowed()) {
            wp_die(esc_html__('Sorry, you are not allowed to do that.', 'ars-petal'), '', ['response' => 403]);
        }
    }

    /**
     * Return to the screen the button was on.
     *
     * @param string $result RESULT value, or '' for none.
     * @return never
     */
    private static function back(string $result): never
    {
        $url = wp_get_referer() ?: admin_url('themes.php');
        $url = remove_query_arg(self::RESULT, $url);

        wp_safe_redirect($result !== '' ? add_query_arg(self::RESULT, $result, $url) : $url);
        exit;
    }
}
