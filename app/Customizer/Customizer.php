<?php

namespace Jiejia\ArsPetal\Customizer;

defined('ABSPATH') || exit;

/**
 * The theme's Customizer section: color scheme, schedule, home headline, newsletter, footer.
 */
final class Customizer
{
    private const SECTION = 'ars_petal';

    /**
     * Register Customizer hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('customize_register', [$this, 'controls']);
    }

    /**
     * Add the settings and their controls.
     *
     * @param \WP_Customize_Manager $customizer Customizer.
     * @return void
     */
    public function controls(\WP_Customize_Manager $customizer): void
    {
        $customizer->add_section(self::SECTION, [
            'title' => __('Petal', 'ars-petal'),
            'priority' => 30,
        ]);

        $this->add($customizer, 'color_scheme', __('Default color scheme', 'ars-petal'), 'select', [
            'choices' => [
                'light' => __('Light (day)', 'ars-petal'),
                'dark' => __('Dark (night)', 'ars-petal'),
                'system' => __('Follow the visitor’s system', 'ars-petal'),
            ],
            'description' => __('Visitors can still switch with the sun / moon button.', 'ars-petal'),
        ]);
        $this->add($customizer, 'schedule', __('Release schedule', 'ars-petal'), 'text', [
            'description' => __('E.g. “Thursdays 21:00”. Shown in the announcement bar and the episode archive.', 'ars-petal'),
        ]);
        $this->add($customizer, 'headline_strong', __('Home headline, bold part', 'ars-petal'), 'text', [
            'description' => __('The big two-weight headline on the home page. Leave empty for the site name.', 'ars-petal'),
        ]);
        $this->add($customizer, 'headline_light', __('Home headline, light part', 'ars-petal'), 'text', [
            'description' => __('Leave empty for the tagline.', 'ars-petal'),
        ]);
        $this->add($customizer, 'statement', __('Home statement', 'ars-petal'), 'text', [
            'description' => __('One sentence above the big numbers on the home page. Leave empty to hide it.', 'ars-petal'),
        ]);
        $this->add($customizer, 'newsletter_action', __('Newsletter form action URL', 'ars-petal'), 'url', [
            'description' => __('Where the e-mail sign-up form posts to (your newsletter service). Leave empty to hide the sign-up section.', 'ars-petal'),
        ]);
        $this->add($customizer, 'newsletter_field', __('Newsletter e-mail field name', 'ars-petal'), 'text');
        $this->add($customizer, 'newsletter_title', __('Newsletter title', 'ars-petal'), 'text');
        $this->add($customizer, 'newsletter_text', __('Newsletter text', 'ars-petal'), 'textarea');
        $this->add($customizer, 'footer_text', __('Footer copyright text', 'ars-petal'), 'text', [
            'description' => __('Leave empty for “© year site name”.', 'ars-petal'),
        ]);
    }

    /**
     * Add one setting with its control.
     *
     * @param \WP_Customize_Manager $customizer Customizer.
     * @param string $name Option name from Options::DEFAULTS.
     * @param string $label Control label.
     * @param string $type Control type.
     * @param array<string,mixed> $control Extra control arguments.
     * @return void
     */
    private function add(\WP_Customize_Manager $customizer, string $name, string $label, string $type, array $control = []): void
    {
        $sanitize = match ($type) {
            'url' => 'esc_url_raw',
            'textarea' => 'sanitize_textarea_field',
            'select' => static fn (string $value): string => array_key_exists($value, $control['choices'] ?? []) ? $value : (string) Options::DEFAULTS[$name],
            default => 'sanitize_text_field',
        };

        $customizer->add_setting($name, [
            'default' => Options::DEFAULTS[$name],
            'sanitize_callback' => $sanitize,
        ]);
        $customizer->add_control($name, ['label' => $label, 'section' => self::SECTION, 'type' => $type] + $control);
    }
}
