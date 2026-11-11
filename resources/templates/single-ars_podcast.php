<?php
/**
 * Episode page (A Ripple Song). Left sidebar: more episodes and the episode categories. Main column: the episode card
 * (cover, meta line, title, excerpt, play and an action pill with share / favorite / download), the hosts
 * and guests, a card with the summary / chapters / transcript tabs and the tags, the previous / next
 * episode and the comments.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Podcast\Episode;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\View\Format;
use Jiejia\ArsPetal\View\Topics;

get_header();

while (have_posts()) :
    the_post();
    $episode = Episode::from(get_post());
    $tag = $episode->tag();
    $playable = $episode->playable();
    $more = array_map(static fn (\WP_Post $post): Episode => new Episode($post), get_posts(['post_type' => Podcast::POST_TYPE, 'numberposts' => 4, 'post__not_in' => [$episode->id()]]));
    $inCategories = wp_get_post_terms($episode->id(), Podcast::CATEGORY, ['fields' => 'ids']);
    $inCategories = is_array($inCategories) ? array_map('intval', $inCategories) : [];
    $topics = array_map(static function (array $item) use ($inCategories): array {
        $count = (int) $item['count'];

        return [
            'label' => $item['term']->name,
            /* translators: %s: number of episodes. */
            'meta' => sprintf(_n('%s episode', '%s episodes', $count, 'ars-petal'), number_format_i18n($count)),
            'url' => Podcast::categoryUrl($item['term']),
            'current' => in_array($item['term']->term_id, $inCategories, true),
            'image' => Podcast::categoryImage($item['term']) ?: Topics::image($item['term']),
        ];
    }, array_slice(Podcast::categories(), 0, 8));
    $separator = '<span class="mx-1 opacity-40">/</span>';
    $tabs = [
        'notes' => __('Summary', 'ars-petal'),
        'chapters' => __('Chapters', 'ars-petal'),
        'transcript' => __('Transcript', 'ars-petal'),
    ];
    ?>
<article x-data="episodePage(<?php echo (int) $episode->id(); ?>)" <?php post_class(); ?>>
  <div class="mt-2 drawer lg:drawer-open lg:items-start lg:gap-8">
    <?php Template::render('parts/sidebar-start', ['class' => 'lg:top-24']); ?>
      <aside class="panel-sm p-4 lg:p-5" aria-label="<?php esc_attr_e('More episodes', 'ars-petal'); ?>">
        <h2 class="display text-xl"><?php esc_html_e('More episodes', 'ars-petal'); ?></h2>
        <?php if ($more !== []) : ?>
          <ol class="mt-4 space-y-2">
            <?php foreach ($more as $item) : ?>
              <?php Template::render('cards/episode-row', ['episode' => $item, 'compact' => true]); ?>
            <?php endforeach; ?>
          </ol>
        <?php else : ?>
          <p class="mute mt-4 rounded-2xl border-2 border-dashed border-base-content/15 px-4 py-6 text-center text-sm"><?php esc_html_e('No other episodes yet.', 'ars-petal'); ?></p>
        <?php endif; ?>
      </aside>
      <?php Template::render('parts/side-list', ['title' => __('Topics', 'ars-petal'), 'items' => $topics, 'wrap' => true, 'empty' => __('No topics yet.', 'ars-petal')]); ?>
    <?php Template::render('parts/sidebar-end'); ?>

    <div class="drawer-content">
      <header class="petal-item panel p-4 sm:p-5">
        <div class="flex flex-col gap-5 sm:flex-row sm:gap-6">
          <?php Template::render('parts/cover', ['url' => $episode->cover('large'), 'alt' => sprintf(/* translators: %s: episode number. */ __('EP.%s cover', 'ars-petal'), $episode->number()), 'class' => 'aspect-[4/3] w-full shrink-0 self-start rounded-2xl object-cover sm:w-56', 'lazy' => false]); ?>
          <div class="flex min-w-0 flex-1 flex-col text-center sm:text-left">
            <p class="mute text-sm">
              <?php if ($tag) : ?>
                <a href="<?php echo esc_url(Podcast::tagUrl($tag)); ?>" class="hover:text-base-content"><?php echo esc_html($tag->name); ?></a><?php echo wp_kses_post($separator); ?>
              <?php endif; ?>
              EP.<?php echo (int) $episode->number(); ?><?php echo wp_kses_post($separator); ?><?php echo esc_html(Format::date()); ?>
              <?php if ($episode->duration() > 0) : ?>
                <?php echo wp_kses_post($separator); ?><?php echo esc_html(Format::duration($episode->duration())); ?>
              <?php endif; ?>
              <?php echo wp_kses_post($separator); ?><span class="whitespace-nowrap"><?php Icon::render('headphones', 'mr-1 inline-block size-3.5 align-[-0.15em]'); ?><span data-ars-plays="<?php echo (int) $episode->id(); ?>" data-ars-plays-one="<?php /* translators: %s: play count. */ echo esc_attr__('%s play', 'ars-petal'); ?>" data-ars-plays-other="<?php /* translators: %s: play count. */ echo esc_attr__('%s plays', 'ars-petal'); ?>"><?php echo esc_html(sprintf(/* translators: %s: play count. */ _n('%s play', '%s plays', $episode->plays(), 'ars-petal'), number_format_i18n($episode->plays()))); ?></span></span>
            </p>
            <h1 class="display mt-1.5 text-balance text-2xl sm:text-3xl"><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
              <?php // Only a hand-written excerpt: the automatic one repeats the summary tab below. ?>
              <p class="mute mt-2 line-clamp-2 leading-relaxed"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
            <div class="mt-auto flex flex-wrap items-center justify-center gap-3 pt-4 sm:justify-start">
              <?php Template::render('parts/play-button', ['episode' => $episode, 'class' => 'btn-black size-12 shrink-0 rounded-full px-0 text-sm sm:w-auto sm:px-7', 'icon' => 'size-4', 'label' => true, 'labelClass' => 'hidden sm:contents']); ?>
              <div class="flex items-center gap-1 rounded-full bg-base-200 p-1">
                <?php Template::render('parts/share'); ?>
                <?php if ($playable) : ?>
                  <button type="button" class="btn-gray size-10 rounded-full !bg-transparent" data-ars-fav="<?php echo (int) $episode->id(); ?>" aria-pressed="false" aria-label="<?php esc_attr_e('Favorite', 'ars-petal'); ?>"><?php Icon::render('heart'); ?></button>
                <?php endif; ?>
                <?php if ($episode->audio() !== '') : ?>
                  <a href="<?php echo esc_url($episode->audio()); ?>" download data-no-swup class="btn-gray size-10 rounded-full !bg-transparent" aria-label="<?php esc_attr_e('Download audio', 'ars-petal'); ?>"><?php Icon::render('arrow-down-to-line'); ?></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <?php Template::render('parts/people', ['title' => __('Hosts', 'ars-petal'), 'users' => $episode->hosts(), 'keepEmpty' => true]); ?>
        <?php Template::render('parts/people', ['title' => __('Guests', 'ars-petal'), 'users' => $episode->guests(), 'keepEmpty' => true]); ?>
      </div>

      <div class="panel mt-4 p-4 sm:p-8">
        <div role="tablist" class="mx-auto mb-6 flex w-fit max-w-full overflow-x-auto rounded-full bg-base-200 p-0.5 sm:mx-0 sm:p-1">
          <?php foreach ($tabs as $key => $label) : ?>
            <button type="button" role="tab" class="shrink-0 rounded-full px-3 py-1.5 text-[0.8125rem] font-medium transition min-[360px]:px-3.5 min-[360px]:text-sm sm:px-5 sm:py-2.5" :class="tab === '<?php echo esc_js($key); ?>' ? 'bg-primary text-primary-content' : 'hover:bg-base-300'" :aria-selected="tab === '<?php echo esc_js($key); ?>'" @click="tab = '<?php echo esc_js($key); ?>'"><?php echo esc_html($label); ?></button>
          <?php endforeach; ?>
        </div>
        <div x-show="tab === 'notes'" role="tabpanel" class="entry-content">
          <?php if (trim(get_the_content()) !== '') : ?>
            <?php the_content(); ?>
            <?php wp_link_pages(); ?>
          <?php else : ?>
            <?php Template::render('episode/empty', ['text' => __('This episode has no summary yet.', 'ars-petal')]); ?>
          <?php endif; ?>
        </div>
        <?php Template::render('episode/chapters', ['chapters' => $episode->chapters(), 'playable' => $playable]); ?>
        <?php Template::render('episode/transcript', ['transcript' => $episode->transcript(), 'playable' => $playable]); ?>
        <?php Template::render('parts/tags', ['terms' => get_the_tags() ?: [], 'url' => [Podcast::class, 'tagUrl'], 'inline' => true]); ?>
      </div>

      <?php Template::render('parts/adjacent', ['newest' => __('This is the newest episode', 'ars-petal'), 'oldest' => __('This is the first episode', 'ars-petal'), 'number' => true]); ?>

      <?php if (comments_open() || get_comments_number()) : ?>
        <?php comments_template(); ?>
      <?php endif; ?>
    </div>
  </div>
</article>
    <?php
endwhile;

get_footer();
