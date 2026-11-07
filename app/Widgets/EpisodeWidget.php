<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Episode;
use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * Base of the episode widgets: a title, the latest, the most played or hand-picked episodes, and how many.
 */
abstract class EpisodeWidget extends Widget
{
    public const LATEST = 'latest';

    public const POPULAR = 'popular';

    public const SPECIFIC = 'specific';

    /**
     * Most episodes a widget shows.
     */
    protected const MAX_COUNT = 24;

    /**
     * How many episodes the picker of a "specific episodes" widget lists, newest first.
     */
    private const PICKER_LIMIT = 200;

    protected function available(): bool
    {
        return Podcast::isActive();
    }

    /**
     * The episodes the settings ask for.
     *
     * @param array<string,mixed> $instance Settings.
     * @return array<int,Episode>
     */
    protected function episodes(array $instance): array
    {
        $count = max(1, min(self::MAX_COUNT, (int) $instance['count']));

        return match ($instance['source']) {
            self::POPULAR => Podcast::popular($count),
            self::SPECIFIC => array_slice(Podcast::byIds((array) $instance['episodes']), 0, self::MAX_COUNT),
            default => Podcast::recent($count),
        };
    }

    /**
     * The heading for the settings: the custom title, else one that names the source.
     *
     * @param array<string,mixed> $instance Settings.
     * @return array{strong:string,light:string}
     */
    protected function heading(array $instance): array
    {
        $default = match ($instance['source']) {
            self::POPULAR => _x('Popular', 'home heading, bold part', 'ars-petal'),
            self::SPECIFIC => _x('Picked', 'home heading, bold part', 'ars-petal'),
            default => _x('Latest', 'home heading, bold part', 'ars-petal'),
        };

        return self::headingParts((string) $instance['title'], $default, _x('episodes', 'home heading, light part', 'ars-petal'));
    }

    /**
     * Sanitize the settings the episode widgets share.
     *
     * @param array<string,mixed> $new New settings.
     * @param array<string,mixed> $old Previous settings.
     * @return array<string,mixed>
     */
    public function update($new, $old): array
    {
        $defaults = static::defaults();
        $source = (string) ($new['source'] ?? $defaults['source']);

        return [
            'title' => sanitize_text_field((string) ($new['title'] ?? '')),
            'source' => in_array($source, [self::LATEST, self::POPULAR, self::SPECIFIC], true) ? $source : $defaults['source'],
            'count' => max(1, min(self::MAX_COUNT, absint($new['count'] ?? $defaults['count']))),
            'episodes' => array_values(array_unique(array_filter(array_map('absint', (array) ($new['episodes'] ?? []))))),
        ] + $this->extraSettings($new, $old);
    }

    /**
     * Sanitize the settings only one widget has.
     *
     * @param array<string,mixed> $new New settings.
     * @param array<string,mixed> $old Previous settings.
     * @return array<string,mixed>
     */
    protected function extraSettings(array $new, array $old): array
    {
        return [];
    }

    /**
     * Print the form fields the episode widgets share.
     *
     * @param array<string,mixed> $instance Settings.
     * @return void
     */
    protected function commonFields(array $instance): void
    {
        $heading = $this->heading($instance);
        $this->textField('title', __('Title', 'ars-petal'), (string) $instance['title'], trim($heading['strong'] . ' ' . $heading['light']));
        $this->selectField('source', __('Episodes', 'ars-petal'), (string) $instance['source'], [
            self::LATEST => __('Latest episodes', 'ars-petal'),
            self::POPULAR => __('Most played episodes', 'ars-petal'),
            self::SPECIFIC => __('Specific episodes', 'ars-petal'),
        ]);
        $this->numberField('count', __('Number of episodes', 'ars-petal'), (int) $instance['count'], 1, self::MAX_COUNT);

        $picked = array_map('intval', (array) $instance['episodes']);
        $posts = get_posts(['post_type' => Podcast::POST_TYPE, 'post_status' => 'publish', 'numberposts' => self::PICKER_LIMIT, 'no_found_rows' => true]);
        ?>
        <details<?php echo $instance['source'] === self::SPECIFIC ? ' open' : ''; ?>>
          <summary><?php esc_html_e('Pick the specific episodes', 'ars-petal'); ?></summary>
          <p class="description"><?php esc_html_e('Used when "Episodes" is set to "Specific episodes". They show in the order the list below has, and the number above is ignored.', 'ars-petal'); ?></p>
          <div style="max-height:11rem;overflow:auto;border:1px solid #c3c4c7;padding:.4rem .6rem;background:#fff">
            <?php foreach ($posts as $post) : ?>
              <label style="display:block;margin:.2rem 0">
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('episodes')); ?>[]" value="<?php echo (int) $post->ID; ?>"<?php checked(in_array($post->ID, $picked, true)); ?>>
                <?php echo esc_html(get_the_title($post) ?: __('(no title)', 'ars-petal')); ?>
              </label>
            <?php endforeach; ?>
          </div>
        </details>
        <?php
    }
}
