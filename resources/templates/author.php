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
// Host, guest or both (by the episodes they appear in); else author.
$hostIds = array_map(static fn (\WP_User $person): int => $person->ID, Podcast::hosts());
$guestIds = array_map(static fn (\WP_User $person): int => $person->ID, Podcast::guests());
$isHost = $user && in_array($user->ID, $hostIds, true);
$isGuest = $user && in_array($user->ID, $guestIds, true);
$role = match (true) {
    $isHost && $isGuest => __('Host / guest', 'ars-petal'),
    $isHost => __('Host', 'ars-petal'),
    $isGuest => __('Guest', 'ars-petal'),
    default => __('Author', 'ars-petal'),
};
// The counts with bold numbers, as in the prototype: "**2** episodes · **1** post".
$bold = static fn (int $number): string => '<span class="font-bold text-base-content">' . esc_html(number_format_i18n($number)) . '</span>';
$stats = array_filter([
    /* translators: %s: number of episodes. */
    $counts[Author::EPISODES] > 0 ? sprintf(esc_html(_nx('%s episode', '%s episodes', $counts[Author::EPISODES], 'author page summary', 'ars-petal')), $bold($counts[Author::EPISODES])) : '',
    /* translators: %s: number of posts. */
    $counts[Author::POSTS] > 0 ? sprintf(esc_html(_n('%s post', '%s posts', $counts[Author::POSTS], 'ars-petal')), $bold($counts[Author::POSTS])) : '',
]);
$people = array_map(static fn (\WP_User $person): array => [
    'label' => $person->display_name,
    'meta' => Author::summary($person),
    'url' => get_author_posts_url($person->ID),
    'current' => $user && $person->ID === $user->ID,
    'user' => $person,
], Podcast::hosts());
?>
<article>
  <div class="mt-2 drawer lg:drawer-open lg:items-start lg:gap-8">
    <?php Template::render('parts/sidebar-start', ['class' => 'lg:top-24']); ?>
      <?php Template::render('parts/side-list', ['title' => Podcast::isActive() ? __('Hosts', 'ars-petal') : __('Authors', 'ars-petal'), 'items' => $people]); ?>
    <?php Template::render('parts/sidebar-end'); ?>

    <div class="drawer-content">
      <?php if ($user) : ?>
        <header class="panel p-4 sm:p-5">
          <div class="flex flex-col gap-5 sm:flex-row sm:gap-6">
            <?php Template::render('parts/avatar', ['user' => $user, 'class' => 'petal-avatar-lg aspect-square w-40 self-center rounded-full sm:w-44 sm:self-start', 'text' => 'text-7xl']); ?>
            <div class="flex min-w-0 flex-1 flex-col text-center sm:text-left">
              <p class="mute text-sm"><?php echo esc_html($role); ?></p>
              <h1 class="display mt-1.5 text-2xl sm:text-3xl"><?php echo esc_html($user->display_name); ?></h1>
              <?php if ($user->description !== '') : ?>
                <p class="mute mt-2 leading-relaxed"><?php echo esc_html($user->description); ?></p>
              <?php endif; ?>
              <div class="mt-auto flex flex-wrap items-center justify-center gap-2 pt-4 sm:justify-start">
                <?php if ($user->user_url !== '') : ?>
                  <a href="<?php echo esc_url($user->user_url); ?>" data-no-swup target="_blank" rel="noopener" class="chip"><?php Icon::render('globe', 'size-4'); ?><?php esc_html_e('Website', 'ars-petal'); ?></a>
                <?php endif; ?>
                <?php if ($stats !== []) : ?>
                  <span class="mute text-sm sm:ml-auto"><?php echo implode(' · ', $stats); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts above. ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </header>
      <?php endif; ?>

      <div class="panel mt-4 p-4 sm:p-8">
        <?php if ($user && $counts[Author::EPISODES] > 0 && $counts[Author::POSTS] > 0) : ?>
          <?php // Links rather than in-page tabs: each list has its own pages. ?>
          <nav role="tablist" class="mx-auto mb-6 flex w-fit max-w-full rounded-full bg-base-200 p-0.5 sm:mx-0 sm:p-1" aria-label="<?php echo esc_attr($user->display_name); ?>">
            <?php foreach ($labels as $key => $label) : ?>
              <a href="<?php echo esc_url(Author::tabUrl($user, $key)); ?>" role="tab" class="rounded-full px-3 py-1.5 text-[0.8125rem] font-medium transition min-[360px]:px-3.5 min-[360px]:text-sm sm:px-5 sm:py-2.5 <?php echo $tab === $key ? 'bg-primary text-primary-content' : 'hover:bg-base-300'; ?>" aria-selected="<?php echo $tab === $key ? 'true' : 'false'; ?>"><?php echo esc_html($label); ?> <span class="opacity-60"><?php echo esc_html(number_format_i18n($counts[$key])); ?></span></a>
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
