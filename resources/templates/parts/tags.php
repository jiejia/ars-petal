<?php
/**
 * Tags as small gray chips: in a sidebar card, or centered under an article ($inline).
 *
 * @var array<int,\WP_Term> $terms Tags.
 * @var callable|null $url Term => URL; the tag archive by default.
 * @var string $current Slug of the selected tag.
 * @var bool $inline Under an article: "#tag" pills with a top rule, no card.
 * @var bool $counts Whether to show the post counts.
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

if ($terms === []) {
    return;
}

$url ??= static fn (\WP_Term $term): string => (string) get_tag_link($term);
$current ??= '';
$inline ??= false;
$counts ??= false;
?>
<?php if ($inline) : ?>
  <div class="mt-8 flex flex-wrap justify-center gap-2 border-t border-base-300 pt-6" aria-label="<?php esc_attr_e('Tags', 'ars-petal'); ?>">
    <?php foreach ($terms as $term) : ?>
      <a href="<?php echo esc_url($url($term)); ?>" class="rounded-full bg-base-200 px-4 py-1.5 text-sm hover:bg-base-300">#<?php echo esc_html($term->name); ?></a>
    <?php endforeach; ?>
  </div>
<?php else : ?>
  <section class="panel-sm p-3 lg:p-4" aria-label="<?php esc_attr_e('Tags', 'ars-petal'); ?>">
    <h2 class="display hidden text-xl lg:block"><?php esc_html_e('Tags', 'ars-petal'); ?></h2>
    <div class="flex flex-wrap gap-2 lg:mt-3 lg:gap-1.5">
      <?php foreach ($terms as $term) :
          $active = $current === $term->slug;
          ?>
        <a href="<?php echo esc_url($url($term)); ?>" class="chip !h-8 !px-3 !text-[0.8rem] <?php echo $active ? '!bg-neutral !text-neutral-content' : ''; ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
          <?php echo esc_html($term->name); ?>
          <?php if ($counts) : ?>
            <span class="text-xs opacity-60"><?php echo (int) $term->count; ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>
