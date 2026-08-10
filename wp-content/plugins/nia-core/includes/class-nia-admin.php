<?php
/**
 * Admin dashboard polish — a modern, data-real "Overview" widget replacing
 * WordPress's/WooCommerce's default dashboard widgets, a sitewide branded
 * chrome pass (admin menu, toolbar, primary buttons, notices), and the
 * shared asset-loading scaffolding later admin-screen redesigns (Products,
 * Subscriptions list polish) will extend. Everything here is additive
 * (hooks + enqueued stylesheets) — never a core admin template override —
 * so third-party admin screens (Wordfence, Rank Math, Elementor) render
 * their own content exactly as those plugins built it; only the shared
 * frame around it (menu/toolbar/buttons/notices) is re-themed.
 *
 * Two stylesheets, two different footprints:
 * - admin-chrome.css: sitewide (every wp-admin screen) — deliberately
 *   narrow, touches only the persistent frame.
 * - admin.css: screen-scoped (Nia_Admin::$screens only) — the larger
 *   surface area of component-level redesigns (dashboard widget, Orders
 *   list pills).
 * Both depend on tokens.css for the shared `--nia-*` palette.
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Nia_Admin.
 */
class Nia_Admin {

	/**
	 * Transient key (wp_options) for the cached overview-widget data.
	 */
	const TRANSIENT_OVERVIEW = 'nia_admin_overview_data';

	/**
	 * Screen IDs the branded admin stylesheet loads on — extended one
	 * redesigned screen at a time rather than loading the stylesheet
	 * admin-wide. `woocommerce_page_wc-orders` is the HPOS Orders screen
	 * (this install's active order-storage mode); `edit-shop_order` is the
	 * legacy post-table equivalent, included for robustness if HPOS is ever
	 * toggled off.
	 *
	 * @var string[]
	 */
	private $screens = array( 'dashboard', 'woocommerce_page_wc-orders', 'edit-shop_order' );

	/**
	 * Wire hooks.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

		// Keep the 5-minute cache (see get_overview_data()) from showing
		// stale numbers right after something it reports on changes.
		add_action( 'woocommerce_order_status_changed', array( $this, 'flush_overview_cache' ) );
		add_action( 'save_post_nia_subscription', array( $this, 'flush_overview_cache' ) );
		add_action( 'transition_comment_status', array( $this, 'flush_overview_cache' ) );
	}

	/**
	 * Enqueue the shared tokens + sitewide chrome on every wp-admin screen,
	 * and the larger component stylesheet (+ the theme's self-hosted brand
	 * fonts) only on screens this plugin has actually redesigned.
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'nia-admin-tokens', NIA_CORE_URI . 'assets/css/tokens.css', array(), NIA_CORE_VERSION );
		wp_enqueue_style( 'nia-admin-chrome', NIA_CORE_URI . 'assets/css/admin-chrome.css', array( 'nia-admin-tokens' ), NIA_CORE_VERSION );

		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, $this->screens, true ) ) {
			return;
		}

		// The theme's self-hosted Montserrat/Playfair Display/Material
		// Symbols (assets/css/fonts.css, DESIGN_SYSTEM.md §2/§6) — reused
		// as-is rather than duplicating @font-face declarations here.
		// Reserved for our own component surfaces (widget numbers, order
		// pills), not applied to WordPress's own UI text.
		wp_enqueue_style( 'nia-theme-fonts', get_theme_file_uri( 'assets/css/fonts.css' ), array(), NIA_CORE_VERSION );

		// Declared as WP-dependent on WooCommerce's own admin stylesheet
		// (handle 'woocommerce_admin_styles') so ours always prints after
		// it and wins the cascade on equal-specificity selectors like
		// `.order-status.status-processing` — without this, load order
		// between two plugins' styles isn't guaranteed, and WC's own
		// default status colors silently won at least once during dev.
		wp_enqueue_style( 'nia-admin', NIA_CORE_URI . 'assets/css/admin.css', array( 'nia-admin-tokens', 'nia-theme-fonts', 'woocommerce_admin_styles' ), NIA_CORE_VERSION );
	}

	/**
	 * Replace the default dashboard widget clutter with one consolidated,
	 * real-data Overview widget. WooCommerce's own "WooCommerce Status" /
	 * "Recent Reviews" widgets are removed rather than left alongside ours —
	 * this widget supersedes both with a broader, better-organized view.
	 */
	public function register_dashboard_widget() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- a real WooCommerce-registered capability, not custom.
			return;
		}

		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress Events & News.
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );

		// Priority 'high' (not the default 'core') so this sits above
		// WooCommerce's and Elementor's own "high"-priority setup widgets —
		// it's meant to be the first thing the client sees, not just
		// another item in the normal column. The title carries a raw
		// Material Symbols icon span — WP core does the same for its own
		// dashboard_php_nag widget (wp-admin/includes/template.php echoes
		// $box['title'] unescaped), so this follows an established pattern
		// rather than fighting one.
		wp_add_dashboard_widget(
			'nia_admin_overview',
			'<span class="material-symbols-outlined nia-admin-widget-icon" aria-hidden="true">auto_awesome</span>' . __( 'Nia Nutrition — Overview', 'nia-theme' ),
			array( $this, 'render_overview_widget' ),
			null,
			null,
			'normal',
			'high'
		);
	}

	/**
	 * Invalidate the overview cache. Hooked to the handful of actions that
	 * change what it reports (order status, subscription save, review
	 * moderation) — a plain 5-minute TTL alone would leave the dashboard
	 * showing stale "needs attention" numbers right after the client
	 * actually attends to one.
	 */
	public function flush_overview_cache() {
		delete_transient( self::TRANSIENT_OVERVIEW );
	}

	/**
	 * Gather (or reuse cached) data for the Overview widget.
	 *
	 * @return array{
	 *     revenue: array{total: float, count: int, trend: float|null},
	 *     attention_count: int,
	 *     subscriptions: array{active_count: int, due_soon: array},
	 *     stock: array{count: int, items: array},
	 *     pending_reviews: int
	 * }
	 */
	private function get_overview_data() {
		$cached = get_transient( self::TRANSIENT_OVERVIEW );
		if ( false !== $cached ) {
			return $cached;
		}

		$data = array(
			'revenue'         => $this->get_recent_revenue(),
			'attention_count' => wc_orders_count( 'processing' ) + wc_orders_count( 'on-hold' ),
			'subscriptions'   => $this->get_subscription_stats(),
			'stock'           => $this->get_stock_stats(),
			'pending_reviews' => (int) get_comments(
				array(
					'type'   => 'review',
					'status' => 'hold',
					'count'  => true,
				)
			),
		);

		set_transient( self::TRANSIENT_OVERVIEW, $data, 5 * MINUTE_IN_SECONDS );
		return $data;
	}

	/**
	 * Total + count of paid orders in the last 7 days, plus a trend
	 * percentage against the 7 days before that — a bare total doesn't say
	 * whether the client should feel good or concerned about it.
	 *
	 * @return array{total: float, count: int, trend: float|null}
	 */
	private function get_recent_revenue() {
		$now      = time();
		$current  = $this->sum_paid_orders( $now - 7 * DAY_IN_SECONDS, $now );
		$previous = $this->sum_paid_orders( $now - 14 * DAY_IN_SECONDS, $now - 7 * DAY_IN_SECONDS );

		$trend = null; // No prior-period baseline to compare against.
		if ( $previous['total'] > 0.0 ) {
			$trend = ( ( $current['total'] - $previous['total'] ) / $previous['total'] ) * 100;
		} elseif ( $current['total'] > 0.0 ) {
			$trend = 100.0; // From nothing to something — a full increase, not a divide-by-zero.
		}

		return array(
			'total' => $current['total'],
			'count' => $current['count'],
			'trend' => $trend,
		);
	}

	/**
	 * Total + count of paid orders created within a timestamp window.
	 *
	 * @param int $from Window start (unix timestamp, inclusive).
	 * @param int $to   Window end (unix timestamp, exclusive).
	 * @return array{total: float, count: int}
	 */
	private function sum_paid_orders( $from, $to ) {
		$orders = wc_get_orders(
			array(
				'status'       => wc_get_is_paid_statuses(),
				'date_created' => $from . '...' . $to,
				'limit'        => -1,
				'return'       => 'objects',
			)
		);

		$total = 0.0;
		foreach ( $orders as $order ) {
			$total += (float) $order->get_total();
		}

		return array(
			'total' => $total,
			'count' => count( $orders ),
		);
	}

	/**
	 * Active-subscription count + each active subscription's next pending
	 * delivery, filtered to the next 7 days.
	 *
	 * @return array{active_count: int, due_soon: array<int, array{post_id:int, date:string}>}
	 */
	private function get_subscription_stats() {
		$active_ids = get_posts(
			array(
				'post_type'      => 'nia_subscription',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$window_end = strtotime( '+7 days', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- comparing against date-only schedule entries; timezone precision doesn't matter at day granularity.
		$due_soon   = array();

		foreach ( $active_ids as $post_id ) {
			$schedule = get_post_meta( $post_id, '_nia_schedule', true );
			if ( ! is_array( $schedule ) ) {
				continue;
			}
			foreach ( $schedule as $cycle ) {
				if ( 'pending' !== ( $cycle['status'] ?? '' ) ) {
					continue;
				}
				$date_ts = strtotime( $cycle['date'] ?? '' );
				if ( $date_ts && $date_ts <= $window_end ) {
					$due_soon[] = array(
						'post_id' => $post_id,
						'date'    => $cycle['date'],
					);
				}
				break; // Only each subscription's *next* pending cycle counts.
			}
		}

		usort( $due_soon, static fn( $a, $b ) => strcmp( $a['date'], $b['date'] ) );

		return array(
			'active_count' => count( $active_ids ),
			'due_soon'     => $due_soon,
		);
	}

	/**
	 * Low-stock product count + up to 5 lowest-stock rows, using the same
	 * `wc_product_meta_lookup` query WooCommerce's own core "Low in stock"
	 * status widget uses (class-wc-admin-dashboard.php), so the threshold
	 * behavior (Settings -> Products -> Inventory) matches exactly.
	 *
	 * @return array{count: int, items: array}
	 */
	private function get_stock_stats() {
		global $wpdb;

		$low_threshold = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
		$no_threshold  = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

		$items = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- mirrors WC core's own status-widget query; result is cached in this class's own transient.
			$wpdb->prepare(
				"SELECT lookup.product_id, lookup.stock_quantity, posts.post_title
				FROM {$wpdb->wc_product_meta_lookup} AS lookup
				INNER JOIN {$wpdb->posts} AS posts ON lookup.product_id = posts.ID
				WHERE lookup.stock_quantity <= %d
				AND lookup.stock_quantity > %d
				AND posts.post_status = 'publish'
				ORDER BY lookup.stock_quantity ASC
				LIMIT 5",
				$low_threshold,
				$no_threshold
			)
		);

		$count = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT COUNT(product_id) FROM {$wpdb->wc_product_meta_lookup} AS lookup
				INNER JOIN {$wpdb->posts} AS posts ON lookup.product_id = posts.ID
				WHERE lookup.stock_quantity <= %d AND posts.post_status = 'publish'",
				$low_threshold
			)
		);

		return array(
			'count' => $count,
			'items' => $items,
		);
	}

	/**
	 * Orders screen URL, HPOS- or legacy-post-table-aware.
	 *
	 * @param string $status Optional order status (e.g. 'wc-processing') to filter to.
	 * @return string
	 */
	private function get_orders_url( $status = '' ) {
		$hpos = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' )
			&& \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();

		if ( $hpos ) {
			$url = admin_url( 'admin.php?page=wc-orders' );
			return $status ? add_query_arg( 'status', $status, $url ) : $url;
		}

		$url = admin_url( 'edit.php?post_type=shop_order' );
		return $status ? add_query_arg( 'post_status', $status, $url ) : $url;
	}

	/**
	 * A trending-up/down pill next to the Revenue hero figure. Silent
	 * (renders nothing) when there's no prior 7-day window to compare
	 * against yet, rather than showing a misleading 0%/100%.
	 *
	 * @param float|null $trend Percentage change vs. the prior 7-day window.
	 */
	private function render_trend_badge( $trend ) {
		if ( null === $trend ) {
			return;
		}
		$up = $trend >= 0;
		printf(
			'<span class="nia-admin-trend %1$s"><span class="material-symbols-outlined" aria-hidden="true">%2$s</span>%3$s</span>',
			esc_attr( $up ? 'nia-admin-trend--up' : 'nia-admin-trend--down' ),
			esc_html( $up ? 'trending_up' : 'trending_down' ),
			esc_html( ( $up ? '+' : '' ) . round( $trend ) . '%' )
		);
	}

	/**
	 * Render the Overview widget.
	 */
	public function render_overview_widget() {
		$data = $this->get_overview_data();
		?>
		<div class="nia-admin-overview">
			<a class="nia-admin-hero-card" href="<?php echo esc_url( $this->get_orders_url() ); ?>">
				<span class="material-symbols-outlined nia-admin-hero-card__icon" aria-hidden="true">payments</span>
				<div class="nia-admin-hero-card__body">
					<p class="nia-admin-kpi-card__label"><?php esc_html_e( 'Revenue', 'nia-theme' ); ?></p>
					<p class="nia-admin-hero-card__value"><?php echo wp_kses_post( wc_price( $data['revenue']['total'] ) ); ?></p>
					<p class="nia-admin-kpi-card__meta">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: order count */
								_n( '%d order · last 7 days', '%d orders · last 7 days', $data['revenue']['count'], 'nia-theme' ),
								$data['revenue']['count']
							)
						);
						?>
					</p>
				</div>
				<?php $this->render_trend_badge( $data['revenue']['trend'] ?? null ); ?>
			</a>

			<div class="nia-admin-kpi-grid">
				<a class="nia-admin-kpi-card<?php echo $data['attention_count'] > 0 ? ' nia-admin-kpi-card--alert' : ''; ?>" href="<?php echo esc_url( $this->get_orders_url( 'wc-processing' ) ); ?>">
					<span class="material-symbols-outlined nia-admin-kpi-card__icon" aria-hidden="true">pending_actions</span>
					<p class="nia-admin-kpi-card__label"><?php esc_html_e( 'Needs Attention', 'nia-theme' ); ?></p>
					<p class="nia-admin-kpi-card__value"><?php echo esc_html( $data['attention_count'] ); ?></p>
					<p class="nia-admin-kpi-card__meta"><?php esc_html_e( 'Processing + on-hold orders', 'nia-theme' ); ?></p>
				</a>

				<a class="nia-admin-kpi-card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nia_subscription' ) ); ?>">
					<span class="material-symbols-outlined nia-admin-kpi-card__icon" aria-hidden="true">auto_awesome</span>
					<p class="nia-admin-kpi-card__label"><?php esc_html_e( 'Active Rituals', 'nia-theme' ); ?></p>
					<p class="nia-admin-kpi-card__value"><?php echo esc_html( $data['subscriptions']['active_count'] ); ?></p>
					<p class="nia-admin-kpi-card__meta">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: delivery count */
								_n( '%d delivery due within 7 days', '%d deliveries due within 7 days', count( $data['subscriptions']['due_soon'] ), 'nia-theme' ),
								count( $data['subscriptions']['due_soon'] )
							)
						);
						?>
					</p>
				</a>

				<a class="nia-admin-kpi-card<?php echo $data['stock']['count'] > 0 ? ' nia-admin-kpi-card--alert' : ''; ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">
					<span class="material-symbols-outlined nia-admin-kpi-card__icon" aria-hidden="true">inventory_2</span>
					<p class="nia-admin-kpi-card__label"><?php esc_html_e( 'Low Stock', 'nia-theme' ); ?></p>
					<p class="nia-admin-kpi-card__value"><?php echo esc_html( $data['stock']['count'] ); ?></p>
					<p class="nia-admin-kpi-card__meta"><?php esc_html_e( 'Products at or below threshold', 'nia-theme' ); ?></p>
				</a>

				<a class="nia-admin-kpi-card<?php echo $data['pending_reviews'] > 0 ? ' nia-admin-kpi-card--alert' : ''; ?>" href="<?php echo esc_url( admin_url( 'edit-comments.php?comment_type=review&comment_status=moderated' ) ); ?>">
					<span class="material-symbols-outlined nia-admin-kpi-card__icon" aria-hidden="true">rate_review</span>
					<p class="nia-admin-kpi-card__label"><?php esc_html_e( 'Pending Reviews', 'nia-theme' ); ?></p>
					<p class="nia-admin-kpi-card__value"><?php echo esc_html( $data['pending_reviews'] ); ?></p>
					<p class="nia-admin-kpi-card__meta"><?php esc_html_e( 'Awaiting moderation', 'nia-theme' ); ?></p>
				</a>
			</div>

			<div class="nia-admin-lists">
				<div class="nia-admin-list">
					<h3><?php esc_html_e( 'Upcoming Deliveries', 'nia-theme' ); ?></h3>
					<?php if ( $data['subscriptions']['due_soon'] ) : ?>
						<ul>
							<?php foreach ( array_slice( $data['subscriptions']['due_soon'], 0, 5 ) as $item ) : ?>
								<?php
								$customer_id = (int) get_post_meta( $item['post_id'], '_nia_customer_id', true );
								$user        = $customer_id ? get_userdata( $customer_id ) : false;
								?>
								<li>
									<a href="<?php echo esc_url( (string) get_edit_post_link( $item['post_id'] ) ); ?>"><?php echo esc_html( $user ? $user->display_name : __( 'Guest', 'nia-theme' ) ); ?></a>
									<span><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item['date'] ) ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p class="nia-admin-empty"><?php esc_html_e( 'Nothing due in the next 7 days.', 'nia-theme' ); ?></p>
					<?php endif; ?>
				</div>

				<div class="nia-admin-list">
					<h3><?php esc_html_e( 'Low Stock Products', 'nia-theme' ); ?></h3>
					<?php if ( $data['stock']['items'] ) : ?>
						<ul>
							<?php foreach ( $data['stock']['items'] as $row ) : ?>
								<li>
									<a href="<?php echo esc_url( (string) get_edit_post_link( $row->product_id ) ); ?>"><?php echo esc_html( $row->post_title ); ?></a>
									<span>
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: remaining stock quantity */
												_n( '%d left', '%d left', (int) $row->stock_quantity, 'nia-theme' ),
												(int) $row->stock_quantity
											)
										);
										?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p class="nia-admin-empty"><?php esc_html_e( 'All products well stocked.', 'nia-theme' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
