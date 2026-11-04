<?php
/**
 * Home: one sentence over big numbers (episodes, average length, categories, total plays) on a soft glow.
 * The sentence is the Customizer's "Home statement" (a new line in it breaks the line on wide screens), else the site tagline. Numbers that are zero are left out.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Podcast\Podcast;
use Jiejia\ArsPetal\View\Format;

$stats = Podcast::stats();

if ($stats['episodes'] === 0) {
    return;
}

$statement = Options::get('statement') ?: trim((string) get_bloginfo('description'));
$categories = count(Podcast::categories());
$numbers = array_filter([
    [$stats['episodes'], number_format_i18n($stats['episodes']), _n('episode', 'episodes', $stats['episodes'], 'ars-petal')],
    [$stats['duration'], max(1, (int) round($stats['duration'] / 60)) . '′', __('Average length', 'ars-petal')],
    [$categories, number_format_i18n($categories), _n('category', 'categories', $categories, 'ars-petal')],
    [$stats['plays'], Format::compact($stats['plays']), __('Total plays', 'ars-petal')],
], static fn (array $number): bool => $number[0] > 0);
$columns = [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4'][count($numbers)];
?>
<section class="relative isolate mt-16 py-10 text-center" aria-label="<?php esc_attr_e('Podcast in numbers', 'ars-petal'); ?>">
  <div class="glow left-1/2 top-0 h-72 w-[36rem] max-w-full -translate-x-1/2 bg-[var(--glow-a)]" aria-hidden="true"></div>
  <?php if ($statement !== '') : ?>
    <h2 class="display mx-auto max-w-4xl text-balance text-4xl !font-black leading-[1.15] sm:text-6xl"><?php echo implode('<br class="hidden sm:block">', array_map('esc_html', preg_split('/\R+/', $statement))); ?></h2>
  <?php endif; ?>
  <div class="<?php echo $statement !== '' ? 'mt-16' : ''; ?> grid grid-cols-2 gap-y-12 <?php echo esc_attr($columns); ?>">
    <?php foreach ($numbers as [, $value, $label]) : ?>
      <div><p class="big-num text-5xl sm:text-6xl"><?php echo esc_html($value); ?></p><p class="mute mt-3 text-sm"><?php echo esc_html($label); ?></p></div>
    <?php endforeach; ?>
  </div>
</section>
