<?php
/**
 * Product Detail Page — theme override of WooCommerce's
 * content-single-product.php, transcribed from nia-products/product.html
 * (PROJECT_PLAN.md Phase 6). Fully custom markup (not the default hook
 * stack) since the mockup's layout — 4-up gallery, sticky purchase panel,
 * purchase-type radio, ingredients/how-to-use/testimonials — doesn't map
 * onto WooCommerce's default single-product hooks. Built as the one shared
 * template for any product in any category (ARCHITECTURE.md §9a) — nothing
 * below assumes a specific product ID or category.
 *
 * The one-time/subscription purchase-type radio is UI + client-side price
 * preview only (Alpine) — the real subscription purchase/checkout path is
 * Phase 9's renewal-request engine (ARCHITECTURE.md §5), not built yet.
 * Selecting "subscription" and adding to bag still places a normal one-time
 * order today; the panel says so explicitly rather than silently pretending
 * otherwise.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP core output.
	return;
}

$nia_main_image_id = $product->get_image_id();
$nia_gallery_ids   = $product->get_gallery_image_ids();
$nia_all_image_ids = $nia_main_image_id ? array_unique( array_merge( array( $nia_main_image_id ), $nia_gallery_ids ) ) : $nia_gallery_ids;

$nia_regular_price  = (float) $product->get_regular_price();
$nia_one_time_price = $product->get_price_html();

// Subscription pricing is a UI preview only (Phase 9 dependency, see docblock) — 15% off the regular price, matching DESIGN_SYSTEM.md §10's subscription-tier discount range.
$nia_sub_percent_off = 15;
$nia_sub_price_raw   = $nia_regular_price > 0 ? $nia_regular_price * ( 1 - $nia_sub_percent_off / 100 ) : 0;
$nia_sub_price_html  = $nia_regular_price > 0 ? wc_price( $nia_sub_price_raw ) : '';

$nia_category_list = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ' ) );

// Placeholder WhatsApp number — replace with the client's real WhatsApp Business number before launch (PAGE_STATUS.md, Phase 6 note).
$nia_whatsapp_number = '255000000000';
$nia_whatsapp_text   = rawurlencode( sprintf( 'Hi Nia, I have a question about %s.', $product->get_name() ) );
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<!-- Product Hero Section -->
	<section class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-start" x-data="{ activeImage: 0 }">
		<!-- Left: Gallery -->
		<div class="md:col-span-7 grid grid-cols-1 gap-4">
			<div class="aspect-[4/5] bg-warm-grey overflow-hidden group">
				<?php foreach ( $nia_all_image_ids as $nia_index => $nia_image_id ) : ?>
					<template x-if="activeImage === <?php echo (int) $nia_index; ?>">
						<?php
						echo wp_kses_post(
							wp_get_attachment_image(
								$nia_image_id,
								'woocommerce_single',
								false,
								array( 'class' => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105' )
							)
						);
						?>
					</template>
				<?php endforeach; ?>
			</div>
			<?php if ( count( $nia_all_image_ids ) > 1 ) : ?>
				<div class="grid grid-cols-3 gap-4">
					<?php foreach ( $nia_all_image_ids as $nia_index => $nia_image_id ) : ?>
						<button type="button" class="aspect-square bg-warm-grey overflow-hidden" @click="activeImage = <?php echo (int) $nia_index; ?>">
							<?php echo wp_kses_post( wp_get_attachment_image( $nia_image_id, 'thumbnail', false, array( 'class' => 'w-full h-full object-cover' ) ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Right: Purchase Controls -->
		<div class="md:col-span-5 md:sticky md:top-32 space-y-8" x-data="{ purchaseOption: 'one_time' }">
			<div>
				<h1 class="font-display-lg text-display-lg-mobile md:text-headline-lg mb-2"><?php the_title(); ?></h1>
				<?php if ( $nia_category_list ) : ?>
					<p class="font-label-lg text-label-lg text-outline mb-6 tracking-widest uppercase"><?php echo esc_html( $nia_category_list ); ?></p>
				<?php endif; ?>
				<div class="font-label-lg text-[24px] font-bold" :class="{ 'text-primary': purchaseOption === 'subscription' }">
					<span x-show="purchaseOption === 'one_time'"><?php echo wp_kses_post( $nia_one_time_price ); ?></span>
					<?php if ( $nia_sub_price_html ) : ?>
						<span x-show="purchaseOption === 'subscription'" x-cloak><?php echo wp_kses_post( $nia_sub_price_html ); ?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="space-y-4">
				<p class="font-label-lg text-label-lg"><?php esc_html_e( 'SELECT RITUAL', 'nia-theme' ); ?></p>
				<!-- One Time Purchase -->
				<div class="relative">
					<input checked class="sr-only peer" id="one_time" name="purchase_option" type="radio" value="one_time" x-model="purchaseOption" />
					<label class="flex items-center justify-between p-6 border border-warm-grey cursor-pointer transition-all duration-300 hover:border-on-background peer-checked:border-on-background peer-checked:bg-surface-container" for="one_time">
						<span class="flex flex-col">
							<span class="font-label-lg text-label-lg"><?php esc_html_e( 'ONE-TIME PURCHASE', 'nia-theme' ); ?></span>
							<span class="font-body-md text-sm text-outline"><?php esc_html_e( 'Single item, ships once', 'nia-theme' ); ?></span>
						</span>
						<span class="font-label-lg text-label-lg"><?php echo wp_kses_post( $nia_one_time_price ); ?></span>
					</label>
				</div>
				<?php if ( $nia_sub_price_html ) : ?>
					<!-- Subscription -->
					<div class="relative">
						<input class="sr-only peer" id="subscription" name="purchase_option" type="radio" value="subscription" x-model="purchaseOption" />
						<label class="flex items-center justify-between p-6 border border-warm-grey cursor-pointer transition-all duration-300 hover:border-on-background peer-checked:border-on-background peer-checked:bg-surface-container" for="subscription">
							<span class="flex flex-col">
								<span class="font-label-lg text-label-lg"><?php esc_html_e( 'THE DAILY RITUAL', 'nia-theme' ); ?></span>
								<span class="font-body-md text-sm text-outline">
									<?php
									/* translators: %d: subscription discount percentage */
									echo esc_html( sprintf( __( 'Monthly subscription, %d%% off', 'nia-theme' ), $nia_sub_percent_off ) );
									?>
								</span>
							</span>
							<span class="font-label-lg text-label-lg text-primary"><?php echo wp_kses_post( $nia_sub_price_html ); ?></span>
						</label>
						<div class="absolute -top-2 right-4 bg-primary text-off-white text-[10px] px-2 py-0.5 font-bold uppercase tracking-tighter">
							<?php esc_html_e( 'Recommended', 'nia-theme' ); ?>
						</div>
					</div>
					<p class="font-body-md text-sm text-outline" x-show="purchaseOption === 'subscription'" x-cloak>
						<?php esc_html_e( 'Subscription checkout arrives in a future update — selecting this today still adds one item at the standard one-time price.', 'nia-theme' ); ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="nia-pdp-add-to-cart">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
			<div class="flex flex-col gap-4">
				<a
					class="w-full border border-on-background text-on-background py-5 font-label-lg text-label-lg tracking-widest uppercase flex items-center justify-center gap-2 hover:bg-on-background hover:text-off-white transition-all duration-300 active:scale-[0.98]"
					href="<?php echo esc_url( 'https://wa.me/' . $nia_whatsapp_number . '?text=' . $nia_whatsapp_text ); ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<span class="material-symbols-outlined text-[18px]">chat</span>
					<?php esc_html_e( 'WhatsApp Inquiry', 'nia-theme' ); ?>
				</a>
			</div>
			<div class="pt-8 space-y-4">
				<div class="flex items-center gap-3">
					<span class="material-symbols-outlined text-primary">verified_user</span>
					<span class="font-label-lg text-label-lg"><?php esc_html_e( 'Lab Tested for Purity', 'nia-theme' ); ?></span>
				</div>
				<div class="flex items-center gap-3">
					<span class="material-symbols-outlined text-primary">local_shipping</span>
					<span class="font-label-lg text-label-lg"><?php esc_html_e( 'Same-day Delivery in Dar es Salaam', 'nia-theme' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<?php if ( post_type_supports( 'product', 'comments' ) && comments_open( $product->get_id() ) ) : ?>
		<!-- Reviews & Ratings -->
		<section id="nia-reviews" class="mt-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
			<h2 class="font-display-lg text-headline-lg mb-12"><?php esc_html_e( 'Customer Reviews', 'nia-theme' ); ?></h2>

			<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-start">
				<!-- Summary + Write a Review -->
				<div class="md:col-span-4 md:sticky md:top-32 space-y-6">
					<div class="bg-off-white sunlight-shadow rounded-xl p-8 text-center">
						<?php $nia_avg = (float) $product->get_average_rating(); ?>
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
					$nia_active_filter = Nia_Reviews::get_current_rating_filter();
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
								<a href="<?php echo esc_url( $nia_filter_url . '#nia-reviews' ); ?>" class="flex items-center gap-3 group">
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
							<?php comment_form( Nia_Reviews::comment_form_args( $product ), $product->get_id() ); ?>
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
					$nia_reviews   = Nia_Reviews::get_reviews( $product->get_id() );
					$nia_sort      = Nia_Reviews::get_current_sort();
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
									class="font-label-md text-label-md uppercase tracking-widest px-3 py-1 rounded-full transition-colors duration-300 <?php echo $nia_sort === $nia_key ? 'bg-on-background text-off-white' : 'text-on-surface-variant hover:text-primary'; ?>"
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
								array( 'callback' => array( 'Nia_Reviews', 'render_comment' ) ),
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
		</section>
	<?php endif; ?>

	<?php
	echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
		array(
			'blockName' => 'nia/benefit-grid',
			'attrs'     => array(
				'eyebrow' => '',
				'heading' => __( 'Power of 92 Minerals', 'nia-theme' ),
				'intro'   => __( 'Our wild-harvested seamoss contains almost every mineral the human body needs to thrive. A foundation for longevity and holistic health.', 'nia-theme' ),
				'items'   => array(
					array(
						'icon'  => 'bolt',
						'title' => __( 'Sustained Energy', 'nia-theme' ),
						'body'  => __( 'Rich in iron and B-vitamins for a natural morning lift without the caffeine crash.', 'nia-theme' ),
					),
					array(
						'icon'  => 'health_and_safety',
						'title' => __( 'Immune Fortress', 'nia-theme' ),
						'body'  => __( 'High potassium chloride content helps resolve inflammation and strengthens defenses.', 'nia-theme' ),
					),
					array(
						'icon'  => 'auto_awesome',
						'title' => __( 'Radiant Skin', 'nia-theme' ),
						'body'  => __( "Nature's collagen — supports skin elasticity, hair growth, and nail strength from within.", 'nia-theme' ),
					),
					array(
						'icon'  => 'psychology',
						'title' => __( 'Mental Clarity', 'nia-theme' ),
						'body'  => __( 'Magnesium and potassium support brain function and emotional equilibrium.', 'nia-theme' ),
					),
				),
			),
		)
	);
	?>

	<!-- Purely Sourced -->
	<section class="mt-section-gap grid grid-cols-1 md:grid-cols-2 gap-gutter items-center bg-inverse-surface text-off-white p-margin-mobile md:p-20">
		<div class="space-y-6">
			<h2 class="font-display-lg text-headline-lg"><?php esc_html_e( 'Purely Sourced', 'nia-theme' ); ?></h2>
			<p class="font-body-lg opacity-80">
				<?php esc_html_e( 'We believe in transparency. No fillers, no preservatives — just the essence of the Indian Ocean and the Tanzanian earth.', 'nia-theme' ); ?>
			</p>
			<ul class="space-y-4">
				<li class="flex items-center gap-4">
					<span class="w-1.5 h-1.5 bg-primary-fixed rounded-full"></span>
					<span class="font-label-lg text-label-lg"><?php esc_html_e( 'PURE TANZANIAN SEAMOSS', 'nia-theme' ); ?></span>
				</li>
				<li class="flex items-center gap-4">
					<span class="w-1.5 h-1.5 bg-primary-fixed rounded-full"></span>
					<span class="font-label-lg text-label-lg"><?php esc_html_e( 'WILDCRAFTED, NOT FARMED', 'nia-theme' ); ?></span>
				</li>
				<li class="flex items-center gap-4">
					<span class="w-1.5 h-1.5 bg-primary-fixed rounded-full"></span>
					<span class="font-label-lg text-label-lg"><?php esc_html_e( 'SUN-DRIED, TRADITIONAL METHOD', 'nia-theme' ); ?></span>
				</li>
			</ul>
		</div>
		<div class="aspect-video bg-surface-variant overflow-hidden">
			<img
				class="w-full h-full object-cover grayscale brightness-75 hover:grayscale-0 transition-all duration-1000"
				src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/about-seamoss-texture.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Raw Tanzanian seamoss', 'nia-theme' ); ?>"
			/>
		</div>
	</section>

	<!-- The Daily Ritual (how to use) -->
	<section class="mt-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
		<div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12">
			<div class="max-w-xl">
				<h2 class="font-display-lg text-headline-lg mb-4"><?php esc_html_e( 'The Daily Ritual', 'nia-theme' ); ?></h2>
				<p class="font-body-lg text-outline"><?php esc_html_e( 'A simple transition into high-performance living. Incorporate into your routine daily.', 'nia-theme' ); ?></p>
			</div>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-12">
			<div class="space-y-4">
				<div class="font-display-lg text-primary text-6xl opacity-20">01</div>
				<h3 class="font-label-lg text-label-lg uppercase border-b border-warm-grey pb-2"><?php esc_html_e( 'Morning Smoothies', 'nia-theme' ); ?></h3>
				<p class="font-body-md text-outline"><?php esc_html_e( 'Blend into your favorite fruit or green smoothie for an immediate mineral infusion.', 'nia-theme' ); ?></p>
			</div>
			<div class="space-y-4">
				<div class="font-display-lg text-primary text-6xl opacity-20">02</div>
				<h3 class="font-label-lg text-label-lg uppercase border-b border-warm-grey pb-2"><?php esc_html_e( 'Breakfast Bowls', 'nia-theme' ); ?></h3>
				<p class="font-body-md text-outline"><?php esc_html_e( 'Stir into warm porridge or top off your yogurt and granola. Dissolves into hot or cold foods.', 'nia-theme' ); ?></p>
			</div>
			<div class="space-y-4">
				<div class="font-display-lg text-primary text-6xl opacity-20">03</div>
				<h3 class="font-label-lg text-label-lg uppercase border-b border-warm-grey pb-2"><?php esc_html_e( 'As Directed', 'nia-theme' ); ?></h3>
				<p class="font-body-md text-outline"><?php esc_html_e( 'Follow the pack instructions for maximum bioavailability, at the same time each day.', 'nia-theme' ); ?></p>
			</div>
		</div>
	</section>

	<?php
	echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
		array(
			'blockName' => 'nia/testimonial',
			'attrs'     => array(
				'heading' => __( 'Safari ya Afya', 'nia-theme' ),
				'items'   => array(
					array(
						'quote'     => __( 'Since starting the subscription, my energy levels throughout the workday have transformed. It\'s become my non-negotiable morning start.', 'nia-theme' ),
						'name'      => __( 'FATUMA M.', 'nia-theme' ),
						'role'      => __( 'ARCHITECT, DAR ES SALAAM', 'nia-theme' ),
						'avatarUrl' => NIA_THEME_URI . '/assets/images/placeholders/subscription-avatar-fatuma.jpg',
						'rating'    => 5,
					),
					array(
						'quote'     => __( 'Finally a premium Tanzanian brand that delivers on its promise. My whole family is now on Nia.', 'nia-theme' ),
						'name'      => __( 'JOSEPH K.', 'nia-theme' ),
						'role'      => __( 'ENTREPRENEUR', 'nia-theme' ),
						'avatarUrl' => NIA_THEME_URI . '/assets/images/placeholders/subscription-avatar-joseph.jpg',
						'rating'    => 5,
					),
				),
			),
		)
	);
	?>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary — kept for related
	 * products only (upsells are already surfaced via the Cart page, task 58).
	 *
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>
