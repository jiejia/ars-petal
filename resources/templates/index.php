<?php
/**
 * List template: the blog, search results, category / tag / author / date archives.
 *
 * Left sidebar: the search box and result types on search pages, else the blog categories and tags.
 * Main column: the title bar with the list / grid toggle, then the posts grouped by month (or as cards).
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\View\Archive;
use Jiejia\ArsPetal\View\Topics;

TemplateLoader::part('header');

$found = (int) $GLOBALS['wp_query']->found_posts;
$sidebar = [];

if (is_search()) {
    $type = get_query_var('post_type');
    $type = is_string($type) ? $type : '';
    $search = static fn (string $postType): string => (string) add_query_arg(array_filter(['s' => get_search_query(false), 'post_type' => $postType]), home_url('/'));
    $types = [['label' => __('All', 'ars-petal'), 'url' => $search(''), 'current' => $type === '', 'icon' => 'layout-grid']];

    if (Podcast::isActive()) {
        $types[] = ['label' => __('Podcast', 'ars-petal'), 'url' => $search(Podcast::POST_TYPE), 'current' => $type === Podcast::POST_TYPE, 'icon' => 'mic'];
    }

    $types[] = ['label' => __('Blog', 'ars-petal'), 'url' => $search('post'), 'current' => $type === 'post', 'icon' => 'newspaper'];
    $title = get_search_query() !== '' ? Archive::title() : __('Search', 'ars-petal');
    /* translators: %s: number of results. */
    $count = sprintf(_n('%s result', '%s results', $found, 'ars-petal'), number_format_i18n($found));
} else {
    $categories = get_categories(['orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true, 'number' => 8]);
    $items = [];

    if (Blog::url() !== '') {
        $items[] = ['label' => __('All', 'ars-petal'), 'meta' => sprintf(/* translators: %s: number of posts. */ _n('%s post', '%s posts', (int) wp_count_posts()->publish, 'ars-petal'), number_format_i18n((int) wp_count_posts()->publish)), 'url' => Blog::url(), 'current' => Blog::isRequest() || is_home(), 'icon' => 'layout-grid'];
    }

    foreach ($categories as $category) {
        $items[] = [
            'label' => $category->name,
            /* translators: %s: number of posts. */
            'meta' => sprintf(_n('%s post', '%s posts', (int) $category->count, 'ars-petal'), number_format_i18n((int) $category->count)),
            'url' => (string) get_category_link($category),
            'current' => is_category($category->term_id),
            'icon' => Topics::icon($category),
            'tile' => Topics::block($category),
        ];
    }

    $title = Archive::title();
    /* translators: %s: number of posts. */
    $count = sprintf(_n('%s post', '%s posts', $found, 'ars-petal'), number_format_i18n($found));
}

$tags = is_search() ? [] : get_tags(['orderby' => 'count', 'order' => 'DESC', 'number' => 12]);
$description = Archive::description();
?>
<section x-data="listView('posts', 'list')" :data-view="view" data-view="list">
  <div class="mt-2 grid grid-cols-1 gap-6 lg:grid-cols-[18rem_minmax(0,1fr)] lg:items-start lg:gap-8">
    <div class="min-w-0 space-y-3 lg:sticky lg:top-[5.5rem]">
      <?php if (is_search()) : ?>
        <aside class="panel-sm p-3 lg:p-4" aria-label="<?php esc_attr_e('Search', 'ars-petal'); ?>">
          <h2 class="display hidden text-xl lg:block"><?php esc_html_e('Search', 'ars-petal'); ?></h2>
          <?php get_search_form(); ?>
        </aside>
        <?php Template::render('parts/side-list', ['title' => __('Type', 'ars-petal'), 'items' => $types]); ?>
      <?php else : ?>
        <?php Template::render('parts/side-list', ['title' => __('Categories', 'ars-petal'), 'items' => $items]); ?>
        <?php Template::render('parts/tags', ['terms' => is_array($tags) ? $tags : [], 'current' => is_tag() ? (string) get_query_var('tag') : '']); ?>
      <?php endif; ?>
    </div>

    <div class="min-w-0">
      <?php Template::render('parts/title-bar', ['title' => $title, 'count' => $count]); ?>
      <?php if ($description !== '') : ?>
        <p class="mute mt-4 px-3"><?php echo esc_html($description); ?></p>
      <?php endif; ?>

      <?php if (have_posts()) : ?>
        <?php Template::render('parts/listing', ['months' => ! is_search(), 'badge' => is_search()]); ?>
        <?php Template::render('parts/pagination'); ?>
      <?php else : ?>
        <?php Template::render('parts/empty', ['message' => is_search() ? __('Nothing matched your search. Try other words.', 'ars-petal') : __('Nothing has been published here yet.', 'ars-petal')]); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
TemplateLoader::part('footer');
