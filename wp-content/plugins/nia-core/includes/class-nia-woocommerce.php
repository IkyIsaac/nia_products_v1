<?php
/**
 * WooCommerce storefront customizations (PROJECT_PLAN.md Phase 5).
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Nia_Woocommerce.
 */
class Nia_Woocommerce {

	/**
	 * Wire hooks.
	 */
	public function __construct() {
		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_downloads_tab' ) );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'quick_add_text' ) );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'add_to_bag_text' ) );
		add_action( 'wp', array( $this, 'remove_default_pdp_sections' ) );
		add_action( 'wp', array( $this, 'remove_woocommerce_sidebar' ) );
		add_action( 'init', array( $this, 'register_dashboard_endpoints' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_and_reorder_dashboard_menu_items' ), 20 );
		add_action( 'woocommerce_account_my-rituals_endpoint', array( $this, 'render_my_rituals_endpoint' ) );
		add_action( 'woocommerce_account_wellness-profile_endpoint', array( $this, 'render_wellness_profile_endpoint' ) );
	}

	/**
	 * "My Rituals" (subscriptions) and "Wellness Profile" — dashboard.html's
	 * sidebar nav (DESIGN_SYSTEM.md §7, PROJECT_PLAN.md Phase 6). Both are
	 * real My Account endpoints, not dead links; their content is an honest
	 * empty state today since the data they'll eventually show doesn't exist
	 * yet (subscriptions are Phase 9; a wellness-profile data model was never
	 * scoped in ARCHITECTURE.md — this endpoint is a placeholder landing spot
	 * for it, not a fabricated feature).
	 */
	public function register_dashboard_endpoints() {
		add_rewrite_endpoint( 'my-rituals', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'wellness-profile', EP_ROOT | EP_PAGES );
	}

	/**
	 * Registering a rewrite endpoint (above) only makes the URL routable —
	 * it does not add it to My Account's nav, which WC builds from a fixed
	 * default list. This adds "My Rituals" and "Wellness Profile" as real
	 * nav items, renames "Account details" to "Settings" (dashboard.html's
	 * wording), and reorders everything to match the sidebar in
	 * dashboard.html: Overview, My Rituals, Order History, Wellness Profile,
	 * Addresses, Settings, Logout.
	 *
	 * @param array $items My Account menu items.
	 * @return array
	 */
	public function add_and_reorder_dashboard_menu_items( $items ) {
		$items['my-rituals']       = __( 'My Rituals', 'nia-theme' );
		$items['wellness-profile'] = __( 'Wellness Profile', 'nia-theme' );
		if ( isset( $items['edit-account'] ) ) {
			$items['edit-account'] = __( 'Settings', 'nia-theme' );
		}

		$ordered_keys = array( 'dashboard', 'my-rituals', 'orders', 'wellness-profile', 'edit-address', 'edit-account', 'customer-logout' );
		$ordered      = array();
		foreach ( $ordered_keys as $key ) {
			if ( isset( $items[ $key ] ) ) {
				$ordered[ $key ] = $items[ $key ];
				unset( $items[ $key ] );
			}
		}
		// Anything not explicitly ordered above (e.g. a plugin-added tab) keeps its place at the end.
		return $ordered + $items;
	}

	/**
	 * "My Rituals" endpoint content — empty state until Phase 9's
	 * subscription engine exists (ARCHITECTURE.md §5).
	 */
	public function render_my_rituals_endpoint() {
		?>
		<div class="nia-account-empty-state">
			<span class="material-symbols-outlined text-primary text-4xl">auto_awesome</span>
			<h2 class="font-headline-md text-headline-md mt-4 mb-2"><?php esc_html_e( 'No active ritual yet', 'nia-theme' ); ?></h2>
			<p class="font-body-md text-on-surface-variant mb-8">
				<?php esc_html_e( 'Subscriptions launch in a future update. Once available, your subscribed products, next delivery date, and pause/cancel controls will appear here.', 'nia-theme' ); ?>
			</p>
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/subscription/' ) ); ?>"><?php esc_html_e( 'Explore the Ritual', 'nia-theme' ); ?></a>
		</div>
		<?php
	}

	/**
	 * "Wellness Profile" endpoint content — no wellness-profile data model
	 * exists (ARCHITECTURE.md never scoped one); this is a genuine landing
	 * spot with real links (Journal), not fabricated personalization.
	 */
	public function render_wellness_profile_endpoint() {
		?>
		<div class="nia-account-empty-state">
			<span class="material-symbols-outlined text-primary text-4xl">book_5</span>
			<h2 class="font-headline-md text-headline-md mt-4 mb-2"><?php esc_html_e( 'Your Wellness Profile', 'nia-theme' ); ?></h2>
			<p class="font-body-md text-on-surface-variant mb-8">
				<?php esc_html_e( 'Personalized recommendations and a nutritional assessment are coming in a future update. In the meantime, explore the Wellness Journal for rituals and recipes.', 'nia-theme' ); ?>
			</p>
			<a class="btn-primary" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Visit the Journal', 'nia-theme' ); ?></a>
		</div>
		<?php
	}

	/**
	 * The Product Detail Page override (`content-single-product.php`,
	 * PROJECT_PLAN.md Phase 6) replaces the description/attributes tabs and
	 * up-sell grid with bespoke sections (Benefits, Purely Sourced, Daily
	 * Ritual, Testimonials) — only related products is kept from WC's
	 * default `woocommerce_after_single_product_summary` stack. Removed on
	 * `wp` (not `plugins_loaded`) so this runs after WooCommerce's own core
	 * hook registration, regardless of load order.
	 */
	public function remove_default_pdp_sections() {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	}

	/**
	 * This theme's layout is full-width everywhere (no sidebar concept exists
	 * on any other page) — but WooCommerce's Shop archive and single-product
	 * templates both call `do_action( 'woocommerce_sidebar' )`, which falls
	 * back to WordPress's default "sidebar-1" widget area when no
	 * `sidebar-shop.php` exists. That area had fresh-install default widgets
	 * (Search, Pages, Archives, Categories) still assigned, so it rendered
	 * as an unstyled, misaligned column after the product grid/related
	 * products. Removed on `wp` (not `plugins_loaded`) so this runs after
	 * WooCommerce's own core hook registration, regardless of load order.
	 */
	public function remove_woocommerce_sidebar() {
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	}

	/**
	 * The catalog is entirely physical products (seamoss powder/gel/raw/
	 * capsules) — no downloadable products exist or are planned, so the
	 * Downloads tab is dead UI in My Account (PROJECT_PLAN.md Phase 5).
	 *
	 * @param array $items My Account menu items.
	 * @return array
	 */
	public function remove_downloads_tab( $items ) {
		unset( $items['downloads'] );
		return $items;
	}

	/**
	 * This filter only affects loop contexts (Shop/category archives,
	 * related/cross-sell/up-sell grids) — the single Product page's Add to
	 * Bag button is bespoke markup (PROJECT_PLAN.md Phase 6), not this text —
	 * so it's safe to always say "Quick Add" here, matching collection.html
	 * and my-cart.html's upsell cards (DESIGN_SYSTEM.md §5/§10/§12).
	 *
	 * @return string
	 */
	public function quick_add_text() {
		return __( 'Quick Add', 'nia-theme' );
	}

	/**
	 * Single Product page's Add to Bag button copy, matching product.html
	 * (PROJECT_PLAN.md Phase 6).
	 *
	 * @return string
	 */
	public function add_to_bag_text() {
		return __( 'Add to Bag', 'nia-theme' );
	}
}
