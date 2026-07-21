<?php
/**
 * My Addresses — theme override of WooCommerce's myaccount/my-address.php
 * (the /edit-address/ index listing billing + shipping), styled to match
 * the account section convention (page heading + off-white sunlight-shadow
 * cards, per dashboard.php).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$nia_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$nia_customer_id
	);
} else {
	$nia_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$nia_customer_id
	);
}
?>

<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-2"><?php esc_html_e( 'Addresses', 'nia-theme' ); ?></h1>
<p class="text-on-surface-variant font-body-md mb-8">
	<?php echo esc_html( apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ) ); ?>
</p>

<div class="grid grid-cols-1 <?php echo count( $nia_addresses ) > 1 ? 'md:grid-cols-2' : ''; ?> gap-gutter">
	<?php foreach ( $nia_addresses as $nia_name => $nia_title ) : ?>
		<?php $nia_address = wc_get_account_formatted_address( $nia_name ); ?>
		<div class="woocommerce-Address bg-off-white sunlight-shadow rounded-xl p-8">
			<header class="woocommerce-Address-title title flex items-center justify-between gap-4 mb-6">
				<h2 class="font-headline-md text-headline-md text-on-background"><?php echo esc_html( $nia_title ); ?></h2>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $nia_name ) ); ?>" class="btn-link edit">
					<?php echo $nia_address ? esc_html__( 'Edit', 'woocommerce' ) : esc_html__( 'Add', 'woocommerce' ); ?>
				</a>
			</header>
			<address class="not-italic font-body-md text-on-surface-variant leading-relaxed">
				<?php
				if ( $nia_address ) {
					echo wp_kses_post( $nia_address );
				} else {
					esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
				}
				do_action( 'woocommerce_my_account_after_my_address', $nia_name );
				?>
			</address>
		</div>
	<?php endforeach; ?>
</div>
