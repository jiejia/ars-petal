<?php
/**
 * Home widget: episodes as a list of episode rows in one panel (cover, title, topic / duration / date,
 * favorite and play), like the archive's list view.
 *
 * @var array<int,\Jiejia\ArsPetal\Podcast\Episode> $episodes Episodes.
 * @var string $id Heading ID, unique per widget.
 * @var string $strong Bold part of the heading.
 * @var string $light Light part of the heading.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

if ($episodes === []) {
    return;
}
?>
<section aria-labelledby="<?php echo esc_attr($id); ?>">
  <?php Template::render('front/heading', ['id' => $id, 'strong' => $strong, 'light' => $light]); ?>
  <ol class="panel mt-6 p-1.5 sm:p-4">
    <?php foreach ($episodes as $episode) : ?>
      <?php Template::render('cards/episode-row', ['episode' => $episode]); ?>
    <?php endforeach; ?>
  </ol>
</section>
