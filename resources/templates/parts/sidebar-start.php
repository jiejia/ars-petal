<?php
/**
 * Opens the left sidebar of a daisyUI drawer layout (https://daisyui.com/components/drawer/). The page wraps
 * sidebar and content in `<div class="drawer lg:drawer-open">` and gives the content column `drawer-content`.
 * From lg the sidebar is a sticky column; below lg it is hidden in a drawer that slides in from the right,
 * opened by a fixed tab at the middle of the left edge. Close it with parts/sidebar-end.
 *
 * @var string $class Extra classes for the sidebar on large screens (e.g. sticky offset, max height).
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Libraries\Icon;

$class ??= '';
?>
<?php // Driven by the open and close buttons (app.js), so the checkbox itself stays out of the tab order. ?>
<input id="petal-drawer" type="checkbox" class="drawer-toggle" tabindex="-1" aria-hidden="true">
<button type="button" data-drawer-open="petal-drawer" aria-controls="petal-drawer-panel" class="fixed left-0 top-1/2 z-30 grid h-14 w-9 -translate-y-1/2 cursor-pointer place-items-center rounded-r-2xl border border-l-0 border-base-300 bg-base-100 text-base-content shadow-lg shadow-base-content/10 transition-colors hover:bg-base-200 lg:hidden" aria-label="<?php esc_attr_e('Open sidebar', 'ars-petal'); ?>"><?php Icon::render('panel-left-open', 'size-[1.1rem]'); ?></button>
<div class="drawer-side max-lg:z-[100000] lg:self-start lg:h-auto <?php echo esc_attr($class); ?>" x-data @click="if ($event.target.closest('a')) document.getElementById('petal-drawer').checked = false">
  <label for="petal-drawer" class="drawer-overlay" aria-label="<?php esc_attr_e('Close', 'ars-petal'); ?>"></label>
  <?php // daisyUI only ships min-width drawer variants, so the right-hand slide below lg is spelled out: park the panel off the right edge, bring it back when the toggle is checked. ?>
  <div id="petal-drawer-panel" class="min-h-full w-80 max-w-[85vw] space-y-3 max-lg:justify-self-end max-lg:translate-x-full! max-lg:[.drawer-toggle:checked~.drawer-side_&]:translate-x-0! bg-base-100 p-4 lg:min-h-0 lg:w-72 lg:max-w-none lg:bg-transparent lg:p-0">
    <div class="-mx-4 -mt-4 flex items-center justify-between gap-3 border-b border-base-300 px-4 py-3 lg:hidden">
      <h2 class="display truncate text-xl"><?php esc_html_e('Sidebar', 'ars-petal'); ?></h2>
      <button type="button" data-drawer-close="petal-drawer" class="grid size-9 shrink-0 cursor-pointer place-items-center rounded-full hover:bg-base-200" aria-label="<?php esc_attr_e('Close', 'ars-petal'); ?>"><?php Icon::render('x', 'size-5'); ?></button>
    </div>
