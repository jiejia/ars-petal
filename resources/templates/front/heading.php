<?php
/**
 * A home section heading in the two weights: a black part and a light part, e.g. "Latest" + "episodes".
 *
 * @var string $strong Bold part.
 * @var string $light Light part.
 * @var string $id Heading ID, for aria-labelledby.
 * @var string $class Size classes.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

$id ??= '';
$class ??= 'text-2xl min-[420px]:text-3xl sm:text-5xl';
// "Latest episodes" needs a space between the parts, "最新节目" does not.
$space = preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{P}\s]$/u', $strong) ? '' : ' ';
?>
<h2<?php echo $id !== '' ? ' id="' . esc_attr($id) . '"' : ''; ?> class="<?php echo esc_attr($class); ?>"><span class="display !font-black"><?php echo esc_html($strong); ?></span><?php echo esc_html($space); ?><span class="display-light"><?php echo esc_html($light); ?></span></h2>
