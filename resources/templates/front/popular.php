<?php
/**
 * Home: the most played episodes as a list of episode rows in one panel (cover, title, topic / duration / date,
 * favorite and play), like the archive's list view.
 *
 * @var array<int,\Jiejia\ArsPetal\Podcast\Episode> $episodes Episodes, most played first.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

if ($episodes === []) {
    return;
}
?>
<section class="mt-16" aria-labelledby="popular-title">
  <?php Template::render('front/heading', ['id' => 'popular-title', 'strong' => _x('Popular', 'home heading, bold part', 'ars-petal'), 'light' => _x('episodes', 'home heading, light part', 'ars-petal')]); ?>
  <ol class="panel mt-6 p-1.5 sm:p-4">
    <?php foreach ($episodes as $episode) : ?>
      <?php Template::render('cards/episode-row', ['episode' => $episode]); ?>
    <?php endforeach; ?>
  </ol>
</section>
