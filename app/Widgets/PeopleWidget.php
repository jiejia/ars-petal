<?php

namespace Jiejia\ArsPetal\Widgets;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;

/**
 * The hosts and the guests of recent episodes, as two cards side by side.
 */
final class PeopleWidget extends Widget
{
    public const ID = 'ars_petal_people';

    public function __construct()
    {
        parent::__construct(self::ID, __('Petal: Hosts & Guests', 'ars-petal'), [
            'classname' => 'ars-petal-people',
            'description' => __('Two cards with the avatars of the hosts and of the guests.', 'ars-petal'),
        ]);
    }

    public static function defaults(): array
    {
        return ['host_title' => '', 'guest_title' => ''];
    }

    protected function available(): bool
    {
        return Podcast::isActive();
    }

    protected function render(array $instance): void
    {
        Template::render('front/people', [
            'hostTitle' => self::headingParts((string) $instance['host_title'], _x('Host', 'home heading, bold part', 'ars-petal'), _x('lineup', 'home heading, light part', 'ars-petal')),
            'guestTitle' => self::headingParts((string) $instance['guest_title'], _x('Guest', 'home heading, bold part', 'ars-petal'), _x('appearances', 'home heading, light part', 'ars-petal')),
        ]);
    }

    public function update($new, $old): array
    {
        return [
            'host_title' => sanitize_text_field((string) ($new['host_title'] ?? '')),
            'guest_title' => sanitize_text_field((string) ($new['guest_title'] ?? '')),
        ];
    }

    public function form($instance): string
    {
        $instance = wp_parse_args($instance, self::defaults());
        $this->textField('host_title', __('Hosts title', 'ars-petal'), (string) $instance['host_title'], __('Host lineup', 'ars-petal'));
        $this->textField('guest_title', __('Guests title', 'ars-petal'), (string) $instance['guest_title'], __('Guest appearances', 'ars-petal'));

        return '';
    }
}
