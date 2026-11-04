<?php
/**
 * Home: "N topics, one color each" – large topic cards in the topic colors, with a white artwork.
 *
 * @var array<int,array{term:\WP_Term,count:int,url:string}> $topics Up to four topics, most used first.
 * @var bool $podcast Whether the topics are episode tags (else blog categories).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Topics;

if ($topics === []) {
    return;
}
?>
<section class="mt-32" aria-labelledby="topics-title">
  <div class="max-w-2xl">
    <?php
    Template::render('front/heading', [
        'id' => 'topics-title',
        /* translators: %s: number of topics shown. */
        'strong' => sprintf(_nx('%s topic, ', '%s topics, ', count($topics), 'home heading, bold part', 'ars-petal'), number_format_i18n(count($topics))),
        /* translators: %s: number of topics shown. */
        'light' => sprintf(_nx('%s color', '%s colors', count($topics), 'home heading, light part', 'ars-petal'), number_format_i18n(count($topics))),
    ]);
    ?>
    <p class="display-light mute mt-4 text-xl"><?php echo esc_html($podcast ? __('Every topic has its own color: badges, tiles and cards follow it.', 'ars-petal') : __('Every category has its own color: badges, tiles and cards follow it.', 'ars-petal')); ?></p>
  </div>
  <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($topics as $topic) : ?>
      <a href="<?php echo esc_url($topic['url']); ?>" class="cat-card <?php echo esc_attr(Topics::block($topic['term'])); ?>">
        <span class="mono text-xs opacity-75">/<?php echo esc_html(urldecode($topic['term']->slug)); ?></span>
        <span class="relative z-10">
          <span class="display block text-2xl"><?php echo esc_html($topic['term']->name); ?></span>
          <span class="mt-1 block text-sm opacity-80"><?php echo esc_html($podcast ? sprintf(/* translators: %s: number of episodes. */ _n('%s episode', '%s episodes', $topic['count'], 'ars-petal'), number_format_i18n($topic['count'])) : sprintf(/* translators: %s: number of posts. */ _n('%s post', '%s posts', $topic['count'], 'ars-petal'), number_format_i18n($topic['count']))); ?></span>
        </span>
        <img src="<?php echo esc_url(Topics::image($topic['term'])); ?>" alt="" loading="lazy">
      </a>
    <?php endforeach; ?>
  </div>
</section>
