<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * The banner carousel: the featured episodes, the latest ones, or images with links.
 */
final class HeroWidget extends Widget
{
    public const ID = 'ars_petal_hero';

    public const FEATURED = 'featured';

    public const LATEST = 'latest';

    public const CUSTOM = 'custom';

    /**
     * Banner slots of the "custom" source.
     */
    private const SLOTS = 5;

    private const MAX_COUNT = 10;

    public function __construct()
    {
        parent::__construct(self::ID, __('Petal: Banners', 'ars-petal'), [
            'classname' => 'ars-petal-hero',
            'description' => __('A carousel of featured episodes, the latest episodes, or your own images.', 'ars-petal'),
        ]);
    }

    public static function defaults(): array
    {
        return ['source' => self::FEATURED, 'count' => 3, 'slides' => []];
    }

    protected function render(array $instance): void
    {
        $source = (string) $instance['source'];

        if ($source === self::CUSTOM) {
            $items = $this->slides((array) $instance['slides']);
            $featured = false;
        } else {
            [$items, $featured] = $this->entries($source, max(1, min(self::MAX_COUNT, (int) $instance['count'])));
        }

        Template::render('front/hero', ['items' => $items, 'featured' => $featured]);
    }

    /**
     * The featured entries, else the latest ones: episodes while the site has any, posts otherwise.
     *
     * @param string $source FEATURED or LATEST.
     * @param int $count Number of latest entries.
     * @return array{0:array<int,mixed>,1:bool} The entries and whether they are the featured ones.
     */
    private function entries(string $source, int $count): array
    {
        if (Podcast::latest() !== null) {
            $featured = $source === self::FEATURED ? Podcast::featured() : [];

            return [$featured ?: Podcast::recent($count), $featured !== []];
        }

        // Sticky posts are WordPress's own "featured" for posts.
        $sticky = array_filter(array_map('intval', (array) get_option('sticky_posts', [])));
        $featured = $source === self::FEATURED && $sticky !== [] ? get_posts(['post__in' => $sticky, 'numberposts' => -1, 'ignore_sticky_posts' => true]) : [];

        return [$featured ?: get_posts(['numberposts' => $count]), $featured !== []];
    }

    /**
     * The custom banners that have an image.
     *
     * @param array<int,array<string,string>> $slides Saved slides.
     * @return array<int,array{image:string,url:string,alt:string}>
     */
    private function slides(array $slides): array
    {
        $items = [];

        foreach ($slides as $slide) {
            if (! is_array($slide) || empty($slide['image'])) {
                continue;
            }

            $items[] = ['image' => (string) $slide['image'], 'url' => (string) ($slide['url'] ?? ''), 'alt' => (string) ($slide['alt'] ?? '')];
        }

        return $items;
    }

    public function update($new, $old): array
    {
        $source = (string) ($new['source'] ?? '');
        $slides = [];

        for ($i = 0; $i < self::SLOTS; $i++) {
            $slide = (array) ($new['slides'][$i] ?? []);
            $slides[] = [
                'image' => esc_url_raw((string) ($slide['image'] ?? '')),
                'url' => esc_url_raw((string) ($slide['url'] ?? '')),
                'alt' => sanitize_text_field((string) ($slide['alt'] ?? '')),
            ];
        }

        return [
            'source' => in_array($source, [self::FEATURED, self::LATEST, self::CUSTOM], true) ? $source : self::FEATURED,
            'count' => max(1, min(self::MAX_COUNT, absint($new['count'] ?? 3))),
            'slides' => $slides,
        ];
    }

    public function form($instance): string
    {
        $instance = wp_parse_args($instance, self::defaults());
        $slides = array_pad(array_values((array) $instance['slides']), self::SLOTS, []);

        $this->selectField('source', __('Show', 'ars-petal'), (string) $instance['source'], [
            self::FEATURED => __('Featured episodes (the latest three when none is featured)', 'ars-petal'),
            self::LATEST => __('Latest episodes', 'ars-petal'),
            self::CUSTOM => __('Custom banners (image and link)', 'ars-petal'),
        ]);
        $this->numberField('count', __('Number of latest episodes', 'ars-petal'), (int) $instance['count'], 1, self::MAX_COUNT);
        ?>
        <details<?php echo $instance['source'] === self::CUSTOM ? ' open' : ''; ?>>
          <summary><?php esc_html_e('Custom banners', 'ars-petal'); ?></summary>
          <p class="description"><?php esc_html_e('Used when "Show" is set to "Custom banners". Slots without an image are skipped.', 'ars-petal'); ?></p>
          <?php foreach ($slides as $i => $slide) : ?>
            <fieldset class="ars-petal-slide" style="margin:.6rem 0;padding:.5rem .6rem;border:1px solid #c3c4c7">
              <legend><?php echo esc_html(sprintf(/* translators: %d: banner number. */ __('Banner %d', 'ars-petal'), $i + 1)); ?></legend>
              <p>
                <input class="widefat" type="url" name="<?php echo esc_attr($this->get_field_name('slides')); ?>[<?php echo (int) $i; ?>][image]" value="<?php echo esc_attr((string) ($slide['image'] ?? '')); ?>" placeholder="<?php esc_attr_e('Image URL', 'ars-petal'); ?>" data-ars-petal-image>
                <button type="button" class="button" data-ars-petal-pick><?php esc_html_e('Select image', 'ars-petal'); ?></button>
              </p>
              <p><input class="widefat" type="url" name="<?php echo esc_attr($this->get_field_name('slides')); ?>[<?php echo (int) $i; ?>][url]" value="<?php echo esc_attr((string) ($slide['url'] ?? '')); ?>" placeholder="<?php esc_attr_e('Link URL', 'ars-petal'); ?>"></p>
              <p><input class="widefat" type="text" name="<?php echo esc_attr($this->get_field_name('slides')); ?>[<?php echo (int) $i; ?>][alt]" value="<?php echo esc_attr((string) ($slide['alt'] ?? '')); ?>" placeholder="<?php esc_attr_e('Alt text', 'ars-petal'); ?>" data-ars-petal-alt></p>
            </fieldset>
          <?php endforeach; ?>
        </details>
        <?php

        return '';
    }
}
