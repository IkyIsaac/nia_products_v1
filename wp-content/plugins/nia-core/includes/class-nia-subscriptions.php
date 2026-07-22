<?php
/**
 * Subscriptions (PROJECT_PLAN.md Phase 9, ARCHITECTURE.md §5/§9) — v1 model:
 * the customer pays for the entire committed term in one checkout (no
 * recurring/renewal charge), so this sidesteps the mobile-money
 * silent-re-billing problem RISKS.md R1 flags entirely for now. A cadence
 * determines a fixed term (order count); the customer picks one or more
 * products to bundle under that cadence, all sharing one delivery schedule.
 *
 * Data model: `nia_subscription` CPT (admin-only, native list-table UI —
 * no bespoke admin screen needed) instead of WooCommerce Subscriptions'
 * data model or a real WooCommerce order per delivery cycle, since every
 * cycle after the first isn't a separate payable event — it's a shipping
 * checkpoint against an already-paid order. Keeps WooCommerce's own Orders
 * screen/reports free of fake $0 orders.
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Nia_Subscriptions.
 */
class Nia_Subscriptions {

	/**
	 * Cart/order item meta key flagging a line item as part of a
	 * subscription bundle (vs. a normal one-time purchase in the same cart).
	 */
	const CART_ITEM_KEY = 'nia_subscription';

	/**
	 * wp_options key for the admin-editable per-cadence discount
	 * percentages. Dummy defaults until the client gives real numbers.
	 */
	const OPTION_DISCOUNTS = 'nia_subscription_discounts';

	/**
	 * Cadence definitions — the fixed term (order count) each cadence
	 * commits to, per the client's spec: weekly = 1 month (4 orders),
	 * bi-weekly = 3 months (6 orders), monthly = 6 months (6 orders).
	 *
	 * @return array<string,array{label:string,interval_days:int,cycles:int,term_label:string}>
	 */
	public static function get_cadences() {
		$cadences = array(
			'weekly'   => array(
				'label'         => __( 'Weekly', 'nia-theme' ),
				'interval_days' => 7,
				'cycles'        => 4,
				'term_label'    => __( '1 month (4 deliveries)', 'nia-theme' ),
			),
			'biweekly' => array(
				'label'         => __( 'Bi-Weekly', 'nia-theme' ),
				'interval_days' => 14,
				'cycles'        => 6,
				'term_label'    => __( '3 months (6 deliveries)', 'nia-theme' ),
			),
			'monthly'  => array(
				'label'         => __( 'Monthly', 'nia-theme' ),
				'interval_days' => 30,
				'cycles'        => 6,
				'term_label'    => __( '6 months (6 deliveries)', 'nia-theme' ),
			),
		);

		// The admin-set discount is folded in here (rather than kept as a
		// separate lookup callers have to remember to also make) so both
		// the Subscription page template and localize_subscribe_script()'s
		// JS payload read it from the exact same place.
		foreach ( $cadences as $key => &$cadence ) {
			$cadence['discount'] = self::get_discount_percent( $key );
		}
		return $cadences;
	}

	/**
	 * Wire hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Admin list table + edit-screen schedule meta box.
		add_filter( 'manage_nia_subscription_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_nia_subscription_posts_custom_column', array( $this, 'render_admin_column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_nia_subscription', array( $this, 'save_schedule_meta_box' ) );

		// Discount settings screen.
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Cart: tag + reprice subscription bundle line items.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_subscription_pricing' ) );
		add_filter( 'woocommerce_get_item_data', array( $this, 'display_cart_item_meta' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_line_item_meta' ), 10, 4 );

		// Order paid -> create the subscription record(s) + schedule.
		add_action( 'woocommerce_thankyou', array( $this, 'maybe_create_subscriptions_from_order' ) );

		// Subscription page's product-picker "Add to Bag".
		add_action( 'wp_ajax_nia_subscribe_add_to_cart', array( $this, 'ajax_add_subscription_to_cart' ) );
		add_action( 'wp_ajax_nopriv_nia_subscribe_add_to_cart', array( $this, 'ajax_add_subscription_to_cart' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_subscribe_script' ), 20 );
	}

	/**
	 * The admin-set discount percentage for a cadence — dummy defaults
	 * until the client gives real numbers, editable at
	 * Settings -> Nia Subscriptions without touching code.
	 *
	 * @param string $cadence One of self::get_cadences()'s keys.
	 * @return float
	 */
	public static function get_discount_percent( $cadence ) {
		$defaults = array(
			'weekly'   => 10,
			'biweekly' => 15,
			'monthly'  => 20,
		);
		$saved = get_option( self::OPTION_DISCOUNTS, array() );
		if ( isset( $saved[ $cadence ] ) && is_numeric( $saved[ $cadence ] ) ) {
			return (float) $saved[ $cadence ];
		}
		return isset( $defaults[ $cadence ] ) ? (float) $defaults[ $cadence ] : 0.0;
	}

	/**
	 * Products a customer can subscribe to — any published, purchasable,
	 * simple product (ARCHITECTURE.md §9: "references a product ID
	 * generically... not a fixed list"), for the picker on the
	 * Subscription page.
	 *
	 * @return WC_Product[]
	 */
	public static function get_subscribable_products() {
		$products = wc_get_products(
			array(
				'status'  => 'publish',
				'type'    => 'simple',
				'limit'   => -1,
				'orderby' => 'title',
				'order'   => 'ASC',
			)
		);
		return array_values( array_filter( $products, static fn( $product ) => $product->is_purchasable() ) );
	}

	/**
	 * Customer's own subscriptions, for My Account -> My Rituals.
	 *
	 * @param int $customer_id WP user ID.
	 * @return WP_Post[]
	 */
	public static function get_customer_subscriptions( $customer_id ) {
		return get_posts(
			array(
				'post_type'      => 'nia_subscription',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small per-customer result set.
					array(
						'key'   => '_nia_customer_id',
						'value' => (int) $customer_id,
					),
				),
			)
		);
	}

	/**
	 * Next pending delivery date across a subscription's schedule, or
	 * empty if fully fulfilled.
	 *
	 * @param array $schedule Array of {cycle,date,status}.
	 * @return string
	 */
	public static function get_next_delivery_date( array $schedule ) {
		foreach ( $schedule as $cycle ) {
			if ( 'pending' === $cycle['status'] ) {
				return $cycle['date'];
			}
		}
		return '';
	}

	/**
	 * Register the `nia_subscription` CPT — admin-only (never public), so
	 * WordPress's own native list-table/edit-screen UI is the entire admin
	 * interface; no bespoke admin page needed for "owner can see active
	 * subscriptions" (PROJECT_PLAN.md Phase 9 checklist).
	 */
	public function register_post_type() {
		register_post_type(
			'nia_subscription',
			array(
				'labels'          => array(
					'name'          => __( 'Subscriptions', 'nia-theme' ),
					'singular_name' => __( 'Subscription', 'nia-theme' ),
					'all_items'     => __( 'Subscriptions', 'nia-theme' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-update',
				'menu_position'   => 56,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);
	}

	/**
	 * Admin list-table columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function admin_columns( $columns ) {
		unset( $columns['date'] );
		$columns['customer']       = __( 'Customer', 'nia-theme' );
		$columns['cadence']        = __( 'Cadence', 'nia-theme' );
		$columns['products']       = __( 'Products', 'nia-theme' );
		$columns['next_delivery']  = __( 'Next Delivery', 'nia-theme' );
		$columns['nia_status']     = __( 'Status', 'nia-theme' );
		$columns['date']           = __( 'Created', 'nia-theme' );
		return $columns;
	}

	/**
	 * Render each custom admin column's content.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Subscription post ID.
	 */
	public function render_admin_column( $column, $post_id ) {
		switch ( $column ) {
			case 'customer':
				$customer_id = (int) get_post_meta( $post_id, '_nia_customer_id', true );
				$user        = $customer_id ? get_userdata( $customer_id ) : false;
				echo esc_html( $user ? $user->display_name . ' (' . $user->user_email . ')' : __( 'Guest', 'nia-theme' ) );
				break;
			case 'cadence':
				$cadences = self::get_cadences();
				$cadence  = get_post_meta( $post_id, '_nia_cadence', true );
				echo esc_html( $cadences[ $cadence ]['label'] ?? $cadence );
				break;
			case 'products':
				$products = (array) get_post_meta( $post_id, '_nia_products', true );
				$names    = array();
				foreach ( $products as $line ) {
					$product = wc_get_product( $line['product_id'] );
					if ( $product ) {
						$names[] = $product->get_name() . ' × ' . (int) $line['quantity'];
					}
				}
				echo esc_html( implode( ', ', $names ) );
				break;
			case 'next_delivery':
				$schedule = (array) get_post_meta( $post_id, '_nia_schedule', true );
				$next     = self::get_next_delivery_date( $schedule );
				echo esc_html( $next ? date_i18n( get_option( 'date_format' ), strtotime( $next ) ) : __( 'Complete', 'nia-theme' ) );
				break;
			case 'nia_status':
				echo esc_html( ucfirst( get_post_meta( $post_id, '_nia_status', true ) ?: 'active' ) ); // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found -- status is always set on creation; short ternary reads fine here.
				break;
		}
	}

	/**
	 * Schedule meta box on the subscription edit screen — the admin's view
	 * into per-cycle delivery dates/fulfillment status.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'nia_subscription_details',
			__( 'Subscription Details', 'nia-theme' ),
			array( $this, 'render_details_meta_box' ),
			'nia_subscription',
			'normal',
			'high'
		);
	}

	/**
	 * Render the details/schedule meta box.
	 *
	 * @param WP_Post $post Subscription post.
	 */
	public function render_details_meta_box( $post ) {
		$cadences    = self::get_cadences();
		$cadence     = get_post_meta( $post->ID, '_nia_cadence', true );
		$discount    = (float) get_post_meta( $post->ID, '_nia_discount_percent', true );
		$total_paid  = (float) get_post_meta( $post->ID, '_nia_total_paid', true );
		$order_id    = (int) get_post_meta( $post->ID, '_nia_source_order_id', true );
		$products    = (array) get_post_meta( $post->ID, '_nia_products', true );
		$schedule    = (array) get_post_meta( $post->ID, '_nia_schedule', true );
		$status      = get_post_meta( $post->ID, '_nia_status', true );
		$status      = $status ? $status : 'active';

		wp_nonce_field( 'nia_save_subscription', 'nia_subscription_nonce' );
		?>
		<p>
			<strong><?php esc_html_e( 'Cadence:', 'nia-theme' ); ?></strong>
			<?php echo esc_html( $cadences[ $cadence ]['label'] ?? $cadence ); ?>
			&nbsp;·&nbsp;
			<strong><?php esc_html_e( 'Discount applied:', 'nia-theme' ); ?></strong>
			<?php echo esc_html( $discount ); ?>%
			&nbsp;·&nbsp;
			<strong><?php esc_html_e( 'Total paid:', 'nia-theme' ); ?></strong>
			<?php echo wp_kses_post( wc_price( $total_paid ) ); ?>
			<?php if ( $order_id ) : ?>
				&nbsp;·&nbsp;
				<strong><?php esc_html_e( 'Order:', 'nia-theme' ); ?></strong>
				<a href="<?php echo esc_url( get_edit_post_link( $order_id ) ); ?>">#<?php echo (int) $order_id; ?></a>
			<?php endif; ?>
		</p>

		<p>
			<label for="nia_status"><strong><?php esc_html_e( 'Status:', 'nia-theme' ); ?></strong></label>
			<select name="nia_status" id="nia_status">
				<?php foreach ( array( 'active', 'paused', 'completed', 'cancelled' ) as $status_option ) : ?>
					<option value="<?php echo esc_attr( $status_option ); ?>" <?php selected( $status, $status_option ); ?>><?php echo esc_html( ucfirst( $status_option ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="description"><?php esc_html_e( 'Pause/cancel handling (grace windows, owner workflow) is future work — this only records the state today.', 'nia-theme' ); ?></span>
		</p>

		<h4><?php esc_html_e( 'Products', 'nia-theme' ); ?></h4>
		<ul>
			<?php foreach ( $products as $line ) : ?>
				<?php $product = wc_get_product( $line['product_id'] ); ?>
				<li>
					<?php
					if ( $product ) {
						echo esc_html( $product->get_name() . ' × ' . (int) $line['quantity'] . ' — ' );
						echo wp_kses_post( wc_price( $line['unit_price'] ) );
						echo esc_html__( ' / delivery', 'nia-theme' );
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<h4><?php esc_html_e( 'Delivery Schedule', 'nia-theme' ); ?></h4>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Cycle', 'nia-theme' ); ?></th>
					<th><?php esc_html_e( 'Date', 'nia-theme' ); ?></th>
					<th><?php esc_html_e( 'Status', 'nia-theme' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $schedule as $index => $cycle ) : ?>
					<tr>
						<td><?php echo (int) $cycle['cycle']; ?></td>
						<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $cycle['date'] ) ) ); ?></td>
						<td>
							<select name="nia_schedule[<?php echo (int) $index; ?>]">
								<?php foreach ( array( 'pending', 'fulfilled', 'skipped' ) as $cycle_status ) : ?>
									<option value="<?php echo esc_attr( $cycle_status ); ?>" <?php selected( $cycle['status'], $cycle_status ); ?>><?php echo esc_html( ucfirst( $cycle_status ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Persist the schedule/status edits from the meta box.
	 *
	 * @param int $post_id Subscription post ID.
	 */
	public function save_schedule_meta_box( $post_id ) {
		if ( ! isset( $_POST['nia_subscription_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nia_subscription_nonce'] ), 'nia_save_subscription' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by wp_verify_nonce itself.
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['nia_status'] ) ) {
			$allowed = array( 'active', 'paused', 'completed', 'cancelled' );
			$status  = sanitize_key( wp_unslash( $_POST['nia_status'] ) );
			if ( in_array( $status, $allowed, true ) ) {
				update_post_meta( $post_id, '_nia_status', $status );
			}
		}

		if ( isset( $_POST['nia_schedule'] ) && is_array( $_POST['nia_schedule'] ) ) {
			$schedule = (array) get_post_meta( $post_id, '_nia_schedule', true );
			$allowed  = array( 'pending', 'fulfilled', 'skipped' );
			foreach ( wp_unslash( $_POST['nia_schedule'] ) as $index => $cycle_status ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- unslashed above.
				$cycle_status = sanitize_key( $cycle_status );
				if ( isset( $schedule[ $index ] ) && in_array( $cycle_status, $allowed, true ) ) {
					$schedule[ $index ]['status'] = $cycle_status;
				}
			}
			update_post_meta( $post_id, '_nia_schedule', $schedule );
		}
	}

	/**
	 * Discount settings screen — Settings -> Nia Subscriptions. Three
	 * numbers, admin-editable, no code changes needed to update them.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Nia Subscriptions', 'nia-theme' ),
			__( 'Nia Subscriptions', 'nia-theme' ),
			'manage_options',
			'nia-subscriptions',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register the discounts option for the Settings API.
	 */
	public function register_settings() {
		register_setting( 'nia_subscriptions', self::OPTION_DISCOUNTS, array( 'sanitize_callback' => array( $this, 'sanitize_discounts' ) ) );
	}

	/**
	 * Sanitize the discounts option — clamp each cadence's percent to 0-90.
	 *
	 * @param array $value Raw posted value.
	 * @return array
	 */
	public function sanitize_discounts( $value ) {
		$clean = array();
		foreach ( array_keys( self::get_cadences() ) as $cadence ) {
			$raw             = isset( $value[ $cadence ] ) ? (float) $value[ $cadence ] : 0;
			$clean[ $cadence ] = max( 0, min( 90, $raw ) );
		}
		return $clean;
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Nia Subscriptions — Discount Settings', 'nia-theme' ); ?></h1>
			<p><?php esc_html_e( 'One discount percentage per cadence, applied to every product a customer bundles under that plan. Dummy defaults are in place until real numbers are confirmed.', 'nia-theme' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'nia_subscriptions' ); ?>
				<table class="form-table">
					<?php foreach ( self::get_cadences() as $key => $cadence ) : ?>
						<tr>
							<th scope="row"><label for="nia_discount_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $cadence['label'] ); ?> (<?php echo esc_html( $cadence['term_label'] ); ?>)</label></th>
							<td>
								<input
									type="number"
									id="nia_discount_<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( self::OPTION_DISCOUNTS ); ?>[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( self::get_discount_percent( $key ) ); ?>"
									min="0"
									max="90"
									step="1"
								/> %
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Tag a cart item as part of a subscription bundle when added via the
	 * Subscription page's picker (not a normal Add to Bag).
	 *
	 * @param array $cart_item_data Existing cart item data.
	 * @param int   $product_id     Product being added.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {
		if ( empty( $_POST['nia_subscription'] ) || ! is_array( $_POST['nia_subscription'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this filter only ever runs from within ajax_add_subscription_to_cart(), which already verified the nonce before calling WC()->cart->add_to_cart().
			return $cart_item_data;
		}
		$posted = wp_unslash( $_POST['nia_subscription'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized field-by-field below.

		$cadences = self::get_cadences();
		$cadence  = isset( $posted['cadence'] ) ? sanitize_key( $posted['cadence'] ) : '';
		if ( ! isset( $cadences[ $cadence ] ) ) {
			return $cart_item_data;
		}

		$start_date = isset( $posted['start_date'] ) ? sanitize_text_field( $posted['start_date'] ) : '';
		if ( ! $start_date || ! strtotime( $start_date ) ) {
			return $cart_item_data;
		}

		$product = wc_get_product( $product_id );

		$cart_item_data[ self::CART_ITEM_KEY ] = array(
			'cadence'    => $cadence,
			'start_date' => gmdate( 'Y-m-d', strtotime( $start_date ) ),
			'discount'   => self::get_discount_percent( $cadence ),
			'unit_price' => $product ? (float) $product->get_price() : 0,
		);
		// Unique key so identical products under different cadences/start
		// dates don't merge into one cart line.
		$cart_item_data['unique_key'] = md5( wp_json_encode( $cart_item_data[ self::CART_ITEM_KEY ] ) . $product_id );

		return $cart_item_data;
	}

	/**
	 * Reprice tagged cart items to the discounted subscription price —
	 * standard "dynamic pricing" pattern (WooCommerce always recalculates
	 * from the product object otherwise, so the discount has to be applied
	 * here, not at add-to-cart time).
	 *
	 * @param WC_Cart $cart Cart instance.
	 */
	public function apply_subscription_pricing( $cart ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}
		foreach ( $cart->get_cart() as $cart_item ) {
			if ( empty( $cart_item[ self::CART_ITEM_KEY ] ) ) {
				continue;
			}
			$meta          = $cart_item[ self::CART_ITEM_KEY ];
			$discounted    = (float) $meta['unit_price'] * ( 1 - ( (float) $meta['discount'] / 100 ) );
			$cart_item['data']->set_price( $discounted );
		}
	}

	/**
	 * Show the cadence/schedule/discount as extra line(s) under a
	 * subscription cart item, on the Cart/Checkout/mini-cart.
	 *
	 * @param array $item_data  Existing display rows.
	 * @param array $cart_item  Cart item.
	 * @return array
	 */
	public function display_cart_item_meta( $item_data, $cart_item ) {
		if ( empty( $cart_item[ self::CART_ITEM_KEY ] ) ) {
			return $item_data;
		}
		$meta     = $cart_item[ self::CART_ITEM_KEY ];
		$cadences = self::get_cadences();
		$cadence  = $cadences[ $meta['cadence'] ] ?? null;
		if ( ! $cadence ) {
			return $item_data;
		}

		$item_data[] = array(
			'key'   => __( 'Ritual', 'nia-theme' ),
			'value' => sprintf(
				/* translators: 1: cadence label, 2: term label, 3: first delivery date, 4: discount percent */
				esc_html__( '%1$s — %2$s, starting %3$s (%4$s%% subscription discount)', 'nia-theme' ),
				esc_html( $cadence['label'] ),
				esc_html( $cadence['term_label'] ),
				esc_html( date_i18n( get_option( 'date_format' ), strtotime( $meta['start_date'] ) ) ),
				esc_html( $meta['discount'] )
			),
		);
		return $item_data;
	}

	/**
	 * Copy subscription cart-item meta onto the order line item so it
	 * survives past checkout (woocommerce_thankyou reads it back).
	 *
	 * @param WC_Order_Item_Product $item          Order line item.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values        Cart item values.
	 * @param WC_Order              $order         Order.
	 */
	public function add_order_line_item_meta( $item, $cart_item_key, $values, $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by the woocommerce_checkout_create_order_line_item signature.
		if ( empty( $values[ self::CART_ITEM_KEY ] ) ) {
			return;
		}
		$item->add_meta_data( '_' . self::CART_ITEM_KEY, $values[ self::CART_ITEM_KEY ], true );
	}

	/**
	 * Once an order is placed (COD today; any gateway later), group its
	 * subscription-tagged line items by cadence+start_date and create one
	 * `nia_subscription` record per group, with a full delivery schedule
	 * computed from cadence + start date. Guarded against re-running (the
	 * thank-you page can be viewed more than once).
	 *
	 * @param int $order_id Order ID.
	 */
	public function maybe_create_subscriptions_from_order( $order_id ) {
		if ( ! $order_id ) {
			return;
		}
		$order = wc_get_order( $order_id );
		if ( ! $order || $order->get_meta( '_nia_subscriptions_created' ) ) {
			return;
		}

		$groups = array();
		foreach ( $order->get_items() as $item ) {
			$meta = $item->get_meta( '_' . self::CART_ITEM_KEY );
			if ( ! $meta || empty( $meta['cadence'] ) ) {
				continue;
			}
			$group_key = $meta['cadence'] . '|' . $meta['start_date'];
			if ( ! isset( $groups[ $group_key ] ) ) {
				$groups[ $group_key ] = array(
					'cadence'    => $meta['cadence'],
					'start_date' => $meta['start_date'],
					'discount'   => $meta['discount'],
					'products'   => array(),
					'total'      => 0.0,
				);
			}
			$cadences   = self::get_cadences();
			$cycles     = $cadences[ $meta['cadence'] ]['cycles'] ?? 1;
			$line_total = (float) $item->get_total();

			$groups[ $group_key ]['products'][] = array(
				'product_id' => $item->get_product_id(),
				// Cart quantity is per-cycle-times-cycles; store the
				// per-delivery quantity for the admin's product list.
				'quantity'   => $cycles ? (int) round( $item->get_quantity() / $cycles ) : (int) $item->get_quantity(),
				'unit_price' => (float) $meta['unit_price'] * ( 1 - ( (float) $meta['discount'] / 100 ) ),
			);
			$groups[ $group_key ]['total'] += $line_total;
		}

		if ( ! $groups ) {
			return;
		}

		$cadences    = self::get_cadences();
		$customer_id = $order->get_customer_id();

		foreach ( $groups as $group ) {
			$cadence_def = $cadences[ $group['cadence'] ] ?? null;
			if ( ! $cadence_def ) {
				continue;
			}

			$schedule = array();
			$date     = strtotime( $group['start_date'] );
			for ( $cycle = 1; $cycle <= $cadence_def['cycles']; $cycle++ ) {
				$schedule[] = array(
					'cycle'  => $cycle,
					'date'   => gmdate( 'Y-m-d', $date ),
					'status' => 'pending',
				);
				$date = strtotime( '+' . $cadence_def['interval_days'] . ' days', $date );
			}

			$post_id = wp_insert_post(
				array(
					'post_type'   => 'nia_subscription',
					'post_status' => 'publish',
					/* translators: 1: order number, 2: cadence label */
					'post_title'  => sprintf( __( 'Subscription — Order #%1$s — %2$s', 'nia-theme' ), $order->get_order_number(), $cadence_def['label'] ),
				)
			);
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_nia_customer_id', $customer_id );
			update_post_meta( $post_id, '_nia_cadence', $group['cadence'] );
			update_post_meta( $post_id, '_nia_discount_percent', $group['discount'] );
			update_post_meta( $post_id, '_nia_products', $group['products'] );
			update_post_meta( $post_id, '_nia_total_paid', $group['total'] );
			update_post_meta( $post_id, '_nia_source_order_id', $order_id );
			update_post_meta( $post_id, '_nia_start_date', $group['start_date'] );
			update_post_meta( $post_id, '_nia_schedule', $schedule );
			update_post_meta( $post_id, '_nia_status', 'active' );
		}

		$order->update_meta_data( '_nia_subscriptions_created', 1 );
		$order->save();
	}

	/**
	 * Localize the Subscription page's AJAX nonce/URL + the subscribable
	 * product list (id/name/price) the picker's Alpine component needs.
	 */
	public function localize_subscribe_script() {
		if ( ! is_page_template( 'page-subscription.php' ) ) {
			return;
		}

		$products = array();
		foreach ( self::get_subscribable_products() as $product ) {
			$products[] = array(
				'id'        => $product->get_id(),
				'name'      => $product->get_name(),
				'price'     => (float) $product->get_price(),
				'priceHtml' => wp_strip_all_tags( $product->get_price_html() ),
			);
		}

		wp_localize_script(
			'nia-main',
			'niaSubscriptions',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nia_subscribe_add_to_cart' ),
				'products' => $products,
				'cadences' => self::get_cadences(),
				'loggedIn' => is_user_logged_in(),
				'loginUrl' => wp_login_url( get_permalink() ),
				'cartUrl'  => wc_get_cart_url(),
			)
		);
	}

	/**
	 * AJAX: add the picked product(s) to the cart as one subscription
	 * bundle (same cadence + start date for all of them), then return the
	 * cart URL so the page can redirect there.
	 */
	public function ajax_add_subscription_to_cart() {
		check_ajax_referer( 'nia_subscribe_add_to_cart', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Please log in to start a subscription.', 'nia-theme' ) ), 401 );
		}

		$cadences = self::get_cadences();
		$cadence  = isset( $_POST['cadence'] ) ? sanitize_key( wp_unslash( $_POST['cadence'] ) ) : '';
		if ( ! isset( $cadences[ $cadence ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid subscription plan.', 'nia-theme' ) ), 400 );
		}

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) : '';
		if ( ! $start_date || ! strtotime( $start_date ) || strtotime( $start_date ) < strtotime( 'today' ) ) {
			wp_send_json_error( array( 'message' => __( 'Please choose a valid first delivery date.', 'nia-theme' ) ), 400 );
		}

		$items = isset( $_POST['items'] ) ? json_decode( wp_unslash( $_POST['items'] ), true ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- raw JSON is never used as HTML/SQL; every field is re-validated (absint) right below.
		if ( ! is_array( $items ) || ! $items ) {
			wp_send_json_error( array( 'message' => __( 'Please choose at least one product.', 'nia-theme' ) ), 400 );
		}

		$cycles = $cadences[ $cadence ]['cycles'];
		$added  = 0;

		foreach ( $items as $line ) {
			$product_id = isset( $line['product_id'] ) ? absint( $line['product_id'] ) : 0;
			$quantity   = isset( $line['quantity'] ) ? absint( $line['quantity'] ) : 0;
			$product    = $product_id ? wc_get_product( $product_id ) : null;

			if ( ! $product || ! $product->is_purchasable() || $quantity < 1 ) {
				continue;
			}

			// $_POST['nia_subscription'] is what add_cart_item_data() reads;
			// set it per-item since add_to_cart() doesn't take arbitrary
			// extra args through to that filter directly.
			$_POST['nia_subscription'] = array(
				'cadence'    => $cadence,
				'start_date' => $start_date,
			);

			$added_key = WC()->cart->add_to_cart( $product_id, $quantity * $cycles );
			if ( $added_key ) {
				++$added;
			}
		}
		unset( $_POST['nia_subscription'] );

		if ( ! $added ) {
			wp_send_json_error( array( 'message' => __( 'Could not add those products to your bag — please try again.', 'nia-theme' ) ), 400 );
		}

		wp_send_json_success( array( 'redirect' => wc_get_cart_url() ) );
	}
}
