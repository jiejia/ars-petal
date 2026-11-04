<?php
/**
 * Author page. Left sidebar: the hosts (all people with the plugin, the authors without), this person black.
 * Main column: the profile card (big avatar, name, bio, links, counts), then a card with the episodes they
 * host or guest on and the posts they wrote; with both, pill tabs link between them (?tab=, see View\Author).
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\Setup\TemplateLoader;
use Jiejia\ArsPetal\View\Author;

TemplateLoader::part('header');

$user = Author::user();
$counts = $user ? Author::counts($user) : [Author::EPISODES => 0, Author::POSTS => 0];
$tab = $user ? Author::tab($user) : Author::POSTS;
$labels = [
    Author::EPISODES => _x('Episodes', 'author page section', 'ars-petal'),
    Author::POSTS => _x('Posts', 'author page section', 'ars-petal'),
];
$summary = $user ? Author::summary($user) : '';
$people = array_map(static fn (\WP_User $person): array => [
    'label' => $person->display_name,
    'meta' => Author::summary($person),
    'url' => get_author_posts_url($person->ID),
    'current' => $user && $person->ID === $user->ID,
    'user' => $person,
], Podcast::hosts());
?>
<article>
  <div class="mt-2 grid grid-cols-1 items-start gap-6 lg:grid-cols-[18rem_minmax(0,1fr)] lg:gap-8">
    <div class="order-2 min-w-0 space-y-3 lg:sticky lg:top-24 lg:order-1">
      <?php Template::render('parts/side-list', ['title' => Podcast::isActive() ? __('Hosts', 'ars-petal') : __('Authors', 'ars-petal'), 'items' => $people]); ?>
    </div>

    <div class="order-1 min-w-0 lg:order-2">
      <?php if ($user) : ?>
        <header class="panel p-4 sm:p-5">
          <div class="flex flex-col gap-5 sm:flex-row sm:gap-6">
            <?php Template::render('parts/avatar', ['user' => $user, 'class' => 'aspect-square w-full rounded-2xl border border-base-300 shadow-lg sm:w-44', 'text' => 'text-7xl']); ?>
            <div class="flex min-w-0 flex-1 flex-col">
              <p class="mute text-sm"><?php echo esc_html($counts[Author::EPISODES] > 0 ? __('Host / guest', 'ars-petal') : __('Author', 'ars-petal')); ?></p>
              <h1 class="display mt-1.5 text-2xl sm:text-3xl"><?php echo esc_html($user->display_name); ?></h1>
              <?php if ($user->description !== '') : ?>
                <p class="mute mt-2 leading-relaxed"><?php echo esc_html($user->description); ?></p>
              <?php endif; ?>
              <div class="mt-auto flex flex-wrap items-center gap-2 pt-4">
                <?php if ($user->user_url !== '') : ?>
                  <a href="<?php echo esc_url($user->user_url); ?>" data-no-swup target="_blank" rel="noopener" class="chip"><?php Icon::render('globe', 'size-4'); ?><?php esc_html_e('Website', 'ars-petal'); ?></a>
                <?php endif; ?>
                <?php if ($summary !== '') : ?>
                  <span class="mute ml-auto text-sm"><?php echo esc_html($summary); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </header>
      <?php endif; ?>

      <div class="panel mt-4 p-4 sm:p-8">
        <?php if ($user && $counts[Author::EPISODES] > 0 && $counts[Author::POSTS] > 0) : ?>
          <?php // Links rather than in-page tabs: each list has its own pages. ?>
          <nav role="tablist" class="mb-6 flex w-fit rounded-full bg-base-200 p-1" aria-label="<?php echo esc_attr($user->display_name); ?>">
            <?php foreach ($labels as $key => $label) : ?>
              <a href="<?php echo esc_url(Author::tabUrl($user, $key)); ?>" role="tab" class="rounded-full px-5 py-2.5 text-sm font-medium transition <?php echo $tab === $key ? 'bg-primary text-primary-content' : 'hover:bg-base-300'; ?>" aria-selected="<?php echo $tab === $key ? 'true' : 'false'; ?>"><?php echo esc_html($label); ?> <span class="opacity-60"><?php echo esc_html(number_format_i18n($counts[$key])); ?></span></a>
            <?php endforeach; ?>
          </nav>
        <?php elseif ($counts[$tab] > 0) : ?>
          <h2 class="display mb-4 px-1 text-xl"><?php echo esc_html($labels[$tab]); ?></h2>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
          <ol class="-mx-2 space-y-1 sm:-mx-3">
            <?php while (have_posts()) : the_post(); ?>
              <?php Template::render('cards/row', ['post' => get_post()]); ?>
            <?php endwhile; ?>
          </ol>
          <?php Template::render('parts/pagination'); ?>
        <?php else : ?>
          <?php Template::render('episode/empty', ['text' => __('Nothing has been published here yet.', 'ars-petal')]); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</article>
<?php
TemplateLoader::part('footer');
