<?php
/**
 * My Account sidebar nav — theme override of WooCommerce's
 * myaccount/navigation.php, transcribed from nia-products/dashboard.html
 * (PROJECT_PLAN.md Phase 6). Desktop: fixed dark-gradient sidebar. Mobile:
 * the same hamburger + full-screen overlay drawer pattern as the main site
 * nav (DESIGN_SYSTEM.md §7), but listing these account links instead —
 * built as its own Alpine component since header.php's mobile drawer only
 * covers the primary site menu, not My Account.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

$nia_icons = array(
	'dashboard'        => 'dashboard',
	'my-rituals'       => 'auto_awesome',
	'orders'           => 'history',
	'wellness-profile' => 'book_5',
	'edit-address'     => 'location_on',
	'edit-account'     => 'settings',
	'customer-logout'  => 'logout',
);

$nia_menu_items = wc_get_account_menu_items();
?>

<button
	type="button"
	class="md:hidden fixed top-24 right-margin-mobile z-40 bg-on-background text-off-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg"
	x-data
	@click="$dispatch('nia-dashboard-menu-toggle')"
	aria-label="<?php esc_attr_e( 'Open account menu', 'nia-theme' ); ?>"
>
	<span class="material-symbols-outlined">menu</span>
</button>

<div x-data="{ dashboardMenuOpen: false }" @nia-dashboard-menu-toggle.window="dashboardMenuOpen = true">
	<!-- Desktop sidebar -->
	<aside class="hidden md:flex w-72 side-nav-gradient flex-col fixed left-0 top-32 bottom-0 z-30 p-base overflow-y-auto">
		<nav class="woocommerce-MyAccount-navigation mt-8 flex flex-col gap-2" aria-label="<?php esc_attr_e( 'Account pages', 'nia-theme' ); ?>">
			<?php foreach ( $nia_menu_items as $nia_endpoint => $nia_label ) : ?>
				<a
					class="px-6 py-4 flex items-center gap-4 rounded-lg premium-transition <?php echo wc_is_current_account_menu_item( $nia_endpoint ) ? 'bg-primary/10 text-primary-fixed' : ( 'customer-logout' === $nia_endpoint ? 'text-error hover:bg-white/5' : 'text-warm-grey hover:bg-white/5' ); ?>"
					href="<?php echo esc_url( wc_get_account_endpoint_url( $nia_endpoint ) ); ?>"
					<?php echo wc_is_current_account_menu_item( $nia_endpoint ) ? 'aria-current="page"' : ''; ?>
				>
					<span class="material-symbols-outlined"<?php echo wc_is_current_account_menu_item( $nia_endpoint ) ? ' style="font-variation-settings: \'FILL\' 1"' : ''; ?>><?php echo esc_html( $nia_icons[ $nia_endpoint ] ?? 'circle' ); ?></span>
					<span class="font-label-lg text-label-lg"><?php echo esc_html( $nia_label ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
	</aside>

	<!-- Mobile drawer -->
	<div
		x-cloak
		x-show="dashboardMenuOpen"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
		class="fixed inset-0 z-50 side-nav-gradient flex flex-col md:hidden"
		role="dialog"
		aria-modal="true"
	>
		<div class="flex items-center justify-between px-margin-mobile py-base">
			<span class="font-label-lg text-label-lg text-off-white uppercase tracking-widest"><?php esc_html_e( 'My Account', 'nia-theme' ); ?></span>
			<button class="text-off-white hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'nia-theme' ); ?>" @click="dashboardMenuOpen = false">
				<span class="material-symbols-outlined">close</span>
			</button>
		</div>
		<nav class="flex-1 flex flex-col items-center justify-center gap-6" aria-label="<?php esc_attr_e( 'Account pages', 'nia-theme' ); ?>">
			<?php foreach ( $nia_menu_items as $nia_endpoint => $nia_label ) : ?>
				<a
					class="font-label-lg text-label-lg <?php echo ( 'customer-logout' === $nia_endpoint ) ? 'text-error' : 'text-warm-grey hover:text-primary-fixed'; ?>"
					href="<?php echo esc_url( wc_get_account_endpoint_url( $nia_endpoint ) ); ?>"
					@click="dashboardMenuOpen = false"
				><?php echo esc_html( $nia_label ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
