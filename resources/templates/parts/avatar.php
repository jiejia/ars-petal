<?php
/**
 * A user's avatar on a rounded tile, or the first letter of their name on a theme color.
 *
 * The letter is always printed; the avatar image covers it. Gravatar is asked with d=404, so a
 * user without one gets an error and the image removes itself, leaving the letter. Once the image loads, the
 * colored letter is removed, so no color shows around or through the image (as in the prototype).
 *
 * @var \WP_User $user User.
 * @var string $class Size and radius, e.g. "size-12 rounded-2xl".
 * @var string $text Letter size.
 * @var bool $ring Whether to draw the gradient ring (.avatar-ring).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\View\Format;

$class ??= 'size-12 rounded-2xl';
$text ??= 'text-lg';
$ring ??= true;
$colors = ['bg-accent text-accent-content', 'bg-secondary text-secondary-content', 'bg-info text-info-content', 'bg-success text-success-content', 'bg-warning text-warning-content', 'bg-primary text-primary-content'];
$url = get_option('show_avatars') ? (string) get_avatar_url($user->ID, ['size' => 192, 'default' => '404']) : '';
?>
<span class="relative block shrink-0 overflow-hidden font-bold <?php echo esc_attr(($ring ? 'avatar-ring ' : '') . $class); ?>" aria-hidden="true">
  <span class="absolute inset-0 grid place-items-center <?php echo esc_attr($colors[$user->ID % count($colors)] . ' ' . $text); ?>"><?php echo esc_html(Format::initial($user->display_name)); ?></span>
  <?php if ($url !== '') : ?>
    <img src="<?php echo esc_url($url); ?>" alt="" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover" onload="this.previousElementSibling?.remove()" onerror="this.remove()">
  <?php endif; ?>
</span>
