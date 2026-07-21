<?php
/**
 * Lost password form — theme override of WooCommerce's
 * myaccount/form-lost-password.php, styled to match form-login.php since
 * it's reached one click away from it.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="px-margin-mobile md:px-margin-desktop py-12 md:py-20">
	<div class="max-w-md mx-auto bg-off-white sunlight-shadow rounded-xl p-8 md:p-12">
		<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-2"><?php esc_html_e( 'Member Access', 'nia-theme' ); ?></p>
		<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-8"><?php esc_html_e( 'Reset Password', 'nia-theme' ); ?></h1>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password flex flex-col gap-6">

			<p class="font-body-md text-body-md text-on-surface-variant">
				<?php echo esc_html( apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ) ); ?>
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label class="nia-form-label" for="user_login"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="btn-primary w-full" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		</form>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
