<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

/**
 * Episodes as one row each, in a single panel.
 */
final class ListWidget extends EpisodeWidget
{
    public const ID = 'ars_petal_list';

    public function __construct()
    {
        parent::__construct(self::ID, __('ARS Petal: Episode List', 'ars-petal'), [
            'classname' => 'ars-petal-list',
            'description' => __('Episodes one per row, with cover, title and a play button.', 'ars-petal'),
        ]);
    }

    public static function defaults(): array
    {
        return ['title' => '', 'source' => self::POPULAR, 'count' => 5, 'episodes' => []];
    }

    protected function render(array $instance): void
    {
        $heading = $this->heading($instance);

        Template::render('front/list', [
            'episodes' => $this->episodes($instance),
            'id' => $this->headingId(),
            'strong' => $heading['strong'],
            'light' => $heading['light'],
        ]);
    }

    public function form($instance): string
    {
        $this->commonFields(wp_parse_args($instance, self::defaults()));

        return '';
    }
}
