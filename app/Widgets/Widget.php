<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

/**
 * Base of the home page widgets.
 *
 * A widget fills in its saved settings over defaults(), lets a resources/templates partial print the markup, and prints
 * nothing at all (not even its wrapper) when that partial printed nothing, e.g. a rail without episodes.
 */
abstract class Widget extends \WP_Widget
{
    /**
     * The settings a new widget starts with. The home page shows these widgets when its widget area is empty.
     *
     * @return array<string,mixed>
     */
    abstract public static function defaults(): array;

    /**
     * Print the widget's markup.
     *
     * @param array<string,mixed> $instance Settings, completed with defaults().
     * @return void
     */
    abstract protected function render(array $instance): void;

    /**
     * Whether the widget can show anything on this site, e.g. false for an episode widget without the plugin.
     *
     * @return bool
     */
    protected function available(): bool
    {
        return true;
    }

    /**
     * Print the widget.
     *
     * @param array<string,mixed> $args Widget area arguments.
     * @param array<string,mixed> $instance Saved settings.
     * @return void
     */
    public function widget($args, $instance): void
    {
        if (! $this->available()) {
            return;
        }

        ob_start();
        $this->render(wp_parse_args($instance, static::defaults()));
        $html = trim((string) ob_get_clean());

        if ($html === '') {
            return;
        }

        // The templates escape their own output; the wrapper comes from register_sidebar() / the theme.
        echo $args['before_widget'] . $html . $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * A heading ID that is unique per widget instance, for aria-labelledby.
     *
     * @return string
     */
    protected function headingId(): string
    {
        return sanitize_html_class($this->id) . '-title';
    }

    /**
     * Split a heading into its bold and its light part, as the home headings are drawn ("Latest" + "episodes").
     *
     * A custom title splits after its first word; "Hot picks" becomes "Hot" + "picks". An empty title gives the defaults.
     *
     * @param string $title Custom title, or ''.
     * @param string $strong Default bold part.
     * @param string $light Default light part.
     * @return array{strong:string,light:string}
     */
    protected static function headingParts(string $title, string $strong, string $light): array
    {
        $title = trim($title);

        if ($title === '') {
            return ['strong' => $strong, 'light' => $light];
        }

        $parts = preg_split('/\s+/u', $title, 2) ?: [$title];

        return ['strong' => $parts[0], 'light' => $parts[1] ?? ''];
    }

    /**
     * Print a text field of the widget form.
     *
     * @param string $field Setting key.
     * @param string $label Label.
     * @param string $value Current value.
     * @param string $placeholder Placeholder, e.g. the default title.
     * @return void
     */
    protected function textField(string $field, string $label, string $value, string $placeholder = ''): void
    {
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id($field)); ?>"><?php echo esc_html($label); ?></label>
          <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id($field)); ?>" name="<?php echo esc_attr($this->get_field_name($field)); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
        </p>
        <?php
    }

    /**
     * Print a select field of the widget form.
     *
     * @param string $field Setting key.
     * @param string $label Label.
     * @param string $value Current value.
     * @param array<string,string> $options Value => label.
     * @return void
     */
    protected function selectField(string $field, string $label, string $value, array $options): void
    {
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id($field)); ?>"><?php echo esc_html($label); ?></label>
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id($field)); ?>" name="<?php echo esc_attr($this->get_field_name($field)); ?>">
            <?php foreach ($options as $key => $text) : ?>
              <option value="<?php echo esc_attr($key); ?>"<?php selected($value, $key); ?>><?php echo esc_html($text); ?></option>
            <?php endforeach; ?>
          </select>
        </p>
        <?php
    }

    /**
     * Print a number field of the widget form.
     *
     * @param string $field Setting key.
     * @param string $label Label.
     * @param int $value Current value.
     * @param int $min Smallest value.
     * @param int $max Largest value.
     * @return void
     */
    protected function numberField(string $field, string $label, int $value, int $min, int $max): void
    {
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id($field)); ?>"><?php echo esc_html($label); ?></label>
          <input class="tiny-text" type="number" step="1" min="<?php echo (int) $min; ?>" max="<?php echo (int) $max; ?>" id="<?php echo esc_attr($this->get_field_id($field)); ?>" name="<?php echo esc_attr($this->get_field_name($field)); ?>" value="<?php echo (int) $value; ?>">
        </p>
        <?php
    }

    /**
     * Print a checkbox field of the widget form.
     *
     * @param string $field Setting key.
     * @param string $label Label.
     * @param bool $checked Current value.
     * @return void
     */
    protected function checkboxField(string $field, string $label, bool $checked): void
    {
        ?>
        <p>
          <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id($field)); ?>" name="<?php echo esc_attr($this->get_field_name($field)); ?>" value="1"<?php checked($checked); ?>>
          <label for="<?php echo esc_attr($this->get_field_id($field)); ?>"><?php echo esc_html($label); ?></label>
        </p>
        <?php
    }
}
