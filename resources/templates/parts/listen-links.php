<?php
/**
 * Podcast app links (the plugin's Subscription Settings) + RSS as <li> items.
 *
 * @var string $style "menu" (dropdown, with icons) or "list" (footer).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Navigation\Menu;
use Jiejia\ArsPetal\Podcast\Podcast;

foreach (Podcast::listenLinks() as $link) :
    if ($style === 'menu') : ?>
      <li><a<?php echo Menu::externalAttributes($link['url']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?>><?php Icon::render($link['icon'], 'size-4'); ?><?php echo esc_html($link['label']); ?></a></li>
    <?php else : ?>
      <li><a<?php echo Menu::externalAttributes($link['url']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?> class="hover:text-base-content"><?php echo esc_html($link['label']); ?></a></li>
    <?php endif;
endforeach;
