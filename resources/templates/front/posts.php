<?php
/**
 * Home: recent blog posts as cards.
 *
 * @var array<int,\WP_Post> $posts Posts.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Blog\Blog;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;

if ($posts === []) {
    return;
}
?>
<section class="mt-8" aria-labelledby="blog-title">
  <div class="flex items-end justify-between gap-4">
    <?php Template::render('front/heading', ['id' => 'blog-title', 'strong' => _x('Recent', 'home heading, bold part', 'ars-petal'), 'light' => _x('posts', 'home heading, light part', 'ars-petal')]); ?>
    <?php if (Blog::url() !== '') : ?>
      <a href="<?php echo esc_url(Blog::url()); ?>" class="btn-white h-10 shrink-0 rounded-xl px-4 text-sm"><?php esc_html_e('All posts', 'ars-petal'); ?><?php Icon::render('arrow-right', 'size-4'); ?></a>
    <?php endif; ?>
  </div>
  <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($posts as $post) : ?>
      <?php Template::render('cards/post', ['post' => $post]); ?>
    <?php endforeach; ?>
  </div>
</section>
