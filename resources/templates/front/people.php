<?php
/**
 * Home: the hosts and the guests of recent episodes, side by side. Prints nothing when there are neither.
 *
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
?>
<section class="mt-16 grid gap-5 sm:grid-cols-2" aria-label="<?php esc_attr_e('Hosts and guests', 'ars-petal'); ?>">
  <?php Template::render('parts/people', ['title' => __('Hosts', 'ars-petal'), 'users' => $hosts, 'class' => 'px-8 py-7', 'heading' => 'text-2xl sm:text-3xl !font-black', 'size' => 'size-16', 'letter' => 'text-2xl']); ?>
  <?php Template::render('parts/people', ['title' => __('Guests', 'ars-petal'), 'users' => $guests, 'class' => 'px-8 py-7', 'heading' => 'text-2xl sm:text-3xl !font-black', 'size' => 'size-16', 'letter' => 'text-2xl']); ?>
</section>
