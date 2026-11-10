<?php
/**
 * One comment; WordPress closes the <li> after the replies.
 *
 * @var \WP_Comment $comment
 * @var array<string,mixed> $args
 * @var int $depth
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Podcast\Episode;

$post = get_post((int) $comment->comment_post_ID);
$episode = Episode::from($post);
$userId = (int) $comment->user_id;
// Hosts: the post author, or the episode's hosts.
$host = $userId > 0 && ($userId === (int) $post?->post_author || ($episode && in_array($userId, wp_list_pluck($episode->hosts(), 'ID'), true)));
?>
<li id="comment-<?php comment_ID(); ?>" <?php comment_class($depth > 1 ? 'list-none' : 'panel-sm list-none p-5', $comment); ?>>
  <div class="flex gap-3">
    <?php echo get_avatar($comment, $depth > 1 ? 32 : 40, '', '', ['class' => 'shrink-0 rounded-full ' . ($depth > 1 ? 'size-8' : 'size-10')]); ?>
    <div class="min-w-0 flex-1">
      <p class="text-sm">
        <span class="font-semibold"><?php comment_author_link($comment); ?></span>
        <?php if ($host) : ?>
          <span class="ml-2 rounded-full bg-primary px-2 py-0.5 text-[0.65rem] text-primary-content"><?php esc_html_e('Host', 'ars-petal'); ?></span>
        <?php endif; ?>
        <a href="<?php echo esc_url(get_comment_link($comment)); ?>" class="mute ml-2 text-xs"><?php echo esc_html(get_comment_date('Y.m.d', $comment)); ?></a>
        <?php edit_comment_link(__('Edit', 'ars-petal'), '<span class="mute ml-2 text-xs [&_a]:hover:text-base-content">', '</span>', $comment->comment_ID); ?>
      </p>
      <?php if ($comment->comment_approved === '0') : ?>
        <p class="mt-1 text-xs text-warning"><?php esc_html_e('Your comment is awaiting moderation.', 'ars-petal'); ?></p>
      <?php endif; ?>
      <div class="comment-content mt-1 leading-relaxed <?php echo $depth > 1 ? 'mute text-sm' : ''; ?>"><?php comment_text($comment); ?></div>
      <?php
      comment_reply_link(array_merge($args, [
          'depth' => $depth,
          'max_depth' => (int) $args['max_depth'],
          'before' => '<div class="mute mt-3 text-sm font-medium [&_a]:hover:text-base-content">',
          'after' => '</div>',
      ]), $comment);
      ?>
    </div>
  </div>
