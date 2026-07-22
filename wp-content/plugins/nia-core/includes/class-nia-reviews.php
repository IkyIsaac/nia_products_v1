<?php
/**
 * Product reviews & ratings (PROJECT_PLAN.md Phase 6). Reviews themselves are
 * unmodified WordPress comments (comment_type=review + a `rating`
 * commentmeta) — WooCommerce core already handles submission, verified-owner
 * gating, moderation, and the cached average/count on the product; admins
 * see and moderate them via wp-admin -> Comments like any other comment.
 * "Helpful" votes are the one genuinely new piece of state here — WordPress
 * and WooCommerce have no built-in concept of it.
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Nia_Reviews.
 */
class Nia_Reviews {

	/**
	 * Cookie name used to remember a guest's helpful votes (logged-in
	 * customers use user meta instead — see user_has_liked()).
	 */
	const GUEST_VOTES_COOKIE = 'nia_helpful_votes';

	/**
	 * Wire hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_nia_toggle_helpful', array( $this, 'ajax_toggle_helpful' ) );
		add_action( 'wp_ajax_nopriv_nia_toggle_helpful', array( $this, 'ajax_toggle_helpful' ) );
		add_action( 'wp_ajax_nia_filter_reviews', array( $this, 'ajax_filter_reviews' ) );
		add_action( 'wp_ajax_nopriv_nia_filter_reviews', array( $this, 'ajax_filter_reviews' ) );
		// Priority 20: plugins' hooks register before the theme's
		// functions.php runs (wp-settings.php fires plugins_loaded before
		// including functions.php), so at the default priority this ran
		// before nia_theme_enqueue_assets() had even registered the
		// 'nia-main' handle wp_localize_script() needs to attach to.
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_reviews_script' ), 20 );
	}

	/**
	 * Main.js needs an ajaxUrl + nonce for the Helpful button — only on
	 * single product pages, since that's the only place it appears.
	 */
	public function localize_reviews_script() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		wp_localize_script(
			'nia-main',
			'niaReviews',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'nia_toggle_helpful' ),
				'filterNonce' => wp_create_nonce( 'nia_filter_reviews' ),
			)
		);
	}

	/**
	 * Toggle a Helpful vote on a review. One vote per person: logged-in
	 * customers are tracked via user meta, guests via a cookie — either way
	 * a second click un-votes rather than double-counting.
	 */
	public function ajax_toggle_helpful() {
		check_ajax_referer( 'nia_toggle_helpful', 'nonce' );

		$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
		$comment    = $comment_id ? get_comment( $comment_id ) : null;

		if ( ! $comment || 'review' !== $comment->comment_type ) {
			wp_send_json_error( array( 'message' => __( 'Review not found.', 'nia-theme' ) ), 404 );
		}

		$already_liked = self::user_has_liked( $comment_id );
		$count         = self::get_helpful_count( $comment_id );

		if ( $already_liked ) {
			$count = max( 0, $count - 1 );
		} else {
			++$count;
		}

		update_comment_meta( $comment_id, '_nia_helpful_count', $count );
		self::set_liked_state( $comment_id, ! $already_liked );

		wp_send_json_success(
			array(
				'count' => $count,
				'liked' => ! $already_liked,
			)
		);
	}

	/**
	 * Current Helpful count for a review.
	 *
	 * @param int $comment_id Comment ID.
	 * @return int
	 */
	public static function get_helpful_count( $comment_id ) {
		return (int) get_comment_meta( $comment_id, '_nia_helpful_count', true );
	}

	/**
	 * Whether the current visitor (logged-in or guest) has already voted a
	 * review Helpful.
	 *
	 * @param int $comment_id Comment ID.
	 * @return bool
	 */
	public static function user_has_liked( $comment_id ) {
		$comment_id = (int) $comment_id;

		if ( is_user_logged_in() ) {
			$votes = array_map( 'intval', (array) get_user_meta( get_current_user_id(), '_nia_helpful_reviews', true ) );
			return in_array( $comment_id, $votes, true );
		}

		return in_array( $comment_id, self::get_guest_votes(), true );
	}

	/**
	 * Record (or remove) a Helpful vote for the current visitor.
	 *
	 * @param int  $comment_id Comment ID.
	 * @param bool $liked      True to record the vote, false to remove it.
	 */
	private static function set_liked_state( $comment_id, $liked ) {
		$comment_id = (int) $comment_id;

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$votes   = array_map( 'intval', (array) get_user_meta( $user_id, '_nia_helpful_reviews', true ) );
			$votes   = self::apply_vote( $votes, $comment_id, $liked );
			update_user_meta( $user_id, '_nia_helpful_reviews', $votes );
			return;
		}

		$votes = self::apply_vote( self::get_guest_votes(), $comment_id, $liked );
		setcookie(
			self::GUEST_VOTES_COOKIE,
			wp_json_encode( $votes ),
			time() + YEAR_IN_SECONDS,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
		// So a second AJAX call within the same request sees the updated vote.
		$_COOKIE[ self::GUEST_VOTES_COOKIE ] = wp_json_encode( $votes ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- our own just-encoded value, not user input.
	}

	/**
	 * Add or remove a comment ID from a list of voted comment IDs.
	 *
	 * @param int[] $votes      Existing voted comment IDs.
	 * @param int   $comment_id Comment ID being toggled.
	 * @param bool  $liked      True to add, false to remove.
	 * @return int[]
	 */
	private static function apply_vote( array $votes, $comment_id, $liked ) {
		if ( $liked ) {
			$votes[] = $comment_id;
		} else {
			$votes = array_diff( $votes, array( $comment_id ) );
		}
		return array_values( array_unique( array_map( 'intval', $votes ) ) );
	}

	/**
	 * The current guest's voted comment IDs, from their votes cookie.
	 *
	 * @return int[]
	 */
	private static function get_guest_votes() {
		if ( empty( $_COOKIE[ self::GUEST_VOTES_COOKIE ] ) ) {
			return array();
		}

		$decoded = json_decode( wp_unslash( $_COOKIE[ self::GUEST_VOTES_COOKIE ] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- json_decode'd and type-checked below.
		return is_array( $decoded ) ? array_map( 'intval', $decoded ) : array();
	}

	/**
	 * The wp_list_comments() callback for the Reviews & Ratings section
	 * (content-single-product.php) — mirrors WooCommerce's own
	 * woocommerce_comments() callback structurally (opens the <li>, WP's
	 * walker closes it) but with this project's card design instead of
	 * WooCommerce's default review markup.
	 *
	 * @param WP_Comment $comment Comment being displayed.
	 * @param array      $args    wp_list_comments() args (unused — required by the callback signature).
	 * @param int        $depth   Thread depth (unused — reviews aren't threaded).
	 */
	public static function render_comment( $comment, $args, $depth ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by wp_list_comments()'s callback signature.
		$rating        = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
		$verified      = (int) get_comment_meta( $comment->comment_ID, 'verified', true );
		$helpful_count = self::get_helpful_count( $comment->comment_ID );
		$liked         = self::user_has_liked( $comment->comment_ID );
		?>
		<li <?php comment_class( 'nia-review-card' ); ?> id="li-comment-<?php comment_ID(); ?>">
			<div class="bg-off-white sunlight-shadow rounded-xl p-6 md:p-8">
				<div class="flex items-start justify-between gap-4 mb-4">
					<div class="flex items-center gap-4">
						<?php echo get_avatar( $comment, 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
						<div>
							<p class="font-label-lg text-label-lg text-on-surface"><?php comment_author(); ?></p>
							<?php if ( $verified ) : ?>
								<span class="inline-flex items-center gap-1 text-primary font-label-md text-label-md uppercase tracking-wide text-xs mt-1">
									<span class="material-symbols-outlined text-[14px]">verified</span>
									<?php esc_html_e( 'Verified Purchase', 'nia-theme' ); ?>
								</span>
							<?php endif; ?>
						</div>
					</div>
					<time class="font-body-md text-xs text-on-surface-variant whitespace-nowrap" datetime="<?php comment_time( 'c' ); ?>">
						<?php echo esc_html( get_comment_date( get_option( 'date_format' ) ) ); ?>
					</time>
				</div>

				<?php if ( $rating > 0 ) : ?>
					<div
						class="flex gap-0.5 text-primary mb-3"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating out of 5 stars */ __( 'Rated %d out of 5', 'nia-theme' ), $rating ) ); ?>"
					>
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' <?php echo $i <= $rating ? '1' : '0'; ?>">star</span>
						<?php endfor; ?>
					</div>
				<?php endif; ?>

				<div class="font-body-md text-on-surface-variant nia-review-text">
					<?php comment_text(); ?>
				</div>

				<div class="mt-4 pt-4 border-t border-outline-variant/30">
					<button
						type="button"
						class="nia-helpful-btn inline-flex items-center gap-2 font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors duration-300<?php echo $liked ? ' is-liked' : ''; ?>"
						data-comment-id="<?php echo (int) $comment->comment_ID; ?>"
						aria-pressed="<?php echo $liked ? 'true' : 'false'; ?>"
					>
						<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' <?php echo $liked ? '1' : '0'; ?>">thumb_up</span>
						<?php esc_html_e( 'Helpful', 'nia-theme' ); ?>
						<span class="nia-helpful-count">(<?php echo (int) $helpful_count; ?>)</span>
					</button>
				</div>
			</div>
		<?php
	}

	/**
	 * Sanitized `?review_sort=` query var, used both to build sort-link
	 * hrefs and to order the get_comments() query in the PDP template.
	 *
	 * @return string One of: recent (default), highest, lowest, helpful.
	 */
	public static function get_current_sort() {
		$allowed = array( 'recent', 'highest', 'lowest', 'helpful' );
		$sort    = isset( $_GET['review_sort'] ) ? sanitize_key( wp_unslash( $_GET['review_sort'] ) ) : 'recent'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display sort, not a state-changing action.
		return in_array( $sort, $allowed, true ) ? $sort : 'recent';
	}

	/**
	 * Sanitized `?rating_filter=` query var (1-5, or 0 for "all"), used to
	 * filter the review list by star rating.
	 *
	 * @return int
	 */
	public static function get_current_rating_filter() {
		$filter = isset( $_GET['rating_filter'] ) ? absint( wp_unslash( $_GET['rating_filter'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display filter, not a state-changing action.
		return ( $filter >= 1 && $filter <= 5 ) ? $filter : 0;
	}

	/**
	 * Approved review comments for a product, honoring the current
	 * `?review_sort=`/`?rating_filter=` query vars. "Most Helpful" sorts
	 * in PHP since helpful counts live in commentmeta, not a column
	 * get_comments() can ORDER BY directly.
	 *
	 * @param int $product_id Product ID.
	 * @return WP_Comment[]
	 */
	public static function get_reviews( $product_id ) {
		$sort   = self::get_current_sort();
		$filter = self::get_current_rating_filter();

		$args = array(
			'post_id' => $product_id,
			'type'    => 'review',
			'status'  => 'approve',
		);

		if ( $filter ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small per-product result set, not a site-wide query.
				array(
					'key'   => 'rating',
					'value' => $filter,
				),
			);
		}

		switch ( $sort ) {
			case 'highest':
				$args['meta_key'] = 'rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small per-product result set.
				$args['orderby']  = array( 'meta_value_num' => 'DESC' );
				break;
			case 'lowest':
				$args['meta_key'] = 'rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small per-product result set.
				$args['orderby']  = array( 'meta_value_num' => 'ASC' );
				break;
			default:
				$args['orderby'] = 'comment_date';
				$args['order']   = 'DESC';
				break;
		}

		$reviews = get_comments( $args );

		if ( 'helpful' === $sort ) {
			usort(
				$reviews,
				static function ( $a, $b ) {
					return self::get_helpful_count( $b->comment_ID ) <=> self::get_helpful_count( $a->comment_ID );
				}
			);
		}

		return $reviews;
	}

	/**
	 * The Reviews & Ratings grid (summary/breakdown/write-review column +
	 * sort/filter + review list column) — content-single-product.php's
	 * #nia-reviews-grid markup, extracted here so ajax_filter_reviews() can
	 * render the exact same markup for the AJAX-refreshed fragment instead
	 * of duplicating it. Sort/rating-filter links keep their real
	 * href (?review_sort=.../#nia-reviews) as a no-JS fallback, but carry a
	 * class main.js intercepts to swap this fragment in place instead of a
	 * full page reload — that reload was the "jumps to the top of the
	 * section" bad UX this replaces (a hash-anchor navigation always
	 * scrolls there, even if the reader was scrolled deep into the list).
	 *
	 * @param WC_Product $product Current product.
	 */
	public static function render_reviews_grid( $product ) {
		$nia_avg = (float) $product->get_average_rating();
		?>
		<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-start">
			<!-- Summary + Write a Review -->
			<div class="md:col-span-4 md:sticky md:top-32 space-y-6">
				<div class="bg-off-white sunlight-shadow rounded-xl p-8 text-center">
					<p class="font-display-lg text-6xl text-on-background"><?php echo esc_html( number_format_i18n( $nia_avg, 1 ) ); ?></p>
					<div class="flex justify-center gap-1 text-primary my-3">
						<?php for ( $nia_i = 1; $nia_i <= 5; $nia_i++ ) : ?>
							<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $nia_i <= round( $nia_avg ) ? '1' : '0'; ?>">star</span>
						<?php endfor; ?>
					</div>
					<p class="font-body-md text-on-surface-variant">
						<?php
						/* translators: %d: number of reviews */
						echo esc_html( sprintf( _n( 'Based on %d review', 'Based on %d reviews', $product->get_review_count(), 'nia-theme' ), $product->get_review_count() ) );
						?>
					</p>
				</div>

				<?php
				$nia_rating_counts = $product->get_rating_counts();
				$nia_max_count     = ! empty( $nia_rating_counts ) ? max( $nia_rating_counts ) : 0;
				$nia_active_filter = self::get_current_rating_filter();
				?>
				<?php if ( $nia_max_count > 0 ) : ?>
					<div class="bg-off-white sunlight-shadow rounded-xl p-8 space-y-3">
						<?php for ( $nia_star = 5; $nia_star >= 1; $nia_star-- ) : ?>
							<?php
							$nia_star_count = isset( $nia_rating_counts[ $nia_star ] ) ? (int) $nia_rating_counts[ $nia_star ] : 0;
							$nia_percent    = $nia_max_count ? round( ( $nia_star_count / $nia_max_count ) * 100 ) : 0;
							$nia_is_active  = $nia_active_filter === $nia_star;
							$nia_filter_url = $nia_is_active ? remove_query_arg( 'rating_filter' ) : add_query_arg( 'rating_filter', $nia_star );
							?>
							<a href="<?php echo esc_url( $nia_filter_url . '#nia-reviews' ); ?>" class="nia-review-filter-link flex items-center gap-3 group">
								<span class="font-label-md text-label-md w-12 group-hover:text-primary <?php echo $nia_is_active ? 'text-primary' : 'text-on-surface-variant'; ?>">
									<?php
									/* translators: %d: star rating (1-5) */
									echo esc_html( sprintf( __( '%d star', 'nia-theme' ), $nia_star ) );
									?>
								</span>
								<span class="flex-1 h-2 bg-warm-grey rounded-full overflow-hidden">
									<span class="block h-full bg-primary rounded-full" style="width: <?php echo (int) $nia_percent; ?>%"></span>
								</span>
								<span class="font-label-md text-label-md w-6 text-right text-on-surface-variant"><?php echo (int) $nia_star_count; ?></span>
							</a>
						<?php endfor; ?>
					</div>
				<?php endif; ?>

				<?php
				$nia_reviews_allowed = 'no' === get_option( 'woocommerce_review_rating_verification_required' ) || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() );
				?>
				<?php if ( $nia_reviews_allowed ) : ?>
					<div class="bg-off-white sunlight-shadow rounded-xl p-8">
						<?php comment_form( self::comment_form_args( $product ), $product->get_id() ); ?>
					</div>
				<?php else : ?>
					<div class="bg-surface-container-low rounded-xl p-8 text-center">
						<p class="font-body-md text-on-surface-variant"><?php esc_html_e( 'Only logged-in customers who have purchased this product may leave a review.', 'nia-theme' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Review List -->
			<div class="md:col-span-8">
				<?php
				$nia_reviews   = self::get_reviews( $product->get_id() );
				$nia_sort      = self::get_current_sort();
				$nia_sort_opts = array(
					'recent'  => __( 'Most Recent', 'nia-theme' ),
					'highest' => __( 'Highest Rating', 'nia-theme' ),
					'lowest'  => __( 'Lowest Rating', 'nia-theme' ),
					'helpful' => __( 'Most Helpful', 'nia-theme' ),
				);
				?>
				<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
					<p class="font-label-lg text-label-lg text-on-surface-variant">
						<?php
						/* translators: %d: number of reviews shown */
						echo esc_html( sprintf( _n( '%d Review', '%d Reviews', count( $nia_reviews ), 'nia-theme' ), count( $nia_reviews ) ) );
						?>
					</p>
					<div class="flex flex-wrap items-center gap-2">
						<?php foreach ( $nia_sort_opts as $nia_key => $nia_label ) : ?>
							<a
								href="<?php echo esc_url( add_query_arg( 'review_sort', $nia_key ) . '#nia-reviews' ); ?>"
								class="nia-review-sort-link font-label-md text-label-md uppercase tracking-widest px-3 py-1 rounded-full transition-colors duration-300 <?php echo $nia_sort === $nia_key ? 'bg-on-background text-off-white' : 'text-on-surface-variant hover:text-primary'; ?>"
							>
								<?php echo esc_html( $nia_label ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $nia_reviews ) : ?>
					<ol class="space-y-6">
						<?php
						wp_list_comments(
							array( 'callback' => array( __CLASS__, 'render_comment' ) ),
							$nia_reviews
						);
						?>
					</ol>
				<?php else : ?>
					<div class="bg-surface-container-low rounded-xl p-12 text-center">
						<span class="material-symbols-outlined text-primary text-4xl">rate_review</span>
						<p class="font-body-md text-on-surface-variant mt-4"><?php esc_html_e( 'No reviews yet — be the first to share your experience.', 'nia-theme' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX refresh of #nia-reviews-grid for a sort/rating-filter click —
	 * avoids the full page reload (and the hash-anchor scroll jump that
	 * came with it) the plain href fallback would otherwise trigger.
	 * review_sort/rating_filter are read the same way the page-load path
	 * reads them (self::get_current_sort()/get_current_rating_filter()
	 * via $_GET), so this request sets $_GET from POST first — read-only
	 * display state either way, not a state-changing action, consistent
	 * with those methods' own nonce-less $_GET reads.
	 */
	public function ajax_filter_reviews() {
		check_ajax_referer( 'nia_filter_reviews', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$product    = $product_id ? wc_get_product( $product_id ) : null;

		if ( ! $product ) {
			wp_send_json_error( array( 'message' => __( 'Product not found.', 'nia-theme' ) ), 404 );
		}

		if ( isset( $_POST['review_sort'] ) ) {
			$_GET['review_sort'] = sanitize_key( wp_unslash( $_POST['review_sort'] ) );
		} else {
			unset( $_GET['review_sort'] );
		}

		if ( isset( $_POST['rating_filter'] ) ) {
			$_GET['rating_filter'] = absint( wp_unslash( $_POST['rating_filter'] ) );
		} else {
			unset( $_GET['rating_filter'] );
		}

		ob_start();
		self::render_reviews_grid( $product );
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html ) );
	}

	/**
	 * The comment_form() args for the "Write a review" form — same real
	 * WooCommerce submission pipeline as templates/single-product-reviews.php
	 * (verified-owner gating, rating requirement, moderation), restyled to
	 * this project's design system with a star-radio picker in place of the
	 * default <select>.
	 *
	 * @param WC_Product $product Current product.
	 * @return array
	 */
	public static function comment_form_args( $product ) {
		$commenter           = wp_get_current_commenter();
		$name_email_required = (bool) get_option( 'require_name_email', 1 );

		$fields = array(
			'author' => '<p class="comment-form-author mb-4"><label class="font-label-md text-label-md text-outline uppercase tracking-normal mb-2 block" for="author">' . esc_html__( 'Name', 'nia-theme' ) . ( $name_email_required ? '&nbsp;<span class="text-error">*</span>' : '' ) . '</label><input class="editorial-input font-body-md text-body-md py-2 w-full" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" autocomplete="name"' . ( $name_email_required ? ' required' : '' ) . ' /></p>',
			'email'  => '<p class="comment-form-email mb-4"><label class="font-label-md text-label-md text-outline uppercase tracking-normal mb-2 block" for="email">' . esc_html__( 'Email', 'nia-theme' ) . ( $name_email_required ? '&nbsp;<span class="text-error">*</span>' : '' ) . '</label><input class="editorial-input font-body-md text-body-md py-2 w-full" id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" autocomplete="email"' . ( $name_email_required ? ' required' : '' ) . ' /></p>',
		);

		$comment_field = '';
		if ( wc_review_ratings_enabled() ) {
			// Flat input+label siblings (not nested) so the CSS-only fill
			// effect (input.css .nia-star-picker) can use `~` general-sibling
			// selectors — the same sr-only+peer approach already used for the
			// PDP purchase-option radios, applied to a 5-star picker.
			$comment_field .= '<div class="nia-star-picker mb-6"><span class="font-label-md text-label-md text-outline uppercase tracking-normal mb-2 block">' . esc_html__( 'Your Rating', 'nia-theme' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="text-error">*</span>' : '' ) . '</span><div class="flex flex-row-reverse justify-end gap-1">';
			for ( $i = 5; $i >= 1; $i-- ) {
				$comment_field .= '<input class="sr-only" type="radio" id="nia-rating-' . (int) $i . '" name="rating" value="' . (int) $i . '"' . ( wc_review_ratings_required() ? ' required' : '' ) . ' /><label class="nia-star-picker__star" for="nia-rating-' . (int) $i . '"><span class="material-symbols-outlined">star</span></label>';
			}
			$comment_field .= '</div></div>';
		}
		$comment_field .= '<p class="comment-form-comment"><label class="font-label-md text-label-md text-outline uppercase tracking-normal mb-2 block" for="comment">' . esc_html__( 'Your Review', 'nia-theme' ) . '&nbsp;<span class="text-error">*</span></label><textarea class="editorial-input font-body-md text-body-md py-2 w-full" id="comment" name="comment" rows="5" required></textarea></p>';

		$account_page_url = wc_get_page_permalink( 'myaccount' );

		return array(
			/* translators: %s: product title */
			'title_reply'          => get_comments_number( $product->get_id() ) ? esc_html__( 'Write a Review', 'nia-theme' ) : sprintf( esc_html__( 'Be the first to review "%s"', 'nia-theme' ), get_the_title( $product->get_id() ) ),
			'title_reply_before'   => '<h3 class="font-headline-md text-headline-md text-on-background mb-6">',
			'title_reply_after'    => '</h3>',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'label_submit'         => esc_html__( 'Submit Review', 'nia-theme' ),
			'class_submit'         => 'btn-primary',
			'logged_in_as'         => '',
			'comment_field'        => $comment_field,
			'fields'               => $fields,
			'must_log_in'          => $account_page_url
				? '<p class="font-body-md text-on-surface-variant">' . sprintf(
					/* translators: 1: opening link tag, 2: closing link tag */
					esc_html__( 'You must be %1$slogged in%2$s to write a review.', 'nia-theme' ),
					'<a class="text-primary underline" href="' . esc_url( $account_page_url ) . '">',
					'</a>'
				) . '</p>'
				: '',
		);
	}
}
