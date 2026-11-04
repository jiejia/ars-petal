<?php
/**
 * A menu tree as daisyUI menu items: items with children fold out with <details>, any depth.
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
      <details<?php echo $item['current'] ? ' open' : ''; ?>>
        <summary><?php echo esc_html($item['label']); ?></summary>
        <ul>
          <?php if ($item['url'] !== '' && $item['url'] !== '#') : ?>
            <li><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html(sprintf(/* translators: %s: menu item, e.g. "Podcast". */ __('All: %s', 'ars-petal'), $item['label'])); ?></a></li>
          <?php endif; ?>
          <?php Template::render('parts/nav-mobile', ['items' => $item['children']]); ?>
        </ul>
      </details>
    <?php endif; ?>
  </li>
<?php endforeach;
