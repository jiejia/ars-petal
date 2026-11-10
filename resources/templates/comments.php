<?php
/**
 * Comments (comments_template()): each comment in a small card, replies in gray boxes inside it, and
 * the form in a large card.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Comments\CommentForm;
use Jiejia\ArsPetal\Podcast\Episode;

if (post_password_required()) {
    return;
}

$count = (int) get_comments_number();
?>
<section id="comments" class="mt-8">
  <?php if ($count > 0) : ?>
    <h2 class="display px-2 text-2xl"><?php echo esc_html(Episode::from(get_post()) ? __('Listener comments', 'ars-petal') : __('Reader comments', 'ars-petal')); ?> <span class="mute"><?php echo esc_html(number_format_i18n($count)); ?></span></h2>
    <ol class="comment-list mt-4 space-y-3">
      <?php wp_list_comments(['callback' => [CommentForm::class, 'comment'], 'style' => 'ol', 'avatar_size' => 40]); ?>
    </ol>
    <?php the_comments_pagination(); ?>
  <?php endif; ?>

  <?php if (comments_open()) : ?>
    <div class="panel mt-4 p-6 sm:p-8">
      <?php comment_form(); ?>
    </div>
  <?php elseif ($count > 0) : ?>
    <p class="mute mt-6 px-2 text-sm"><?php esc_html_e('Comments are closed.', 'ars-petal'); ?></p>
  <?php endif; ?>
</section>
