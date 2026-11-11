<?php
/**
 * Hosts or guests: a small card with overlapping round avatars; hovering one shows the name, a click opens
 * the person's page.
 *
 * @var string $title Heading.
 * @var array<int,\WP_User> $users Users.
 * @var string $light Optional light second part of the title, as in the two-weight home headings.
 * @var string $lightClass Classes for the light part, e.g. to hide it where the card is narrow.
 * @var string $class Card padding classes.
 * @var string $heading Heading size classes.
 * @var string $size Avatar size classes.
 * @var string $letter Letter size of avatars without a picture.
 * @var string $overlap Negative margin that overlaps neighbouring avatars (half an avatar).
 * @var bool $keepEmpty Still show the card, with a dashed border and no avatars, when there are no users.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

$keepEmpty ??= false;
if ($users === [] && !$keepEmpty) {
    return;
}

$light ??= '';
$lightClass ??= '';
// "Host lineup" needs a space between the parts, "主播阵容" does not (as in front/heading).
$space = preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{P}\s]$/u', $title) ? '' : ' ';
$class ??= 'px-5 py-4';
$heading ??= 'text-base';
$size ??= 'size-11';
$letter ??= 'text-base';
$overlap ??= '-ml-5.5';
// At most five avatars; the rest collapse into a "+N" tile.
$max = 5;
$extra = count($users) - $max;
$users = array_slice($users, 0, $max);
// Empty: a dashed outline, like the missing previous / next episode card.
$frame = $users === [] ? ' !border-2 !border-dashed !border-base-content/20' : '';
?>
<section class="panel-sm flex items-center justify-between gap-4 <?php echo esc_attr($class . $frame); ?>" aria-label="<?php echo esc_attr($title . $light); ?>">
  <?php if ($light !== '') : ?>
    <h2 class="<?php echo esc_attr($heading); ?>"><span class="display !font-black"><?php echo esc_html($title); ?></span><span class="display-light <?php echo esc_attr($lightClass); ?>"><?php echo esc_html($space . $light); ?></span></h2>
  <?php else : ?>
    <h2 class="display <?php echo esc_attr($heading); ?>"><?php echo esc_html($title); ?></h2>
  <?php endif; ?>
  <?php if ($users === []) : ?>
    <span class="<?php echo esc_attr($size); ?> shrink-0" aria-hidden="true"></span>
  <?php else : ?>
  <ul class="flex items-center">
    <?php foreach ($users as $index => $user) : ?>
      <li class="group relative transition-transform focus-within:z-10 hover:z-10 hover:-translate-y-1<?php echo $index > 0 ? ' ' . esc_attr($overlap) : ''; ?>">
        <a href="<?php echo esc_url(get_author_posts_url($user->ID)); ?>" class="block rounded-full" aria-label="<?php echo esc_attr($user->display_name); ?>">
          <?php Template::render('parts/avatar', ['user' => $user, 'class' => $size . ' rounded-full', 'text' => $letter]); ?>
        </a>
        <span role="tooltip" class="pointer-events-none absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-full bg-neutral px-3 py-1.5 text-xs font-medium text-neutral-content opacity-0 shadow-lg transition-opacity group-focus-within:opacity-100 group-hover:opacity-100"><?php echo esc_html($user->display_name); ?></span>
      </li>
    <?php endforeach; ?>
    <?php if ($extra > 0) : ?>
      <li class="relative <?php echo esc_attr($overlap); ?>">
        <span class="petal-avatar relative grid shrink-0 place-items-center overflow-hidden rounded-full <?php echo esc_attr($size); ?>" role="img" aria-label="<?php echo esc_attr(sprintf(/* translators: %d: number of people not shown */ _n('%d more person', '%d more people', $extra, 'ars-petal'), $extra)); ?>">
          <span class="absolute inset-0 grid place-items-center rounded-full bg-neutral font-bold tabular-nums text-neutral-content <?php echo esc_attr($letter); ?>"><?php echo esc_html('+' . min($extra, 99)); ?></span>
        </span>
      </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>
</section>
