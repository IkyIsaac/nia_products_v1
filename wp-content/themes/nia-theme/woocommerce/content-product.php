<?php
/**
 * Product card — theme override of WooCommerce's content-product.php,
 * transcribed from nia-products/collection.html's product card
 * (PROJECT_PLAN.md Phase 6, DESIGN_SYSTEM.md §5/§10/§14 item 8).
 *
 * Badge logic is data-driven, not hardcoded: on-sale products show the
 * actual discount percentage; Featured products (no sale) show "Best
 * Seller" — both admin-controlled from the ordinary product edit screen,
 * consistent with ARCHITECTURE.md §9a's count-agnostic rule.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$nia_badge_text = '';
if ( $product->is_on_sale() && $product->get_regular_price() > 0 ) {
	$nia_discount   = round( ( ( $product->get_regular_price() - $product->get_price() ) / $product->get_regular_price() ) * 100 );
	$nia_badge_text = sprintf( /* translators: %d: discount percentage */ esc_html__( 'SALE — %d%% OFF', 'nia-theme' ), $nia_discount );
} elseif ( $product->is_featured() ) {
	$nia_badge_text = __( 'BEST SELLER', 'nia-theme' );
}

$nia_category_list = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ' ) );
?>
<li <?php wc_product_class( 'group relative', $product ); ?>>
	<div class="product-image-container relative aspect-[4/5] bg-warm-grey overflow-hidden mb-6">
		<a href="<?php the_permalink(); ?>" class="absolute inset-0 z-0" aria-label="<?php the_title_attribute(); ?>">
			<?php
			echo wp_kses_post(
				get_the_post_thumbnail(
					$product->get_id(),
					'woocommerce_thumbnail',
					array( 'class' => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105' )
				)
			);
			?>
		</a>
		<?php if ( $nia_badge_text ) : ?>
			<div class="absolute top-4 left-4 z-10 bg-tertiary-container/90 px-3 py-1 rounded-full pointer-events-none">
				<span class="font-label-md text-label-md text-on-tertiary-container"><?php echo esc_html( $nia_badge_text ); ?></span>
			</div>
		<?php endif; ?>
		<!--
			Sibling of the permalink <a> above, not nested inside it — an <a>
			(Quick Add, from woocommerce_template_loop_add_to_cart()) nested
			inside another <a> is invalid HTML; browsers silently split the
			outer link when they hit it, which is what caused the unlabeled
			"phantom" second button on hover before this was split into two
			siblings.
		-->
		<div class="quick-add-overlay absolute inset-0 z-20 bg-black/5 flex items-end p-6 opacity-0 translate-y-4 transition-all duration-300 pointer-events-none [&_a]:pointer-events-auto">
			<div class="nia-quick-add w-full [&_a]:!w-full [&_a]:!block [&_a]:!bg-on-background [&_a]:!text-off-white [&_a]:!py-4 [&_a]:!font-label-lg [&_a]:!text-label-lg [&_a]:!uppercase [&_a]:!tracking-widest [&_a]:!text-center hover:[&_a]:!bg-primary [&_a]:!transition-colors [&_a]:!duration-300">
				<?php woocommerce_template_loop_add_to_cart( array( 'quantity' => 1 ) ); ?>
			</div>
		</div>
	</div>
	<a href="<?php the_permalink(); ?>" class="block space-y-1">
		<h3 class="font-headline-md text-headline-md"><?php the_title(); ?></h3>
		<?php if ( $nia_category_list ) : ?>
			<p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $nia_category_list ); ?></p>
		<?php endif; ?>
		<p class="font-label-lg text-label-lg font-bold mt-2"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
	</a>
</li>
