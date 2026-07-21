<?php
/**
 * My Account Overview — theme override of WooCommerce's
 * myaccount/dashboard.php, transcribed from nia-products/dashboard.html
 * (PROJECT_PLAN.md Phase 6). Welcome header + status card + a My Rituals
 * summary (honest empty state — subscriptions are Phase 9) + a Wellness
 * Journal recommendations aside (real latest Journal posts, same WP_Query
 * pattern as page-journal.php).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_recent_orders = wc_get_orders(
	array(
		'customer' => get_current_user_id(),
		'limit'    => 1,
		'orderby'  => 'date',
		'order'    => 'DESC',
	)
);
$nia_has_orders    = ! empty( $nia_recent_orders );
?>

<header class="mb-section-gap">
	<div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
		<div>
			<p class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-2"><?php esc_html_e( 'Member Dashboard', 'nia-theme' ); ?></p>
			<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background">
				<?php
				/* translators: %s: customer's first name */
				echo esc_html( sprintf( __( 'Karibu, %s', 'nia-theme' ), $current_user->display_name ) );
				?>
			</h1>
			<p class="text-on-surface-variant mt-2 font-body-md"><?php esc_html_e( 'Your body is a temple; thank you for choosing Nia to nourish it.', 'nia-theme' ); ?></p>
		</div>
		<div class="bg-off-white sunlight-shadow p-6 rounded-lg border-l-4 border-primary-container min-w-[300px]">
			<p class="font-label-md text-label-md text-on-surface-variant uppercase mb-1"><?php esc_html_e( 'Status', 'nia-theme' ); ?></p>
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 <?php echo $nia_has_orders ? 'bg-primary animate-pulse' : 'bg-warm-grey'; ?> rounded-full"></span>
				<span class="font-label-lg text-label-lg text-on-surface"><?php echo $nia_has_orders ? esc_html__( 'Active Customer', 'nia-theme' ) : esc_html__( 'No orders yet', 'nia-theme' ); ?></span>
			</div>
		</div>
	</div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
	<section class="lg:col-span-8 space-y-gutter">
		<h2 class="font-headline-md text-headline-md text-on-background"><?php esc_html_e( 'My Rituals', 'nia-theme' ); ?></h2>
		<div class="bg-off-white p-8 sunlight-shadow rounded-xl flex flex-col items-center text-center gap-4 premium-transition">
			<span class="material-symbols-outlined text-primary text-4xl">auto_awesome</span>
			<p class="font-body-md text-on-surface-variant max-w-md">
				<?php esc_html_e( 'Subscriptions launch in a future update. Once available, your subscribed products and next delivery date will appear here.', 'nia-theme' ); ?>
			</p>
			<a class="btn-primary" href="<?php echo esc_url( wc_get_account_endpoint_url( 'my-rituals' ) ); ?>"><?php esc_html_e( 'Learn More', 'nia-theme' ); ?></a>
		</div>

		<div class="pt-12">
			<div class="flex items-center justify-between mb-8">
				<h2 class="font-headline-md text-headline-md text-on-background"><?php esc_html_e( 'Recent Orders', 'nia-theme' ); ?></h2>
				<a class="btn-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'View All', 'nia-theme' ); ?></a>
			</div>
			<?php if ( $nia_has_orders ) : ?>
				<div class="bg-off-white sunlight-shadow rounded-xl overflow-hidden nia-account-orders-preview">
					<?php foreach ( $nia_recent_orders as $nia_order ) : ?>
						<div class="flex items-center justify-between p-6 border-b border-warm-grey last:border-0">
							<div>
								<p class="font-label-lg text-label-lg text-on-surface font-bold">#<?php echo esc_html( $nia_order->get_order_number() ); ?></p>
								<p class="font-body-md text-on-surface-variant text-sm"><?php echo esc_html( wc_format_datetime( $nia_order->get_date_created() ) ); ?></p>
							</div>
							<p class="font-label-lg text-label-lg font-bold"><?php echo wp_kses_post( $nia_order->get_formatted_order_total() ); ?></p>
							<span class="px-3 py-1 bg-primary/10 text-primary font-label-md text-label-md rounded-full uppercase"><?php echo esc_html( wc_get_order_status_name( $nia_order->get_status() ) ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="bg-off-white sunlight-shadow rounded-xl p-8 text-center">
					<p class="font-body-md text-on-surface-variant mb-4"><?php esc_html_e( 'No orders yet.', 'nia-theme' ); ?></p>
					<a class="btn-primary" href="<?php echo esc_url( ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ) ); ?>"><?php esc_html_e( 'Browse Products', 'nia-theme' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<aside class="lg:col-span-4 space-y-gutter">
		<h2 class="font-headline-md text-headline-md text-on-background"><?php esc_html_e( 'Wellness Journal', 'nia-theme' ); ?></h2>
		<div class="bg-surface-container-low p-8 rounded-xl space-y-8">
			<div>
				<h3 class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-4"><?php esc_html_e( 'Personalized For You', 'nia-theme' ); ?></h3>
				<div class="space-y-6">
					<?php
					$nia_journal_query = new WP_Query(
						array(
							'post_type'      => 'post',
							'posts_per_page' => 2,
							'orderby'        => 'date',
							'order'          => 'DESC',
						)
					);
					if ( $nia_journal_query->have_posts() ) :
						while ( $nia_journal_query->have_posts() ) :
							$nia_journal_query->the_post();
							?>
							<a class="group block cursor-pointer" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="h-40 overflow-hidden rounded-lg mb-4">
										<?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover group-hover:scale-110 premium-transition' ) ); ?>
									</div>
								<?php endif; ?>
								<h4 class="font-headline-md text-[18px] text-on-surface group-hover:text-primary premium-transition"><?php the_title(); ?></h4>
								<p class="text-on-surface-variant font-body-md mt-2 line-clamp-2"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
							</a>
							<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
			<hr class="border-outline-variant opacity-30" />
			<div>
				<h3 class="font-label-lg text-label-lg text-on-surface uppercase tracking-widest mb-4"><?php esc_html_e( 'Quick Links', 'nia-theme' ); ?></h3>
				<div class="space-y-4">
					<a class="flex items-center justify-between p-4 bg-off-white rounded-lg sunlight-shadow premium-transition hover:-translate-y-1" href="<?php echo esc_url( wc_get_account_endpoint_url( 'wellness-profile' ) ); ?>">
						<span class="font-label-lg text-label-lg"><?php esc_html_e( 'Wellness Profile', 'nia-theme' ); ?></span>
						<span class="material-symbols-outlined text-primary">arrow_forward</span>
					</a>
					<a class="flex items-center justify-between p-4 bg-off-white rounded-lg sunlight-shadow premium-transition hover:-translate-y-1" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<span class="font-label-lg text-label-lg"><?php esc_html_e( 'Contact Us', 'nia-theme' ); ?></span>
						<span class="material-symbols-outlined text-primary">chat</span>
					</a>
				</div>
			</div>
		</div>
	</aside>
</div>
