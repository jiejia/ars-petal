<?php
/**
 * Site footer, printed by TemplateLoader::part('footer'): closes the swup container (after the JSON of
 * the episodes shown on this page), then the footer and wp_footer, where the plugin prints its player.
 *
 * Footer columns: the footer menu's items with children each become a column; items without children
 * are gathered in a "Browse" column. Without a footer menu, the header menu is used. A "Listen"
 * column lists the podcast apps.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Navigation\Menu;
use Jiejia\ArsPetal\Podcast\EpisodeRegistry;
use Jiejia\ArsPetal\Podcast\Podcast;

$about = Podcast::setting('description') ?: get_bloginfo('description');
$copyright = Options::get('footer_text') ?: sprintf('© %s %s', wp_date('Y'), get_bloginfo('name'));
$columns = Menu::columns(['label' => __('Browse', 'ars-petal'), 'children' => Menu::tree(has_nav_menu('footer') ? 'footer' : 'primary')]);
$listen = Podcast::listenLinks();
?>
<?php EpisodeRegistry::print(); ?>
</main>

<footer class="site-footer mt-16 border-t border-base-300 bg-base-200 pb-16">
  <div class="mx-auto grid max-w-6xl grid-cols-2 gap-x-6 gap-y-10 px-4 pt-16 text-center sm:px-6 md:grid-cols-6 md:text-left">
    <div class="col-span-2">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-2.5">
        <span class="mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span class="display text-2xl !font-bold tracking-tight"><?php bloginfo('name'); ?></span>
      </a>
      <?php if ($about !== '') : ?>
        <p class="display-light mute mx-auto mt-4 max-w-xs text-lg leading-snug md:mx-0"><?php echo esc_html(wp_trim_words($about, 40)); ?></p>
      <?php endif; ?>
      <div class="mt-6 flex flex-wrap items-center justify-center gap-1.5 md:justify-start">
        <?php foreach (array_slice(array_reverse($listen), 0, 5) as $link) : ?>
          <a<?php echo Menu::externalAttributes($link['url']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?> class="btn-white size-10 rounded-full" aria-label="<?php echo esc_attr($link['label']); ?>"><?php Icon::render($link['icon'], 'size-4'); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div id="petal-footer-nav" data-swup-sync class="contents">
      <?php foreach (array_slice($columns, 0, 3) as $column) : ?>
        <nav aria-label="<?php echo esc_attr($column['title']); ?>">
          <p class="display text-sm">
            <?php if ($column['url'] !== '') : ?>
              <a href="<?php echo esc_url($column['url']); ?>" class="hover:text-primary"><?php echo esc_html($column['title']); ?></a>
            <?php else : ?>
              <?php echo esc_html($column['title']); ?>
            <?php endif; ?>
          </p>
          <ul class="mute mt-4 space-y-2.5 text-sm">
            <?php foreach ($column['links'] as $item) : ?>
              <li><a class="hover:text-base-content<?php echo $item['current'] ? ' text-base-content' : ''; ?>" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </nav>
      <?php endforeach; ?>
    </div>
    <?php // Two columns on small screens: when Listen would sit alone in its row, it takes the whole row so it is centered. ?>
    <nav aria-label="<?php esc_attr_e('Listen', 'ars-petal'); ?>"<?php echo count(array_slice($columns, 0, 3)) % 2 === 0 ? ' class="col-span-2 md:col-span-1"' : ''; ?>>
      <p class="display text-sm"><?php echo esc_html(Podcast::isActive() ? __('Listen', 'ars-petal') : __('Follow', 'ars-petal')); ?></p>
      <ul class="mute mt-4 space-y-2.5 text-sm">
        <?php foreach ($listen as $link) : ?>
          <li><a<?php echo Menu::externalAttributes($link['url']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by Html::attributes(). ?> class="hover:text-base-content"><?php echo esc_html($link['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
  <div class="mx-auto mt-14 flex max-w-6xl flex-wrap items-center justify-center gap-4 border-t border-base-300 px-4 pt-6 text-sm sm:px-6 md:justify-between">
    <p class="mute"><?php echo esc_html($copyright); ?></p>
    <label class="mute flex cursor-pointer items-center gap-2.5" x-data>
      <?php Icon::render('sun', 'size-4'); ?>
      <input type="checkbox" class="toggle toggle-sm toggle-primary" :checked="$store.ui.dark" @change="$store.ui.toggleTheme()" aria-label="<?php esc_attr_e('Night mode', 'ars-petal'); ?>">
      <?php Icon::render('moon', 'size-4'); ?>
    </label>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
