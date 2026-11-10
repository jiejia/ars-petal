<?php

namespace Jiejia\ArsPetal\Customizer;

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Branding\Logo;

/**
 * Customizer picker for the logo icon: a grid of radio buttons, each showing the icon.
 *
 * Only load this class inside customize_register, where WP_Customize_Control exists.
 */
final class LogoIconControl extends \WP_Customize_Control
{
    /**
     * Control type.
     *
     * @var string
     */
    public $type = 'ars_petal_logo_icon';

    /**
     * Print the picker. The controls pane does not load theme.css, so the icon colors are set here.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $name = '_customize-radio-' . $this->id;
        ?>
        <style>
          .petal-logo-picker { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px; margin-top: 6px; }
          .petal-logo-picker label { display: grid; place-items: start center; align-content: start; gap: 4px; overflow-wrap: anywhere; padding: 8px 2px 6px; border: 1px solid #dcdcde; border-radius: 8px; background: #fff; cursor: pointer; font-size: 11px; line-height: 1.2; text-align: center; }
          .petal-logo-picker input { position: absolute; opacity: 0; }
          .petal-logo-picker label:has(input:checked) { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
          .petal-logo-picker label:has(input:focus-visible) { outline: 2px solid #2271b1; outline-offset: 1px; }
          .petal-logo-picker svg, .petal-logo-picker .mark { width: 30px; height: 30px; }
          .petal-logo-picker .mark { display: inline-grid; grid-template-columns: repeat(2, 1fr); gap: 2px; }
          .petal-logo-picker .mark i { display: block; border-radius: 9999px 9999px 9999px 2px; }
          .petal-logo-picker .mark i:nth-child(1), .petal-logo-picker .lp { background: oklch(45% 0.24 277.023); fill: oklch(45% 0.24 277.023); }
          .petal-logo-picker .mark i:nth-child(2), .petal-logo-picker .ls { background: oklch(65% 0.241 354.308); fill: oklch(65% 0.241 354.308); }
          .petal-logo-picker .mark i:nth-child(3), .petal-logo-picker .lw { background: oklch(82% 0.189 84.429); fill: oklch(82% 0.189 84.429); }
          .petal-logo-picker .mark i:nth-child(4), .petal-logo-picker .la { background: oklch(77% 0.152 181.912); fill: oklch(77% 0.152 181.912); }
          .petal-logo-picker .mark i:nth-child(2) { transform: rotate(90deg); }
          .petal-logo-picker .mark i:nth-child(3) { transform: rotate(-90deg); }
          .petal-logo-picker .mark i:nth-child(4) { transform: rotate(180deg); }
          .petal-logo-picker .lc { fill: oklch(21% 0.006 285.885); }
          .petal-logo-picker .lb { fill: #fff; }
          .petal-logo-picker .lp-s { stroke: oklch(45% 0.24 277.023); }
          .petal-logo-picker .ls-s { stroke: oklch(65% 0.241 354.308); }
          .petal-logo-picker .la-s { stroke: oklch(77% 0.152 181.912); }
          .petal-logo-picker .lb-s { stroke: #fff; }
        </style>
        <?php if ($this->label !== '') : ?>
          <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <?php endif; ?>
        <?php if ($this->description !== '') : ?>
          <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
        <?php endif; ?>
        <div class="petal-logo-picker" role="radiogroup" aria-label="<?php echo esc_attr($this->label); ?>">
          <?php foreach (Logo::labels() as $slug => $label) : ?>
            <label>
              <input type="radio" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($slug); ?>" <?php $this->link(); ?> <?php checked($this->value(), $slug); ?> />
              <?php echo Logo::icon($slug); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG shipped with the theme. ?>
              <span><?php echo esc_html($label); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
        <?php
    }
}
