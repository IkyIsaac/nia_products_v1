<?php
/**
 * Shop / Category archive — theme override of WooCommerce's default
 * archive-product.php, transcribed from nia-products/collection.html
 * (PROJECT_PLAN.md Phase 6). Keeps WooCommerce's own hooks for notices,
 * ordering, and pagination (still native/functional) but replaces the
 * page header and adds the category filter/tab row (DESIGN_SYSTEM.md §10,
 * reusing the journal/FAQ tab pattern) so an arbitrary, growing number of
 * categories works without new UI (ARCHITECTURE.md §9a).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$nia_is_category  = is_tax( 'product_cat' );
$nia_current_term = $nia_is_category ? get_queried_object() : null;

if ( $nia_is_category && $nia_current_term instanceof WP_Term ) {
	$nia_shop_title = $nia_current_term->name;
	$nia_shop_desc  = $nia_current_term->description
		? $nia_current_term->description
		: __( 'Pure Tanzanian Seamoss, harvested for vitality. Ethically sourced from the pristine waters of the Tanzanian coast.', 'nia-theme' );
} else {
	$nia_shop_title = __( 'The Collection', 'nia-theme' );
	$nia_shop_desc  = __( 'Pure Tanzanian Seamoss, harvested for vitality. Ethically sourced from the pristine waters of the Tanzanian coast.', 'nia-theme' );
}
?>

<!-- Editorial Header -->
<section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mb-section-gap">
	<div class="flex flex-col md:flex-row md:items-end justify-between gap-base border-b border-warm-grey pb-base">
		<div class="max-w-2xl">
			<span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em] mb-4 block"><?php esc_html_e( 'Lishe Bora', 'nia-theme' ); ?></span>
			<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-4"><?php echo esc_html( $nia_shop_title ); ?></h1>
			<p class="font-body-lg text-body-lg text-on-surface-variant"><?php echo esc_html( wp_strip_all_tags( $nia_shop_desc ) ); ?></p>
		</div>
	</div>
</section>

<!-- Category filter/tab row (DESIGN_SYSTEM.md §10/§14 item 12 — journal/FAQ tab pattern) -->
<?php
$nia_categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( get_option( 'default_product_cat' ) ),
	)
);
if ( ! is_wp_error( $nia_categories ) && ! empty( $nia_categories ) ) :
	?>
	<section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mb-section-gap">
		<nav class="flex flex-wrap items-center gap-8" aria-label="<?php esc_attr_e( 'Product categories', 'nia-theme' ); ?>">
			<a
				class="font-label-lg text-label-lg uppercase tracking-widest transition-colors duration-300 <?php echo ! $nia_is_category ? 'text-primary' : 'text-on-surface-variant hover:text-primary'; ?>"
				href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
			><?php esc_html_e( 'All', 'nia-theme' ); ?></a>
			<?php foreach ( $nia_categories as $nia_cat ) : ?>
				<a
					class="font-label-lg text-label-lg uppercase tracking-widest transition-colors duration-300 <?php echo ( $nia_is_category && $nia_current_term->term_id === $nia_cat->term_id ) ? 'text-primary' : 'text-on-surface-variant hover:text-primary'; ?>"
					href="<?php echo esc_url( get_term_link( $nia_cat ) ); ?>"
				><?php echo esc_html( $nia_cat->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	</section>
<?php endif; ?>

<section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mb-section-gap">
	<?php if ( woocommerce_product_loop() ) : ?>

		<?php
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );
		?>

		<?php woocommerce_product_loop_start(); ?>

			<?php
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>

		<?php woocommerce_product_loop_end(); ?>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
		?>

	<?php else : ?>

		<?php do_action( 'woocommerce_no_products_found' ); ?>

	<?php endif; ?>
</section>

<?php
do_action( 'woocommerce_after_main_content' );
// No woocommerce_sidebar() call — this theme has no sidebar concept
// anywhere (Nia_Woocommerce::remove_woocommerce_sidebar() also strips the
// hook globally, since WC's own single-product.php template calls it too).
get_footer( 'shop' );
