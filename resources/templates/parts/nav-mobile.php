<?php
/**
 * A menu tree as daisyUI menu items: items with children fold out with <details> (expanded by default), any depth.
 * The first entry of a folded list links to the parent itself ("All …").
 *
 * @var array<int,array<string,mixed>> $items Menu tree (Menu::tree()).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Template;

foreach ($items as $item) : ?>
  <li>
    <?php if ($item['children'] === []) : ?>
      <a href="<?php echo esc_url($item['url']); ?>"<?php echo $item['current'] ? ' class="menu-active" aria-current="page"' : ''; ?>><?php echo esc_html($item['label']); ?></a>
    <?php else : ?>
      <details open>
        <summary><?php echo esc_html($item['label']); ?></summary>
        <ul>
          <?php Template::render('parts/nav-mobile', ['items' => $item['children']]); ?>
        </ul>
      </details>
    <?php endif; ?>
  </li>
<?php endforeach;
