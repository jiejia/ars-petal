<?php
/**
 * Home: the "Topics" heading over large topic cards in the topic colors, with a white artwork.
 *
 * @var array<int,array{term:\WP_Term,count:int,url:string,image?:string}> $topics Up to four topics, most used first;
 *      image is the category's own artwork (plugin field), else the theme's.
 * @var bool $podcast Whether the topics are episode categories (else blog categories).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Topics;

if ($topics === []) {
    return;
}
?>
<section class="mt-8" aria-labelledby="topics-title">
  <?php
  Template::render('front/heading', [
      'id' => 'topics-title',
      'strong' => $podcast ? _x('Podcast', 'home heading, bold part', 'ars-petal') : _x('Blog', 'home heading, bold part', 'ars-petal'),
      'light' => _x('categories', 'home heading, light part', 'ars-petal'),
  ]);
  ?>
  <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($topics as $topic) : ?>
      <a href="<?php echo esc_url($topic['url']); ?>" class="cat-card <?php echo esc_attr(Topics::block($topic['term'])); ?>">
        <span class="mono text-xs opacity-75">/<?php echo esc_html(urldecode($topic['term']->slug)); ?></span>
        <span class="relative z-10">
          <span class="display block text-2xl"><?php echo esc_html($topic['term']->name); ?></span>
          <span class="mt-1 block text-sm opacity-80"><?php echo esc_html($podcast ? sprintf(/* translators: %s: number of episodes. */ _n('%s episode', '%s episodes', $topic['count'], 'ars-petal'), number_format_i18n($topic['count'])) : sprintf(/* translators: %s: number of posts. */ _n('%s post', '%s posts', $topic['count'], 'ars-petal'), number_format_i18n($topic['count']))); ?></span>
        </span>
        <img src="<?php echo esc_url(($topic['image'] ?? '') ?: Topics::image($topic['term'])); ?>" alt="" loading="lazy">
      </a>
    <?php endforeach; ?>
  </div>
</section>
