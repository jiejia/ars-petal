<?php
/**
 * Episode archive (A Ripple Song), also used for episode category pages: episode categories and tags (?tag=) in the left sidebar; a title bar with the sort
 * order (?sort=) and the list / grid toggle; episodes grouped by month (list) or as cards (grid); pages.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Integration;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\View\Archive;
use Jiejia\ArsPetal\View\Topics;

TemplateLoader::part('header');

// On an episode category page, the sort and topic links stay inside the category.
$category = is_tax(Podcast::CATEGORY) ? get_queried_object() : null;
$category = $category instanceof \WP_Term ? $category : null;
$archive = $category ? Podcast::categoryUrl($category) : Podcast::archiveUrl();
$current = sanitize_title((string) get_query_var('tag'));
$sort = Integration::sort();
$found = (int) $GLOBALS['wp_query']->found_posts;
$schedule = Podcast::schedule();
$url = static fn (array $args): string => (string) add_query_arg(array_filter($args + ['tag' => $current, 'sort' => $sort === 'new' ? '' : $sort]), $archive);
$tags = Podcast::tags();
$stats = Podcast::stats();
/* translators: %s: number of episodes. */
$episodes = static fn (int $count): string => sprintf(_n('%s episode', '%s episodes', $count, 'ars-petal'), number_format_i18n($count));

// "Topics" are the plugin's episode categories, as on the home page and in the menu; tags follow as chips.
$topics = [['label' => __('All', 'ars-petal'), 'meta' => $episodes($stats['episodes']), 'url' => Podcast::archiveUrl(), 'current' => $category === null && $current === '', 'icon' => 'layout-grid']];

foreach (array_slice(Podcast::categories(), 0, 8) as $item) {
    $topics[] = ['label' => $item['term']->name, 'meta' => $episodes($item['count']), 'url' => Podcast::categoryUrl($item['term']), 'current' => $category !== null && $category->term_id === $item['term']->term_id, 'image' => Podcast::categoryImage($item['term']) ?: Topics::image($item['term'])];
}

$sorts = [];

foreach (Archive::sorts() as $value => $label) {
    $sorts[] = ['label' => $label, 'url' => $url(['sort' => $value === 'new' ? '' : $value]), 'current' => $sort === $value];
}

$heading = $current !== '' ? (get_term_by('slug', $current, 'post_tag')->name ?? $current) : ($category ? $category->name : __('All episodes', 'ars-petal'));
$title = sprintf(/* translators: %s: "All episodes", a category or a topic. */ __('Episodes: %s', 'ars-petal'), $heading);
?>
<section x-data="listView('episodes', 'list')" :data-view="view" data-view="list">
  <div class="mt-2 grid grid-cols-1 gap-6 lg:grid-cols-[18rem_minmax(0,1fr)] lg:items-start lg:gap-8">
    <div class="min-w-0 space-y-3 lg:sticky lg:top-[5.5rem] lg:max-h-[calc(100vh-12rem)] lg:overflow-y-auto lg:[scrollbar-width:none]">
      <?php Template::render('parts/side-list', ['title' => __('Topics', 'ars-petal'), 'items' => $topics]); ?>
      <?php
      Template::render('parts/tags', [
          'terms' => array_map(static fn (array $tag): \WP_Term => $tag['term'], array_slice($tags, 0, 16)),
          'url' => static fn (\WP_Term $term): string => $url(['tag' => $term->slug]),
          'current' => $current,
      ]);
      ?>
    </div>

    <div class="min-w-0">
      <?php Template::render('parts/title-bar', ['title' => $title, 'count' => implode(' · ', array_filter([$episodes($found), $schedule])), 'sorts' => $sorts]); ?>

      <?php if (have_posts()) : ?>
        <?php Template::render('parts/listing', ['months' => $sort !== 'short']); ?>
        <?php Template::render('parts/pagination'); ?>
      <?php else : ?>
        <?php Template::render('parts/empty', ['message' => __('No episodes match these filters.', 'ars-petal'), 'link' => $current !== '' ? $archive : '']); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
TemplateLoader::part('footer');
