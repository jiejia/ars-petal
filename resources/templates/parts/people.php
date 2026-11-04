<?php
/**
 * Hosts or guests: a small card with overlapping round avatars; hovering one shows the name, a click opens
 * the person's page.
 *
 * @var string $title Heading.
 * @var array<int,\WP_User> $users Users.
 * @var string $class Card padding classes.
 * @var string $heading Heading size classes.
 * @var string $size Avatar size classes.
 * @var string $letter Letter size of avatars without a picture.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

if ($users === []) {
    return;
}

$class ??= 'px-5 py-4';
$heading ??= 'text-base';
$size ??= 'size-11';
$letter ??= 'text-base';
?>
<section class="panel-sm flex items-center justify-between gap-4 <?php echo esc_attr($class); ?>" aria-label="<?php echo esc_attr($title); ?>">
  <h2 class="display <?php echo esc_attr($heading); ?>"><?php echo esc_html($title); ?></h2>
  <ul class="flex items-center">
    <?php foreach ($users as $index => $user) : ?>
      <li class="group relative transition-transform focus-within:z-10 hover:z-10 hover:-translate-y-1<?php echo $index > 0 ? ' -ml-3' : ''; ?>">
        <a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>" class="block rounded-full border-[3px] border-base-100" aria-label="<?php echo esc_attr($user->display_name); ?>">
          <?php Template::render('parts/avatar', ['user' => $user, 'class' => $size . ' rounded-full', 'text' => $letter]); ?>
        </a>
        <span role="tooltip" class="pointer-events-none absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-full bg-neutral px-3 py-1.5 text-xs font-medium text-neutral-content opacity-0 shadow-lg transition-opacity group-focus-within:opacity-100 group-hover:opacity-100"><?php echo esc_html($user->display_name); ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
