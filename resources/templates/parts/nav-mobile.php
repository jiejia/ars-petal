<?php
/**
 * A menu tree as daisyUI menu items: items with children fold out with <details> (expanded by default), any depth.
 * The first entry of a folded list links to the parent itself.
 *
 * @var array<int,array<string,mixed>> $items Menu tree (Menu::tree()).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;
use Jiejia\ArsPetal\Navigation\Menu;

foreach ($items as $item) : ?>
  <li>
    <?php if ($item['children'] === []) : ?>
      <a href="<?php echo esc_url($item['url']); ?>"<?php echo $item['current'] ? ' class="menu-active" aria-current="page"' : ''; ?>><?php echo esc_html($item['label']); ?></a>
    <?php else : ?>
      <details open>
        <summary><?php echo esc_html($item['label']); ?></summary>
        <ul>
          <?php if ($item['url'] !== '' && $item['url'] !== '#') : ?>
            <?php // The summary only folds the list, so the parent's own link comes first. ?>
            <li><a href="<?php echo esc_url($item['url']); ?>"<?php echo $item['current'] && ! Menu::anyCurrent($item['children']) ? ' class="menu-active" aria-current="page"' : ''; ?>><?php echo esc_html($item['label']); ?></a></li>
          <?php endif; ?>
          <?php Template::render('parts/nav-mobile', ['items' => $item['children']]); ?>
        </ul>
      </details>
    <?php endif; ?>
  </li>
<?php endforeach;
