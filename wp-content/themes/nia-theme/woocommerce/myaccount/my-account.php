<?php
/**
 * My Account page — theme override of WooCommerce's myaccount/my-account.php,
 * transcribed from nia-products/dashboard.html (PROJECT_PLAN.md Phase 6).
 * Provides the overall dark-sidebar + content layout; the sidebar's own
 * markup lives in navigation.php (this theme's override) so it can carry
 * its own Alpine mobile-drawer state independently of header.php's.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="flex min-h-screen">
	<?php do_action( 'woocommerce_account_navigation' ); ?>

	<div class="woocommerce-MyAccount-content flex-1 md:ml-72 px-margin-mobile md:px-margin-desktop py-12">
		<div class="max-w-6xl mx-auto">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
