<?php
/**
 * Edit address form — theme override of WooCommerce's
 * myaccount/form-edit-address.php (billing/shipping), styled to match the
 * account section convention (page heading + off-white sunlight-shadow
 * card) and the .nia-form-input/.nia-form-label treatment from
 * myaccount/form-login.php. Each field's own 'class' (form-row-wide vs
 * form-row-first/last, set by WC_Countries::get_address_fields()) drives
 * the grid span/pairing — see .nia-account-form CSS.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing Address', 'woocommerce' ) : esc_html__( 'Shipping Address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' );
?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>

	<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-8">
		<?php echo esc_html( apply_filters( 'woocommerce_my_account_edit_address_title', $nia_page_title, $load_address ) ); ?>
	</h1>

	<div class="nia-account-form bg-off-white sunlight-shadow rounded-xl p-8 md:p-12 max-w-3xl">
		<form method="post" novalidate>

			<div class="woocommerce-address-fields">
				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

				<div class="woocommerce-address-fields__field-wrapper grid grid-cols-1 md:grid-cols-2 gap-x-6">
					<?php
					foreach ( $address as $key => $field ) {
						$field['input_class'][] = 'nia-form-input';
						$field['label_class'][] = 'nia-form-label';
						woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					}
					?>
				</div>

				<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

				<p class="mt-8 mb-0">
					<button type="submit" class="btn-primary" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
					<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
					<input type="hidden" name="action" value="edit_address" />
				</p>
			</div>

		</form>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
