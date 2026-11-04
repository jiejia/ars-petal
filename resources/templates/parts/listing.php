<?php
/**
 * The posts of the main query in both views: a list grouped by month (rows in a card per month) and a grid of
 * cards. The wrapper's data-view (listView in app.js) shows one of them.
 *
 * @var bool $months Whether to group the list by month (archives); search results stay in one card.
 * @var bool $badge Whether rows show the content type (mixed search results).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Archive;

$months ??= true;
$badge ??= false;
$posts = $GLOBALS['wp_query']->posts;
$groups = $months ? Archive::byMonth($posts) : [['label' => '', 'items' => $posts]];
?>
<div class="petal-view-list mt-6 space-y-6">
  <?php foreach ($groups as $group) : ?>
    <section>
      <?php if ($group['label'] !== '') : ?>
        <h2 class="display px-2 text-xl"><?php echo esc_html($group['label']); ?></h2>
      <?php endif; ?>
      <ol class="panel <?php echo $group['label'] !== '' ? 'mt-3' : ''; ?> p-3 sm:p-4">
        <?php foreach ($group['items'] as $post) : ?>
          <?php Template::render('cards/row', ['post' => $post, 'heading' => $group['label'] !== '' ? 'h3' : 'h2', 'badge' => $badge]); ?>
        <?php endforeach; ?>
      </ol>
    </section>
  <?php endforeach; ?>
</div>
<div class="petal-view-grid mt-8 grid grid-cols-1 gap-x-5 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
  <?php foreach ($posts as $post) : ?>
    <?php Template::render('cards/card', ['post' => $post, 'heading' => 'h2']); ?>
  <?php endforeach; ?>
</div>
