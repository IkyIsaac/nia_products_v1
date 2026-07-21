<?php
/**
 * Login / Register form — theme override of WooCommerce's
 * myaccount/form-login.php. WooCommerce renders this template directly
 * (not wrapped by myaccount/my-account.php's sidebar layout, since that
 * layout only applies once logged in), so this template owns its own
 * centered page container.
 *
 * Both cards are flex columns with their primary button pushed to
 * mt-auto — the two forms have different amounts of content above the
 * button (login has a remember-me/lost-password row, register doesn't),
 * so without this the two CTA buttons would land at different heights.
 *
 * Field logic (which inputs render) is left to WooCommerce's own template
 * conditionals — only markup/classes are restyled here.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_registration_open = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="px-margin-mobile md:px-margin-desktop py-12">
	<div class="max-w-5xl mx-auto grid grid-cols-1 <?php echo $nia_registration_open ? 'md:grid-cols-2' : ''; ?> gap-gutter items-stretch">

		<div class="<?php echo $nia_registration_open ? '' : 'max-w-md mx-auto w-full'; ?> bg-off-white sunlight-shadow rounded-xl p-8 md:p-12 flex flex-col">
			<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-2"><?php esc_html_e( 'Member Access', 'nia-theme' ); ?></p>
			<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-2"><?php esc_html_e( 'Log In', 'nia-theme' ); ?></h1>
			<p class="text-on-surface-variant font-body-md mb-8"><?php esc_html_e( 'Welcome back — sign in to track your orders and manage your rituals.', 'nia-theme' ); ?></p>

			<form class="woocommerce-form woocommerce-form-login login flex-1 flex flex-col gap-6" method="post" novalidate>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label class="nia-form-label" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label class="nia-form-label" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row flex items-center justify-between flex-wrap gap-4 mb-0">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme flex items-center gap-2 font-body-md text-body-md">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<a class="btn-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

				<button type="submit" class="btn-primary w-full mt-auto" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</form>
		</div>

		<?php if ( $nia_registration_open ) : ?>
			<div class="bg-off-white sunlight-shadow rounded-xl p-8 md:p-12 flex flex-col">
				<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-2"><?php esc_html_e( 'New Here?', 'nia-theme' ); ?></p>
				<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-2"><?php esc_html_e( 'Create an Account', 'nia-theme' ); ?></h1>
				<p class="text-on-surface-variant font-body-md mb-8"><?php esc_html_e( 'Join Nia to save your details, track orders, and unlock your wellness profile.', 'nia-theme' ); ?></p>

				<form method="post" class="woocommerce-form woocommerce-form-register register flex-1 flex flex-col gap-6" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="nia-form-label" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
							<input type="text" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
						</p>
					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label class="nia-form-label" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
						<input type="email" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label class="nia-form-label" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
							<input type="password" class="nia-form-input woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
						</p>
					<?php else : ?>
						<p class="font-body-md text-body-md text-on-surface-variant"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="btn-primary w-full mt-auto" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
