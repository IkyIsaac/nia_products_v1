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
		add_action( 'woocommerce_before_account_orders', array( $this, 'render_orders_heading' ) );
		add_action( 'woocommerce_my_account_my_orders_column_order-status', array( $this, 'render_order_status_pill' ) );
	}

	/**
	 * "Order History" page heading — the native Orders endpoint
	 * (myaccount/orders.php, unmodified) never had one; every other My
	 * Account section does (dashboard.php's welcome header, or the h1 this
	 * theme's edit-address/edit-account overrides add). Hooked rather than
	 * template-copied since a heading is the only thing missing here — see
	 * the CSS below for the actual table restyle, which only needed
	 * WooCommerce's own stable class names (ARCHITECTURE.md "hooks before
	 * template overrides").
	 *
	 * @param bool $has_orders Whether the customer has any orders — fires
	 *                         either way, so the heading shows on the empty
	 *                         state too.
	 */
	public function render_orders_heading( $has_orders ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by the woocommerce_before_account_orders signature; heading shows regardless of its value.
		echo '<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-8">' . esc_html__( 'Order History', 'nia-theme' ) . '</h1>';
	}

	/**
	 * Order status as a colored pill (matches dashboard.php's Recent Orders
	 * preview treatment) instead of the default plain status text. Hooked
	 * via woocommerce_my_account_my_orders_column_order-status — orders.php
	 * checks has_action() for this exact hook name and, when present, skips
	 * its own default output entirely and calls this instead.
	 *
	 * @param WC_Order $order Order being listed.
	 */
	public function render_order_status_pill( $order ) {
		$status  = $order->get_status();
		$palette = array(
			'completed'  => 'bg-primary/10 text-primary',
			'processing' => 'bg-surface-container text-on-background',
			'on-hold'    => 'bg-tertiary-container text-on-tertiary-container',
			'pending'    => 'bg-tertiary-container text-on-tertiary-container',
			'cancelled'  => 'bg-error-container text-on-error-container',
			'failed'     => 'bg-error-container text-on-error-container',
			'refunded'   => 'bg-surface-container-low text-on-surface-variant',
		);
		$class = $palette[ $status ] ?? 'bg-surface-container-low text-on-surface-variant';
		printf(
			'<span class="px-3 py-1 %s font-label-md text-label-md rounded-full uppercase">%s</span>',
			esc_attr( $class ),
			esc_html( wc_get_order_status_name( $status ) )
		);
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
	 * "My Rituals" endpoint content. Real data once a subscription exists
	 * (Nia_Subscriptions, PROJECT_PLAN.md Phase 9) — honest empty state
	 * otherwise. Pause/cancel are intentionally not offered yet: what
	 * "pause" means mid-term (skip one cycle vs. shift the whole schedule
	 * vs. forfeit it) is a business rule that hasn't been decided, so this
	 * only ever displays status, never lets the customer change it — see
	 * RISKS.md R12 and PROJECT_PLAN.md Phase 9's own checklist, which
	 * treats pause/cancel as separate follow-up work.
	 */
	public function render_my_rituals_endpoint() {
		$subscriptions = Nia_Subscriptions::get_customer_subscriptions( get_current_user_id() );

		if ( ! $subscriptions ) {
			?>
			<div class="nia-account-empty-state">
				<span class="material-symbols-outlined text-primary text-4xl">auto_awesome</span>
				<h2 class="font-headline-md text-headline-md mt-4 mb-2"><?php esc_html_e( 'No active ritual yet', 'nia-theme' ); ?></h2>
				<p class="font-body-md text-on-surface-variant mb-8">
					<?php esc_html_e( 'Subscribe to a ritual and your subscribed products, delivery schedule, and status will appear here.', 'nia-theme' ); ?>
				</p>
				<a class="btn-primary" href="<?php echo esc_url( home_url( '/subscription/' ) ); ?>"><?php esc_html_e( 'Explore the Ritual', 'nia-theme' ); ?></a>
			</div>
			<?php
			return;
		}

		$cadences = Nia_Subscriptions::get_cadences();
		?>
		<div class="space-y-6">
			<?php foreach ( $subscriptions as $subscription ) : ?>
				<?php
				$cadence_key = get_post_meta( $subscription->ID, '_nia_cadence', true );
				$cadence     = $cadences[ $cadence_key ] ?? null;
				$products    = (array) get_post_meta( $subscription->ID, '_nia_products', true );
				$schedule    = (array) get_post_meta( $subscription->ID, '_nia_schedule', true );
				$status      = get_post_meta( $subscription->ID, '_nia_status', true );
				$status      = $status ? $status : 'active';
				$next        = Nia_Subscriptions::get_next_delivery_date( $schedule );
				?>
				<div class="bg-off-white sunlight-shadow rounded-xl p-8">
					<div class="flex items-start justify-between gap-4 flex-wrap mb-6">
						<div>
							<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-1"><?php echo esc_html( $cadence['label'] ?? $cadence_key ); ?></p>
							<h3 class="font-headline-md text-headline-md text-on-background"><?php echo esc_html( $cadence['term_label'] ?? '' ); ?></h3>
						</div>
						<span class="px-3 py-1 bg-primary/10 text-primary font-label-md text-label-md rounded-full uppercase"><?php echo esc_html( ucfirst( $status ) ); ?></span>
					</div>

					<ul class="space-y-2 mb-6">
						<?php foreach ( $products as $line ) : ?>
							<?php $product = wc_get_product( $line['product_id'] ); ?>
							<?php if ( $product ) : ?>
								<li class="font-body-md text-on-surface-variant">
									<?php
									echo esc_html( $product->get_name() . ' × ' . (int) $line['quantity'] . ' — ' );
									echo wp_kses_post( wc_price( $line['unit_price'] ) );
									esc_html_e( ' / delivery', 'nia-theme' );
									?>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>

					<div class="flex items-center justify-between gap-4 pt-4 border-t border-warm-grey">
						<p class="font-label-md text-label-md uppercase tracking-widest text-on-surface-variant">
							<?php if ( $next ) : ?>
								<?php
								/* translators: %s: next delivery date */
								echo esc_html( sprintf( __( 'Next delivery: %s', 'nia-theme' ), date_i18n( get_option( 'date_format' ), strtotime( $next ) ) ) );
								?>
							<?php else : ?>
								<?php esc_html_e( 'All deliveries complete', 'nia-theme' ); ?>
							<?php endif; ?>
						</p>
					</div>
				</div>
			<?php endforeach; ?>
			<p class="font-body-md text-sm text-on-surface-variant"><?php esc_html_e( 'Pausing or cancelling a ritual is coming in a future update — contact us if you need changes in the meantime.', 'nia-theme' ); ?></p>
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
