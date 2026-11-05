<?php
/**
 * Episode row (archive list view, author page, search, sidebars): small cover, title, topic / duration /
 * date, then favorite and play. The row turns gray while its episode is in the player.
 *
 * @var \Jiejia\ArsPetal\Podcast\Episode $episode
 * @var string $heading Heading tag.
 * @var bool $badge Whether to start the meta line with a "Podcast" badge (mixed search results).
 * @var bool $compact Sidebar size: smaller cover, two-line title, no favorite.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Format;

$heading ??= 'h3';
$badge ??= false;
$compact ??= false;
$tag = $episode->tag();
$url = get_permalink($episode->post);
$meta = array_filter([
    $tag ? $tag->name : '',
    $episode->duration() > 0 ? Format::duration($episode->duration()) : '',
    $compact ? '' : Format::date($episode->post),
]);
$separator = '<span class="mx-1 opacity-40">/</span>';
?>
<?php if ($compact) : ?>
<li class="petal-item petal-row flex items-center gap-3 rounded-2xl bg-base-200/70 p-2 transition-colors hover:bg-base-200">
  <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="shrink-0"><?php Template::render('parts/cover', ['url' => $episode->cover('thumbnail'), 'alt' => '', 'class' => 'size-12 rounded-xl object-cover']); ?></a>
  <div class="min-w-0 flex-1">
    <a href="<?php echo esc_url($url); ?>" class="line-clamp-2 text-sm font-semibold leading-snug hover:underline"><?php echo esc_html(get_the_title($episode->post)); ?></a>
    <p class="mute mt-0.5 truncate text-xs"><?php echo wp_kses(implode($separator, array_map('esc_html', $meta)), ['span' => ['class' => true]]); ?></p>
  </div>
  <?php Template::render('parts/play-button', ['episode' => $episode, 'class' => 'btn-black size-9 shrink-0 rounded-full', 'icon' => 'size-3.5']); ?>
</li>
<?php else : ?>
<li class="petal-item petal-row flex items-center gap-3 rounded-2xl p-2 transition-colors hover:bg-base-200 sm:gap-4 sm:p-2.5">
  <a href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true" class="relative shrink-0">
    <?php Template::render('parts/cover', ['url' => $episode->cover('medium'), 'alt' => '', 'class' => 'size-14 rounded-xl object-cover sm:size-[4.5rem] sm:rounded-2xl']); ?>
    <?php // Shown while this episode plays (see .petal-eq-cover in theme.css). ?>
    <span class="petal-eq-cover absolute inset-0 place-items-center rounded-xl bg-black/40 text-white sm:rounded-2xl"><span class="eq"><i></i><i></i><i></i></span></span>
  </a>
  <div class="min-w-0 flex-1">
    <<?php echo tag_escape($heading); ?> class="text-[0.95rem] font-semibold leading-snug sm:line-clamp-1 sm:text-base sm:leading-normal"><a href="<?php echo esc_url($url); ?>" class="hover:underline"><?php echo esc_html(get_the_title($episode->post)); ?></a></<?php echo tag_escape($heading); ?>>
    <p class="mute mt-1 flex min-w-0 items-center text-xs sm:mt-0.5 sm:text-sm">
      <?php if ($badge) : ?>
        <span class="mr-2 shrink-0 rounded-full bg-base-200 px-2 py-0.5 text-[0.7rem] font-semibold text-base-content"><?php esc_html_e('Podcast', 'ars-petal'); ?></span>
      <?php endif; ?>
      <?php // Each part stays whole and the "/" ends its line, so on phones the line wraps between parts. ?>
      <span class="sm:truncate"><?php
        $last = count($meta) - 1;
        echo wp_kses(implode(' ', array_map(static fn (string $part, int $i): string => '<span class="whitespace-nowrap">' . esc_html($part) . ($i < $last ? $separator : '') . '</span>', array_values($meta), array_keys(array_values($meta)))), ['span' => ['class' => true]]);
        ?></span>
    </p>
  </div>
  <?php if ($episode->playable()) : ?>
    <button type="button" class="mute hidden size-10 shrink-0 place-items-center rounded-full hover:bg-base-300 sm:grid" data-ars-fav="<?php echo (int) $episode->id(); ?>" aria-pressed="false" aria-label="<?php esc_attr_e('Favorite', 'ars-petal'); ?>"><?php Icon::render('heart', 'size-5'); ?></button>
  <?php endif; ?>
  <?php Template::render('parts/play-button', ['episode' => $episode, 'class' => 'btn-black size-10 shrink-0 rounded-full', 'icon' => 'size-4']); ?>
</li>
<?php endif; ?>
