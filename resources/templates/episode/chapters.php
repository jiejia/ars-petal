<?php
/**
 * Chapters tab: numbered rows; a click plays from there, the current chapter is highlighted.
 * Episodes without chapters get a short note instead.
 *
 * @var array<int,array{start:int,end:int,title:string,url:string}> $chapters
 * @var bool $playable Whether the player can play this episode.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\View\Format;

if ($chapters === []) : ?>
  <div x-show="tab === 'chapters'" x-cloak role="tabpanel">
    <?php Template::render('episode/empty', ['text' => __('This episode has no chapters.', 'ars-petal')]); ?>
  </div>
  <?php
  return;
endif;
?>
<ol x-show="tab === 'chapters'" x-cloak role="tabpanel">
  <?php foreach ($chapters as $index => $chapter) :
      $active = sprintf('chapterActive(%d, %d)', $chapter['start'], $chapter['end']);
      $tag = $playable ? 'button' : 'div';
      ?>
    <li>
      <<?php echo tag_escape($tag); ?><?php echo $playable ? ' type="button" @click="playAt(' . (int) $chapter['start'] . ')"' : ''; ?> class="flex w-full items-center gap-3 rounded-2xl px-3 py-3.5 text-left transition-colors hover:bg-base-200" :class="<?php echo esc_attr($active); ?> && 'bg-base-200 font-semibold'">
        <span class="display grid size-9 shrink-0 place-items-center rounded-full bg-base-200 text-xs" :class="<?php echo esc_attr($active); ?> && '!bg-primary !text-primary-content'"><?php echo (int) $index + 1; ?></span>
        <span class="min-w-0 flex-1"><?php echo esc_html($chapter['title']); ?></span>
        <span x-show="<?php echo esc_attr($active); ?>" x-cloak class="eq" aria-label="<?php esc_attr_e('Current chapter', 'ars-petal'); ?>"><i></i><i></i><i></i></span>
        <span class="mute shrink-0 text-sm tabular-nums"><?php echo esc_html(Format::duration($chapter['start'])); ?></span>
      </<?php echo tag_escape($tag); ?>>
    </li>
  <?php endforeach; ?>
</ol>
