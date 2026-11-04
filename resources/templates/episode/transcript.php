<?php
/**
 * Transcript tab: one row per line with the speaker's letter avatar (a color per speaker), name and time;
 * the time plays from there. Formats the theme cannot read (PDF, HTML) are linked instead; episodes
 * without a transcript get a short note.
 *
 * @var array{url:string,cues:array<int,array{start:int|null,speaker:string,text:string}>} $transcript
 * @var bool $playable Whether the player can play this episode.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Format;

if ($transcript['url'] === '') : ?>
  <div x-show="tab === 'transcript'" x-cloak role="tabpanel">
    <?php Template::render('episode/empty', ['text' => __('This episode has no transcript.', 'ars-petal')]); ?>
  </div>
  <?php
  return;
endif;

$colors = ['bg-accent text-accent-content', 'bg-secondary text-secondary-content', 'bg-info text-info-content', 'bg-warning text-warning-content'];
$speakers = array_values(array_unique(array_filter(array_column($transcript['cues'], 'speaker'))));
?>
<div x-show="tab === 'transcript'" x-cloak role="tabpanel">
  <?php foreach ($transcript['cues'] as $cue) :
      $time = $cue['start'] !== null ? Format::duration($cue['start']) : '';
      ?>
    <div class="flex gap-3 rounded-2xl p-3 hover:bg-base-200">
      <?php if ($cue['speaker'] !== '') : ?>
        <span class="grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold <?php echo esc_attr($colors[(int) array_search($cue['speaker'], $speakers, true) % count($colors)]); ?>" aria-hidden="true"><?php echo esc_html(Format::initial($cue['speaker'])); ?></span>
      <?php endif; ?>
      <div class="min-w-0 flex-1">
        <?php if ($cue['speaker'] !== '' || $time !== '') : ?>
          <p class="text-sm">
            <?php if ($cue['speaker'] !== '') : ?>
              <span class="font-semibold"><?php echo esc_html($cue['speaker']); ?></span>
            <?php endif; ?>
            <?php if ($time !== '' && $playable) : ?>
              <button type="button" class="mute ml-2 text-xs tabular-nums hover:text-base-content" @click="playAt(<?php echo (int) $cue['start']; ?>)" aria-label="<?php echo esc_attr(sprintf(/* translators: %s: timestamp. */ __('Play from %s', 'ars-petal'), $time)); ?>"><?php echo esc_html($time); ?></button>
            <?php elseif ($time !== '') : ?>
              <span class="mute ml-2 text-xs tabular-nums"><?php echo esc_html($time); ?></span>
            <?php endif; ?>
          </p>
        <?php endif; ?>
        <p class="mt-0.5 leading-relaxed"><?php echo esc_html($cue['text']); ?></p>
      </div>
    </div>
  <?php endforeach; ?>
  <p class="mute flex flex-wrap items-center gap-2 px-3 pt-3 text-xs">
    <?php if ($transcript['cues'] === []) : ?>
      <?php esc_html_e('The transcript is available as a file.', 'ars-petal'); ?>
    <?php endif; ?>
    <a href="<?php echo esc_url($transcript['url']); ?>" data-no-swup target="_blank" rel="noopener" class="inline-flex items-center gap-1 hover:text-base-content"><?php Icon::render('arrow-up-right', 'size-4'); ?><?php esc_html_e('Open the transcript file', 'ars-petal'); ?></a>
  </p>
</div>
