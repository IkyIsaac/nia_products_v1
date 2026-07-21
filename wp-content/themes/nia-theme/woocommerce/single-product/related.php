<?php
/**
 * Related Products — theme override of WooCommerce's
 * single-product/related.php. The default template renders a bare
 * `<section class="related products">` with an unstyled `<h2>` and a plain
 * `<ul class="products">` grid — none of which match the container width,
 * heading typography, or grid treatment every other PDP section uses
 * (Benefits, Purely Sourced, Testimonials, Reviews). This override reuses
 * WooCommerce's own related-product query/visibility logic (the
 * `$related_products` list passed in by woocommerce_related_products())
 * and `content-product.php`'s card markup per item — only the wrapper is
 * bespoke.
 *
 * @package Nia_Theme
 *
 * @var WC_Product[] $related_products Related products to display.
 */

defined( 'ABSPATH' ) || exit;

if ( ! $related_products ) {
	return;
}

$nia_heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'You May Also Like', 'nia-theme' ) );
?>
<section class="related products mt-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
	<?php if ( $nia_heading ) : ?>
		<h2 class="font-display-lg text-headline-lg mb-12"><?php echo esc_html( $nia_heading ); ?></h2>
	<?php endif; ?>

	<?php // content-product.php outputs a root <li> — it must sit inside a real <ul> (not a <div>), or the <li> falls outside any list context and the browser's default UA stylesheet gives it back a bullet (list-style only inherits from a ul/ol ancestor). ?>
	<ul class="products">
		<?php foreach ( $related_products as $nia_related_product ) : ?>
			<?php
			$post_object = get_post( $nia_related_product->get_id() );

			setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

			wc_get_template_part( 'content', 'product' );
			?>
		<?php endforeach; ?>
	</ul>
</section>
<?php
wp_reset_postdata();
