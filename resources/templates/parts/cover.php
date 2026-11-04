<?php
/**
 * A cover image, or a placeholder made of the theme's glows and petal mark when there is none.
 *
 * @var string $url Image URL.
 * @var string $alt Alt text.
 * @var string $class Image classes.
 * @var bool $lazy Whether to lazy-load.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

$lazy ??= true;
?>
<?php if ($url !== '') : ?>
  <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>"<?php echo $lazy ? ' loading="lazy"' : ''; ?> decoding="async" class="<?php echo esc_attr($class); ?>">
<?php else : ?>
  <span class="wash <?php echo esc_attr($class); ?> grid place-items-center" role="img" aria-label="<?php echo esc_attr($alt); ?>">
    <span class="mark scale-150" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
  </span>
<?php endif; ?>
