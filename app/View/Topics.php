<?php

namespace Jiejia\ArsPetal\View;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Theme;

/**
 * The design gives every topic its own color block (and a white artwork on the large topic cards).
 * Episode tags and blog categories get them by rank, most used first, so a topic keeps its color on
 * every page: badges, swatch tiles, topic cards and sidebar lists.
 */
final class Topics
{
    /**
     * daisyUI color names in the order the design hands them out.
     */
    private const COLORS = ['secondary', 'primary', 'accent', 'warning', 'info', 'success', 'error'];

    /**
     * Generic audio icons for the swatch tiles (Lucide has no per-topic icons for user content).
     */
    private const ICONS = ['sparkles', 'mic', 'headphones', 'radio', 'music-2', 'orbit', 'podcast', 'book-audio'];

    /**
     * White artworks of the large topic cards, resources/assets/images/cat-*.svg.
     */
    private const IMAGES = ['cat-design.svg', 'cat-tech.svg', 'cat-city.svg', 'cat-talk.svg'];

    /**
     * The color of a tag or category, e.g. "secondary".
     *
     * @param \WP_Term $term Episode tag or blog category.
     * @return string
     */
    public static function color(\WP_Term $term): string
    {
        return self::COLORS[self::rank($term) % count(self::COLORS)];
    }

    /**
     * The swatch tile icon of a tag or category.
     *
     * @param \WP_Term $term Term.
     * @return string
     */
    public static function icon(\WP_Term $term): string
    {
        return self::ICONS[self::rank($term) % count(self::ICONS)];
    }

    /**
     * The URL of the artwork of a tag or category.
     *
     * @param \WP_Term $term Term.
     * @return string
     */
    public static function image(\WP_Term $term): string
    {
        return Theme::URL . 'resources/assets/images/' . self::IMAGES[self::rank($term) % count(self::IMAGES)];
    }

    /**
     * Daisy UI classes for a solid block in the topic color, e.g. "bg-secondary text-secondary-content".
     *
     * Written out in full so Tailwind finds every class in this file.
     *
     * @param \WP_Term $term Term.
     * @return string
     */
    public static function block(\WP_Term $term): string
    {
        return match (self::color($term)) {
            'secondary' => 'bg-secondary text-secondary-content',
            'primary' => 'bg-primary text-primary-content',
            'accent' => 'bg-accent text-accent-content',
            'warning' => 'bg-warning text-warning-content',
            'info' => 'bg-info text-info-content',
            'success' => 'bg-success text-success-content',
            default => 'bg-error text-error-content',
        };
    }

    /**
     * A soft daisyUI badge in the topic color.
     *
     * @param \WP_Term $term Term.
     * @return string
     */
    public static function badge(\WP_Term $term): string
    {
        return match (self::color($term)) {
            'secondary' => 'badge-secondary',
            'primary' => 'badge-primary',
            'accent' => 'badge-accent',
            'warning' => 'badge-warning',
            'info' => 'badge-info',
            'success' => 'badge-success',
            default => 'badge-error',
        };
    }

    /**
     * Place of a term among its siblings, most used first.
     *
     * @param \WP_Term $term Term.
     * @return int
     */
    private static function rank(\WP_Term $term): int
    {
        static $ranks = [];

        if (! isset($ranks[$term->taxonomy])) {
            $terms = $term->taxonomy === 'post_tag' && Podcast::isActive()
                ? array_map(static fn (array $tag): \WP_Term => $tag['term'], Podcast::tags())
                : get_terms(['taxonomy' => $term->taxonomy, 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => false]);
            $ranks[$term->taxonomy] = is_array($terms) ? array_flip(array_map(static fn (\WP_Term $item): int => $item->term_id, $terms)) : [];
        }

        return $ranks[$term->taxonomy][$term->term_id] ?? $term->term_id;
    }
}
