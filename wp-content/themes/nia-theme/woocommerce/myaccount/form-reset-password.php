<?php
/**
 * Lost password reset form — theme override of WooCommerce's
 * myaccount/form-reset-password.php, styled to match form-login.php since
 * it's reached one click away from it.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="px-margin-mobile md:px-margin-desktop py-12 md:py-20">
	<div class="max-w-md mx-auto bg-off-white sunlight-shadow rounded-xl p-8 md:p-12">
		<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-2"><?php esc_html_e( 'Member Access', 'nia-theme' ); ?></p>
		<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-8"><?php esc_html_e( 'Set a New Password', 'nia-theme' ); ?></h1>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password flex flex-col gap-6">

			<p class="font-body-md text-body-md text-on-surface-variant">
				<?php echo esc_html( apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ) ); ?>
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label class="nia-form-label" for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" autocomplete="new-password" required aria-required="true" />
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label class="nia-form-label" for="password_2"><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" required aria-required="true" />
			</p>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="btn-primary w-full" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

		</form>
	</div>
</div>
<?php
do_action( 'woocommerce_after_reset_password_form' );
