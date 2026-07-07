<?php
/**
 * Template Name: Nia The Ritual / Subscription
 *
 * Built ONE template for this page, per DESIGN_SYSTEM.md §0: ritual.html and
 * subscription.html are near-identical (same hero, tiers, testimonials,
 * footer). subscription.html was chosen as the source because "Subscription"
 * — not "Ritual" — is the nav label used consistently sitewide
 * (PAGE_STATUS.md decision, logged 2026-07-07).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="pt-32">

	<!-- Hero -->
	<section class="px-margin-mobile md:px-margin-desktop mb-section-gap max-w-container-max mx-auto">
		<div class="relative grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
			<div class="md:col-span-6 z-10">
				<span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em] mb-4 block"><?php esc_html_e( 'The Daily Ritual', 'nia-theme' ); ?></span>
				<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-6"><?php esc_html_e( 'Wellness on Autopilot.', 'nia-theme' ); ?></h1>
				<p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg mb-10">
					<?php esc_html_e( "Experience the effortless vitality of Tanzania's finest Seamoss, delivered directly to your sanctuary. Subscribe to consistency, unlock premium savings, and let health become your second nature.", 'nia-theme' ); ?>
				</p>
				<div class="flex flex-wrap gap-4">
					<a class="btn-inverse-surface" href="#tiers"><?php esc_html_e( 'Explore Tiers', 'nia-theme' ); ?></a>
					<a class="btn-outline-light" href="#how-it-works"><?php esc_html_e( 'How it Works', 'nia-theme' ); ?></a>
				</div>
			</div>
			<div class="md:col-span-6 h-[400px] md:h-[600px] relative">
				<div class="absolute inset-0 bg-surface-container-low overflow-hidden">
					<img
						class="w-full h-full object-cover"
						src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/subscription-hero.jpg' ); ?>"
						alt="<?php esc_attr_e( 'Premium Tanzanian seamoss gel in a minimalist glass jar', 'nia-theme' ); ?>"
					/>
				</div>
				<div class="absolute -bottom-6 -left-6 bg-off-white p-8 sunlight-shadow hidden md:block">
					<p class="font-display-lg text-headline-md text-primary">15%</p>
					<p class="font-label-md text-label-md uppercase text-on-surface-variant"><?php esc_html_e( 'Savings for Life', 'nia-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- How It Works -->
	<section id="how-it-works" class="bg-surface-container-low py-section-gap px-margin-mobile md:px-margin-desktop scroll-mt-24">
		<div class="max-w-container-max mx-auto text-center mb-16">
			<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-4"><?php esc_html_e( 'The Seamless Journey', 'nia-theme' ); ?></h2>
			<p class="font-body-md text-body-md text-on-surface-variant"><?php esc_html_e( 'Three steps to a lifetime of vitality.', 'nia-theme' ); ?></p>
		</div>
		<div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-3 gap-16">
			<?php
			$nia_steps = array(
				array(
					'icon'  => 'inventory_2',
					'title' => __( 'Select your product', 'nia-theme' ),
					'body'  => __( 'Choose from our curated range of raw gold or purple Seamoss, or our signature infused gels.', 'nia-theme' ),
				),
				array(
					'icon'  => 'calendar_today',
					'title' => __( 'Choose your frequency', 'nia-theme' ),
					'body'  => __( 'Weekly, bi-weekly, or monthly. Tailor your delivery to match your personal wellness rhythm.', 'nia-theme' ),
				),
				array(
					'icon'  => 'auto_awesome',
					'title' => __( 'Receive & Thrive', 'nia-theme' ),
					'body'  => __( 'Open your sanctuary to freshly harvested vitality. Manage, pause, or skip at any time.', 'nia-theme' ),
				),
			);
			foreach ( $nia_steps as $nia_step ) :
				?>
				<div class="text-center group">
					<div class="w-16 h-16 bg-off-white text-primary rounded-full flex items-center justify-center mx-auto mb-6 sunlight-shadow group-hover:bg-primary group-hover:text-off-white transition-colors duration-300">
						<span class="material-symbols-outlined text-3xl"><?php echo esc_html( $nia_step['icon'] ); ?></span>
					</div>
					<h3 class="font-headline-md text-headline-md mb-4"><?php echo esc_html( $nia_step['title'] ); ?></h3>
					<p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $nia_step['body'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Subscription Tiers -->
	<section id="tiers" class="px-margin-mobile md:px-margin-desktop py-section-gap max-w-container-max mx-auto scroll-mt-24">
		<div class="text-center mb-16">
			<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-4"><?php esc_html_e( 'Subscription Tiers', 'nia-theme' ); ?></h2>
			<p class="font-body-md text-body-md text-on-surface-variant"><?php esc_html_e( 'Choose the pace of your transformation.', 'nia-theme' ); ?></p>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">

			<div class="card-subscription-tier hover:scale-[1.02] transition-transform duration-300 flex flex-col">
				<span class="font-label-md text-label-md text-primary uppercase mb-4"><?php esc_html_e( 'The Enthusiast', 'nia-theme' ); ?></span>
				<h3 class="font-headline-md text-headline-md mb-2"><?php esc_html_e( 'Weekly', 'nia-theme' ); ?></h3>
				<div class="flex items-baseline gap-2 mb-6">
					<span class="font-label-lg text-label-lg font-bold">45,000 TZS</span>
					<span class="text-on-surface-variant text-sm"><?php esc_html_e( '/ delivery', 'nia-theme' ); ?></span>
				</div>
				<div class="border-t border-warm-grey pt-6 mb-8 flex-grow">
					<ul class="space-y-4">
						<?php foreach ( array( __( '20% Life-time Savings', 'nia-theme' ), __( 'Free Express Shipping', 'nia-theme' ), __( 'Early Harvest Access', 'nia-theme' ) ) as $nia_perk ) : ?>
							<li class="flex items-center gap-2 font-body-md text-body-md text-on-surface-variant">
								<span class="material-symbols-outlined text-primary text-sm">check</span>
								<?php echo esc_html( $nia_perk ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<a class="btn-outline-light w-full" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'Subscribe Now', 'nia-theme' ); ?></a>
			</div>

			<div class="card-subscription-tier card-subscription-tier--popular relative overflow-hidden">
				<div class="absolute top-0 right-0 bg-primary px-4 py-1 font-label-md text-label-md text-on-primary-container"><?php esc_html_e( 'MOST POPULAR', 'nia-theme' ); ?></div>
				<span class="font-label-md text-label-md text-primary-fixed uppercase mb-4 block"><?php esc_html_e( 'The Balanced', 'nia-theme' ); ?></span>
				<h3 class="font-headline-md text-headline-md mb-2 text-off-white"><?php esc_html_e( 'Bi-Weekly', 'nia-theme' ); ?></h3>
				<div class="flex items-baseline gap-2 mb-6">
					<span class="font-label-lg text-label-lg font-bold">82,000 TZS</span>
					<span class="text-surface-variant text-sm"><?php esc_html_e( '/ delivery', 'nia-theme' ); ?></span>
				</div>
				<div class="border-t border-surface-variant/20 pt-6 mb-8">
					<ul class="space-y-4">
						<?php foreach ( array( __( '15% Savings Included', 'nia-theme' ), __( 'Monthly Wellness Workshop', 'nia-theme' ), __( 'Recipe E-Book Series', 'nia-theme' ) ) as $nia_perk ) : ?>
							<li class="flex items-center gap-2 font-body-md text-body-md text-surface-variant">
								<span class="material-symbols-outlined text-primary-fixed text-sm">check</span>
								<?php echo esc_html( $nia_perk ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<a class="btn-primary-filled w-full" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'Subscribe Now', 'nia-theme' ); ?></a>
			</div>

			<div class="card-subscription-tier hover:scale-[1.02] transition-transform duration-300 flex flex-col">
				<span class="font-label-md text-label-md text-primary uppercase mb-4"><?php esc_html_e( 'The Ritualist', 'nia-theme' ); ?></span>
				<h3 class="font-headline-md text-headline-md mb-2"><?php esc_html_e( 'Monthly', 'nia-theme' ); ?></h3>
				<div class="flex items-baseline gap-2 mb-6">
					<span class="font-label-lg text-label-lg font-bold">155,000 TZS</span>
					<span class="text-on-surface-variant text-sm"><?php esc_html_e( '/ delivery', 'nia-theme' ); ?></span>
				</div>
				<div class="border-t border-warm-grey pt-6 mb-8 flex-grow">
					<ul class="space-y-4">
						<?php foreach ( array( __( '10% Constant Savings', 'nia-theme' ), __( 'Sustainable Glass Jars', 'nia-theme' ), __( 'Eco-Packaging Priority', 'nia-theme' ) ) as $nia_perk ) : ?>
							<li class="flex items-center gap-2 font-body-md text-body-md text-on-surface-variant">
								<span class="material-symbols-outlined text-primary text-sm">check</span>
								<?php echo esc_html( $nia_perk ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<a class="btn-outline-light w-full" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'Subscribe Now', 'nia-theme' ); ?></a>
			</div>

		</div>
	</section>

	<!-- Benefits -->
	<section class="bg-surface py-section-gap px-margin-mobile md:px-margin-desktop">
		<div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
			<div>
				<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-8"><?php esc_html_e( 'Elevate Your Lifestyle', 'nia-theme' ); ?></h2>
				<div class="grid grid-cols-1 gap-8">
					<?php
					$nia_benefits = array(
						array(
							'icon'  => 'workspace_premium',
							'title' => __( 'Exclusive Workshops', 'nia-theme' ),
							'body'  => __( "Join quarterly virtual retreats with Tanzania's leading nutritional experts and holistic healers.", 'nia-theme' ),
						),
						array(
							'icon'  => 'savings',
							'title' => __( '15% Perpetual Savings', 'nia-theme' ),
							'body'  => __( 'Inflation-proof pricing. Your subscription price remains locked for the duration of your journey.', 'nia-theme' ),
						),
						array(
							'icon'  => 'priority_high',
							'title' => __( 'Early Harvest Access', 'nia-theme' ),
							'body'  => __( 'Receive priority fulfillment from our exclusive harvest lots before they reach the general shop.', 'nia-theme' ),
						),
					);
					foreach ( $nia_benefits as $nia_benefit ) :
						?>
						<div class="flex gap-6">
							<span class="material-symbols-outlined text-primary text-4xl"><?php echo esc_html( $nia_benefit['icon'] ); ?></span>
							<div>
								<h4 class="font-label-lg text-label-lg uppercase mb-2"><?php echo esc_html( $nia_benefit['title'] ); ?></h4>
								<p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $nia_benefit['body'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="relative">
				<div class="aspect-square bg-warm-grey overflow-hidden sunlight-shadow">
					<img
						class="w-full h-full object-cover"
						src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/subscription-lifestyle.jpg' ); ?>"
						alt="<?php esc_attr_e( 'Woman practicing yoga in a sunlit minimalist studio', 'nia-theme' ); ?>"
					/>
				</div>
				<div class="absolute -top-10 -right-10 w-48 h-48 bg-tertiary-fixed rounded-full mix-blend-multiply opacity-50 -z-10"></div>
			</div>
		</div>
	</section>

<?php
echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/testimonial',
		'attrs'     => array(
			'heading' => __( 'Shared Journeys', 'nia-theme' ),
			'items'   => array(
				array(
					'quote'     => __( 'Since starting The Daily Ritual, my morning routine has transformed. The energy is sustained, clean, and undeniable. It’s truly medical-grade luxury.', 'nia-theme' ),
					'name'      => __( 'FATUMA M.', 'nia-theme' ),
					'role'      => __( 'SUBSCRIBER FOR 14 MONTHS', 'nia-theme' ),
					'avatarUrl' => NIA_THEME_URI . '/assets/images/placeholders/subscription-avatar-fatuma.jpg',
					'rating'    => 5,
				),
				array(
					'quote'     => __( "Sustainability is as important to me as the nutrients. NIA Nutrition's commitment to Tanzanian coastal communities makes every jar feel like a gift back to the earth.", 'nia-theme' ),
					'name'      => __( 'JOSEPH K.', 'nia-theme' ),
					'role'      => __( 'SUBSCRIBER FOR 8 MONTHS', 'nia-theme' ),
					'avatarUrl' => NIA_THEME_URI . '/assets/images/placeholders/subscription-avatar-joseph.jpg',
					'rating'    => 5,
				),
			),
		),
	)
);
?>

	<!-- CTA -->
	<section class="px-margin-mobile md:px-margin-desktop py-section-gap max-w-container-max mx-auto">
		<div class="bg-primary-container text-on-primary-container p-16 md:p-24 text-center relative overflow-hidden">
			<div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, #000 1px, transparent 0); background-size: 40px 40px;"></div>
			<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-8 relative z-10"><?php esc_html_e( 'Begin Your Ritual Today.', 'nia-theme' ); ?></h2>
			<p class="font-body-lg text-body-lg mb-12 max-w-2xl mx-auto relative z-10">
				<?php esc_html_e( 'Join thousands of people who have automated their wellness journey. No commitments, total flexibility, pure vitality.', 'nia-theme' ); ?>
			</p>
			<a class="btn-inverse-surface sunlight-shadow relative z-10" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Get Started', 'nia-theme' ); ?></a>
		</div>
	</section>

</main>

<?php get_footer(); ?>