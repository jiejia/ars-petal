<?php
/**
 * Home: one sentence (Customizer "Home statement") over a row of huge numbers. Only real numbers are
 * shown: episodes, average length, total plays and topics (posts and categories in blog mode).
 *
 * @var array<int,array{value:string,label:string}> $numbers Numbers to show.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;

$statement = Options::get('statement');

if (count($numbers) < 2 && $statement === '') {
    return;
}
?>
<section class="relative isolate mt-32 text-center">
  <div class="glow left-1/2 top-0 h-72 w-[36rem] max-w-full -translate-x-1/2 bg-[var(--glow-a)]" aria-hidden="true"></div>
  <?php if ($statement !== '') : ?>
    <h2 class="display mx-auto max-w-4xl text-balance text-4xl !font-black leading-[1.15] sm:text-6xl"><?php echo esc_html($statement); ?></h2>
  <?php endif; ?>
  <?php if (count($numbers) >= 2) : ?>
    <div class="<?php echo $statement !== '' ? 'mt-20' : ''; ?> grid grid-cols-2 gap-y-12 <?php echo esc_attr(match (count($numbers)) { 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', default => 'md:grid-cols-4' }); ?>">
      <?php foreach ($numbers as $number) : ?>
        <div><p class="big-num text-5xl sm:text-6xl"><?php echo esc_html($number['value']); ?></p><p class="mute mt-3 text-sm"><?php echo esc_html($number['label']); ?></p></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
