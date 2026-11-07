<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

/**
 * Episodes as a horizontally scrolling rail of cards.
 */
final class RailWidget extends EpisodeWidget
{
    public const ID = 'ars_petal_rail';

    public function __construct()
    {
        parent::__construct(self::ID, __('Petal: Episode Rail', 'ars-petal'), [
            'classname' => 'ars-petal-rail',
            'description' => __('Episode cards in a row that scrolls sideways.', 'ars-petal'),
        ]);
    }

    public static function defaults(): array
    {
        return ['title' => '', 'source' => self::LATEST, 'count' => 8, 'episodes' => [], 'loop' => false];
    }

    protected function render(array $instance): void
    {
        $heading = $this->heading($instance);

        Template::render('front/rail', [
            'episodes' => $this->episodes($instance),
            'id' => $this->headingId(),
            'strong' => $heading['strong'],
            'light' => $heading['light'],
            'loop' => (bool) $instance['loop'],
        ]);
    }

    protected function extraSettings(array $new, array $old): array
    {
        return ['loop' => ! empty($new['loop'])];
    }

    public function form($instance): string
    {
        $instance = wp_parse_args($instance, self::defaults());
        $this->commonFields($instance);
        $this->checkboxField('loop', __('Loop: the arrows wrap around at either end', 'ars-petal'), (bool) $instance['loop']);

        return '';
    }
}
