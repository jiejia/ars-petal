<?php
/**
 * Home: the big bold / light headline with the "$ subscribe" command label and two large buttons, beside a
 * tilted stack of "app windows": where to listen (with the real night mode toggle), the topic swatches and
 * two facts about the show.
 *
 * @var \Jiejia\ArsPetal\Podcast\Episode|null $latest Newest episode, null in blog mode.
 * @var array<int,\WP_Term> $topics Episode tags or blog categories, most used first.
 * @var callable $topicUrl Term => URL.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Navigation\Menu;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\View\Format;
use Jiejia\ArsPetal\View\Topics;

$strong = Options::get('headline_strong') ?: get_bloginfo('name');
$light = Options::get('headline_light') ?: get_bloginfo('description');
$text = Podcast::setting('description');
$text = $text !== $light ? $text : '';
$feed = Podcast::feedUrl();
$stats = Podcast::stats();
$schedule = Options::get('schedule');
$links = array_slice(Podcast::listenLinks(), 0, 4);
?>
<section class="relative isolate mt-24 grid items-center gap-14 lg:mt-32 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
  <div class="glow -left-20 top-10 size-80 bg-[var(--glow-b)]" aria-hidden="true"></div>
  <div class="min-w-0">
    <button type="button" class="cmd max-w-full" x-data="copyText('<?php echo esc_js($feed); ?>')" @click="copy()" aria-label="<?php esc_attr_e('Copy the feed address', 'ars-petal'); ?>">
      <span x-show="!copied"><?php Icon::render('copy', 'mute size-4'); ?></span><span x-show="copied" x-cloak><?php Icon::render('check', 'size-4 text-success'); ?></span>
      <span class="truncate"><span class="mute">$</span> subscribe <?php echo esc_html(untrailingslashit((string) preg_replace('#^https?://#', '', $feed))); ?></span>
    </button>
    <h2 class="mt-7 text-5xl leading-[1.05] sm:text-6xl">
      <span class="display block !font-black"><?php echo esc_html($strong); ?></span>
      <?php if ($light !== '') : ?>
        <span class="display-light block"><?php echo esc_html($light); ?></span>
      <?php endif; ?>
    </h2>
    <?php if ($text !== '') : ?>
      <p class="display-light mute mt-7 line-clamp-4 max-w-lg text-xl leading-relaxed sm:text-2xl"><?php echo esc_html($text); ?></p>
    <?php endif; ?>
    <div class="mt-10 grid max-w-lg grid-cols-1 gap-3 sm:grid-cols-2">
      <?php if ($latest) : ?>
        <a href="<?php echo esc_url(Podcast::archiveUrl()); ?>" class="btn-white h-14 rounded-2xl text-lg"><?php Icon::render('audio-lines', 'size-5'); ?><?php esc_html_e('All episodes', 'ars-petal'); ?></a>
        <?php if ($latest->playable()) : ?>
          <?php Template::render('parts/play-button', ['episode' => $latest, 'class' => 'btn-black h-14 rounded-2xl text-lg', 'icon' => 'size-5', 'label' => __('Start with the latest', 'ars-petal')]); ?>
        <?php else : ?>
          <a href="<?php echo esc_url(get_permalink($latest->post)); ?>" class="btn-black h-14 rounded-2xl text-lg"><?php esc_html_e('Start with the latest', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-5'); ?></a>
        <?php endif; ?>
      <?php elseif (Blog::url() !== '') : ?>
        <a href="<?php echo esc_url(Blog::url()); ?>" class="btn-black h-14 rounded-2xl text-lg sm:col-span-2"><?php esc_html_e('Read the blog', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-5'); ?></a>
      <?php endif; ?>
    </div>
    <?php if ($stats['plays'] > 0) : ?>
      <p class="mute mt-4 flex max-w-lg items-center justify-center gap-1.5 text-sm sm:justify-end"><?php Icon::render('flame', 'size-4 fill-error text-error'); ?><?php echo esc_html(sprintf(/* translators: %s: number of plays. */ _n('%s play so far', '%s plays so far', $stats['plays'], 'ars-petal'), number_format_i18n($stats['plays']))); ?></p>
    <?php endif; ?>
  </div>

  <div class="tilt-stage relative hidden sm:block" aria-hidden="true">
    <div class="tilt grid grid-cols-[1.1fr_1fr] gap-5">
      <div class="win p-5">
        <div class="win-dots"><i></i><i></i><i></i></div>
        <div role="presentation" class="tabs tabs-border mt-3">
          <span class="tab tab-active"><?php echo esc_html(Podcast::isActive() ? __('Listen on', 'ars-petal') : __('Follow', 'ars-petal')); ?></span>
        </div>
        <ul class="mt-2 divide-y divide-base-300">
          <?php foreach ($links as $link) : ?>
            <li><a<?php echo Menu::externalAttributes($link['url']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?> tabindex="-1" class="flex items-center justify-between gap-4 py-2.5"><span class="display-light truncate text-[1.05rem]"><?php echo esc_html($link['label']); ?></span><?php Icon::render($link['icon'], 'size-5 text-primary'); ?></a></li>
          <?php endforeach; ?>
          <li class="flex items-center justify-between gap-4 py-2.5" x-data>
            <span class="display-light text-[1.05rem]"><?php esc_html_e('Night mode', 'ars-petal'); ?></span>
            <input type="checkbox" tabindex="-1" class="toggle toggle-success" :checked="$store.ui.dark" @change="$store.ui.toggleTheme()">
          </li>
        </ul>
      </div>
      <div class="flex flex-col gap-5">
        <?php if ($topics !== []) : ?>
          <div class="win p-5">
            <p class="display text-lg"><?php esc_html_e('Topics', 'ars-petal'); ?></p>
            <div class="mt-4 grid grid-cols-4 gap-3">
              <?php foreach (array_slice($topics, 0, 8) as $topic) : ?>
                <a href="<?php echo esc_url($topicUrl($topic)); ?>" tabindex="-1" class="group/s min-w-0 text-center">
                  <span class="swatch-tile <?php echo esc_attr(Topics::block($topic)); ?>"><?php Icon::render(Topics::icon($topic), 'size-6'); ?></span>
                  <span class="mute mt-1.5 block truncate text-[0.68rem] group-hover/s:text-base-content"><?php echo esc_html($topic->name); ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="win flex items-center gap-4 p-5">
          <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-base-content text-base-100"><?php Icon::render($latest ? 'audio-lines' : 'newspaper', 'size-5'); ?></span>
          <p class="display-light leading-snug">
            <?php if ($latest) : ?>
              <?php echo esc_html(sprintf(/* translators: %s: number of episodes. */ _n('%s episode', '%s episodes', $stats['episodes'], 'ars-petal'), number_format_i18n($stats['episodes']))); ?>
              <br><span class="mute text-sm"><?php echo esc_html($schedule !== '' ? $schedule : ($stats['duration'] > 0 ? sprintf(/* translators: %s: average duration. */ __('About %s each', 'ars-petal'), Format::minutes($stats['duration'])) : '')); ?></span>
            <?php else : ?>
              <?php $count = (int) wp_count_posts()->publish; ?>
              <?php echo esc_html(sprintf(/* translators: %s: number of posts. */ _n('%s post', '%s posts', $count, 'ars-petal'), number_format_i18n($count))); ?>
              <br><span class="mute text-sm"><?php echo esc_html(get_bloginfo('name')); ?></span>
            <?php endif; ?>
          </p>
        </div>
        <div class="win flex items-center gap-4 p-5">
          <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-primary text-primary-content"><?php Icon::render('rss', 'size-5'); ?></span>
          <p class="display-light leading-snug"><?php esc_html_e('Open RSS feed', 'ars-petal'); ?><br><span class="mute text-sm"><?php echo esc_html(Podcast::isActive() ? __('Works in any podcast app', 'ars-petal') : __('Works in any feed reader', 'ars-petal')); ?></span></p>
        </div>
      </div>
    </div>
  </div>
</section>
