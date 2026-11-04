<?php
/**
 * Home: the subscribe block on a glow background. The e-mail form posts to the newsletter service set in
 * the Customizer; without one, the feed address can be copied. Beside it, a code window with the feed and
 * the podcast apps from the plugin's Subscription Settings.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

use Jiejia\ArsPetal\Customizer\Options;
use Jiejia\ArsPetal\Libraries\Icon;
use Jiejia\ArsPetal\Podcast\Podcast;

$podcast = Podcast::isActive();
$action = Options::get('newsletter_action');
$field = Options::get('newsletter_field') ?: 'email';
$title = Options::get('newsletter_title');
$text = Options::get('newsletter_text') ?: ($action !== ''
    ? __('New episodes and full notes in your inbox. No ads, unsubscribe any time.', 'ars-petal')
    : __('Add the feed to your podcast app and every new episode arrives by itself.', 'ars-petal'));
$feed = Podcast::feedUrl();
$apps = array_values(array_filter(Podcast::listenLinks(), static fn (array $link): bool => $link['url'] !== $feed));
$schedule = Options::get('schedule');
$line = 1;
?>
<section class="wash relative mt-28 grid items-center gap-10 overflow-hidden rounded-[2rem] px-6 py-14 sm:px-12 lg:grid-cols-2 lg:py-16">
  <div class="min-w-0">
    <h2 class="text-4xl sm:text-5xl">
      <?php if ($title !== '') : ?>
        <span class="display block !font-black"><?php echo esc_html($title); ?></span>
      <?php else : ?>
        <span class="display block !font-black"><?php esc_html_e('Don’t want to miss one?', 'ars-petal'); ?></span>
        <span class="display-light block"><?php echo esc_html($action !== '' ? __('We’ll send it to you', 'ars-petal') : __('Subscribe to the feed', 'ars-petal')); ?></span>
      <?php endif; ?>
    </h2>
    <p class="display-light mute mt-4 max-w-md text-lg"><?php echo esc_html($text); ?></p>
    <?php if ($action !== '') : ?>
      <form action="<?php echo esc_url($action); ?>" method="post" target="_blank" class="mt-8 flex max-w-md flex-col gap-3 sm:flex-row">
        <label for="newsletter-email" class="sr-only"><?php esc_html_e('E-mail address', 'ars-petal'); ?></label>
        <input id="newsletter-email" name="<?php echo esc_attr($field); ?>" type="email" required placeholder="you@example.com" class="field h-14 min-w-0 flex-1 rounded-2xl">
        <button type="submit" class="btn-black h-14 shrink-0 rounded-2xl px-7"><?php esc_html_e('Subscribe', 'ars-petal'); ?></button>
      </form>
    <?php else : ?>
      <div class="mt-8 flex max-w-md flex-col gap-3 sm:flex-row" x-data="copyText('<?php echo esc_js($feed); ?>')">
        <button type="button" class="btn-black h-14 flex-1 rounded-2xl px-7" @click="copy()">
          <span x-show="!copied"><?php Icon::render('copy', 'size-5'); ?></span><span x-show="copied" x-cloak><?php Icon::render('check', 'size-5'); ?></span>
          <span x-text="copied ? '<?php echo esc_js(__('Copied', 'ars-petal')); ?>' : '<?php echo esc_js(__('Copy the feed address', 'ars-petal')); ?>'"><?php esc_html_e('Copy the feed address', 'ars-petal'); ?></span>
        </button>
        <a href="<?php echo esc_url($feed); ?>" data-no-swup class="btn-white h-14 rounded-2xl px-7"><?php Icon::render('rss', 'size-5'); ?>RSS</a>
      </div>
    <?php endif; ?>
  </div>
  <div class="mockup-code w-full min-w-0 rounded-3xl bg-neutral text-neutral-content shadow-2xl shadow-base-content/20" aria-hidden="true">
    <pre data-prefix="<?php echo (int) $line++; ?>" class="opacity-50"><code><?php echo esc_html($podcast ? __('# add the show in any podcast app', 'ars-petal') : __('# follow in any feed reader', 'ars-petal')); ?></code></pre>
    <pre data-prefix="<?php echo (int) $line++; ?>"><code><span class="text-secondary">subscribe</span> <?php echo esc_html($feed); ?></code></pre>
    <?php if ($apps !== []) : ?>
      <pre data-prefix="<?php echo (int) $line++; ?>" class="opacity-50"><code></code></pre>
      <pre data-prefix="<?php echo (int) $line++; ?>" class="opacity-50"><code><?php echo esc_html(sprintf(/* translators: %s: site name. */ __('# or search for “%s” on', 'ars-petal'), get_bloginfo('name'))); ?></code></pre>
      <?php foreach (array_chunk(array_slice($apps, 0, 6), 2) as $pair) : ?>
        <pre data-prefix="<?php echo (int) $line++; ?>"><code><?php foreach ($pair as $app) : ?><span class="text-accent">✓</span> <?php echo esc_html(str_pad($app['label'], 16)); ?><?php endforeach; ?></code></pre>
      <?php endforeach; ?>
    <?php endif; ?>
    <pre data-prefix="<?php echo (int) $line++; ?>" class="bg-warning text-warning-content"><code><?php echo esc_html($schedule !== '' ? $schedule : ($podcast ? __('new episodes land here first', 'ars-petal') : __('new posts land here first', 'ars-petal'))); ?></code></pre>
  </div>
</section>
