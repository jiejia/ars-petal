<?php
/**
 * The header's top-level menu: icon links; an item with children opens a rounded dropdown card
 * whose columns are its children (see Menu::columns()), with a third level as small chips (deeper
 * levels join those chips).
 *
 * @var array<int,array<string,mixed>> $items Menu tree (Menu::tree()).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Html;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Navigation\Menu;

$link = static function (array $item, string $class = ''): string {
    return Html::attributes([
        'href' => $item['url'],
        'class' => trim($class . ($item['current'] ? ' is-on' : '')) ?: null,
        'aria-current' => $item['current'] ? 'page' : null,
    ]);
};

foreach ($items as $item) :
    if ($item['children'] === []) : ?>
      <a<?php echo $link($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?>><?php echo $item['icon'] !== '' ? Icon::svg($item['icon'], '') : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><?php echo esc_html($item['label']); ?></a>
    <?php else :
        $columns = Menu::columns($item);
        ?>
      <div class="has-mega">
        <a<?php echo $link($item, 'tn'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?> aria-haspopup="true"><?php echo $item['icon'] !== '' ? Icon::svg($item['icon'], '') : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><?php echo esc_html($item['label']); ?><?php Icon::render('chevron-down', '!size-3.5 opacity-60'); ?></a>
        <div class="mega" style="--cols: <?php echo (int) min(4, count($columns)); ?>">
          <?php foreach ($columns as $column) : ?>
            <div>
              <h3>
                <?php if ($column['url'] !== '') : ?>
                  <a href="<?php echo esc_url($column['url']); ?>" class="!m-0 !p-0 !bg-transparent hover:!text-primary"><?php echo esc_html($column['title']); ?></a>
                <?php else : ?>
                  <?php echo esc_html($column['title']); ?>
                <?php endif; ?>
              </h3>
              <div class="mt-3">
                <?php foreach ($column['links'] as $child) : ?>
                  <a<?php echo $link($child); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?>><?php echo esc_html($child['label']); ?></a>
                  <?php if ($child['children'] !== []) : ?>
                    <div class="sub">
                      <?php foreach (Menu::flatten($child['children']) as $grandchild) : ?>
                        <a<?php echo $link($grandchild); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?>><?php echo esc_html($grandchild['label']); ?></a>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif;
endforeach;
