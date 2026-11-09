<?php
/**
 * Home widget: the hosts and the guests of recent episodes, side by side. Prints nothing when there are neither.
 *
 * @var array{strong:string,light:string} $hostTitle Heading of the hosts card.
 * @var array{strong:string,light:string} $guestTitle Heading of the guests card.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;

$hosts = Podcast::hosts();
$guests = Podcast::guests();

if ($hosts === [] && $guests === []) {
    return;
}

$card = [
    'class' => 'px-8 py-7',
    'heading' => 'text-2xl sm:text-3xl',
    'size' => 'size-12',
    'letter' => 'text-lg',
    'overlap' => '-ml-6',
    'lightClass' => 'max-lg:hidden',
];
?>
<section class="grid gap-5 sm:grid-cols-2" aria-label="<?php esc_attr_e('Hosts and guests', 'ars-petal'); ?>">
  <?php Template::render('parts/people', ['title' => $hostTitle['strong'], 'light' => $hostTitle['light'], 'users' => $hosts] + $card); ?>
  <?php Template::render('parts/people', ['title' => $guestTitle['strong'], 'light' => $guestTitle['light'], 'users' => $guests] + $card); ?>
</section>
