<?php
/**
 * Site logo link: the uploaded image (header only, when "Uploaded image" is chosen) or the chosen
 * icon + site title.
 *
 * @var string $place "header" or "footer".
 * @package ArsPetal
 */

use Jiejia\ArsPetal\Branding\Logo;
use Jiejia\ArsPetal\Customizer\Options;

defined('ABSPATH') || exit;

$place = ($place ?? 'header') === 'footer' ? 'footer' : 'header';

if ($place === 'header' && Logo::usesImage()) {
    the_custom_logo();
    return;
}

$link = $place === 'footer' ? 'inline-flex items-center gap-2.5' : 'flex min-w-0 shrink-0 items-center gap-2.5';
$name = $place === 'footer' ? 'display text-2xl !font-bold tracking-tight' : 'display truncate text-[1.35rem] !font-bold tracking-tight';
?>
<a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo esc_attr($link); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo('name')); ?>">
  <?php echo Logo::icon(Options::logoIcon()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG shipped with the theme. ?>
  <span class="<?php echo esc_attr($name); ?>"><?php bloginfo('name'); ?></span>
</a>
