<?php
/**
 * Template Name: Nia FAQ
 *
 * Transcribed from nia-products/faqs.html, re-themed from the stale
 * olive-gold palette (Set B, #735c00) to the canonical orange-gold tokens —
 * DESIGN_SYSTEM.md §0. Accordion + category-tab interaction rebuilt with
 * Alpine.js instead of the mockup's vanilla JS (ARCHITECTURE.md §3).
 *
 * A "Payments" FAQ block is added below (the mockup's category grid links
 * to one, but no corresponding content block exists in the source) — filled
 * with reasonable placeholder copy matching the TZS/USD + Mobile Money
 * facts already established elsewhere in this project.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_faq_categories = array(
	'shipping'      => array(
		'label'   => __( 'Shipping', 'nia-theme' ),
		'icon'    => 'local_shipping',
		'heading' => __( 'Shipping & Delivery', 'nia-theme' ),
		'items'   => array(
			array(
				'q' => __( 'Do you deliver within Tanzania?', 'nia-theme' ),
				'a' => __( 'Yes, we provide premium door-to-door delivery across Tanzania. Within Dar es Salaam, orders typically arrive within 24 hours. For regions like Arusha, Mwanza, and Zanzibar, please allow 2-3 business days. All domestic orders are handled with refrigerated care to maintain the integrity of our Seamoss gels.', 'nia-theme' ),
			),
			array(
				'q' => __( 'What are your international shipping rates?', 'nia-theme' ),
				'a' => __( 'We ship our dried Seamoss varieties internationally via DHL Express. Shipping rates are calculated at checkout based on your location and weight. Please note that while we ship globally, import duties and taxes are the responsibility of the recipient. International delivery typically takes 5-7 business days.', 'nia-theme' ),
			),
		),
	),
	'seamoss'       => array(
		'label'   => __( 'Seamoss', 'nia-theme' ),
		'icon'    => 'eco',
		'heading' => __( 'Seamoss Benefits & Types', 'nia-theme' ),
		'items'   => array(
			array(
				'q' => __( 'What is the difference between Gold and Purple Seamoss?', 'nia-theme' ),
				'a' => __( 'Gold Seamoss is sun-dried to a pale color and has a very mild taste, making it ideal for mixing into smoothies and juices. Purple Seamoss is dried in the shade to preserve its anthocyanins—powerful antioxidants. It has a slightly stronger "ocean" taste and a more robust nutritional profile. Both offer the 92 essential minerals your body needs.', 'nia-theme' ),
			),
			array(
				'q' => __( 'Where is your Seamoss sourced?', 'nia-theme' ),
				'a' => __( 'Our Seamoss is wild-crafted from the pristine, mineral-rich waters of the Zanzibar archipelago. We work directly with local coastal communities to ensure sustainable harvesting practices that protect the marine ecosystem while delivering the highest pharmaceutical-grade quality.', 'nia-theme' ),
			),
		),
	),
	'subscriptions' => array(
		'label'   => __( 'Subscriptions', 'nia-theme' ),
		'icon'    => 'calendar_today',
		'heading' => __( 'Subscriptions', 'nia-theme' ),
		'items'   => array(
			array(
				'q' => __( 'How does the Nia Vitality Club work?', 'nia-theme' ),
				'a' => __( 'Members receive a fresh delivery of their chosen Seamoss gel every 30 days. You enjoy a 15% discount on all orders and exclusive access to limited-edition blends. You can pause, skip, or cancel your subscription at any time through your account portal.', 'nia-theme' ),
			),
		),
	),
	'payments'      => array(
		'label'   => __( 'Payments', 'nia-theme' ),
		'icon'    => 'payments',
		'heading' => __( 'Payments', 'nia-theme' ),
		'items'   => array(
			array(
				'q' => __( 'Which currencies can I pay in?', 'nia-theme' ),
				'a' => __( 'Prices are shown in Tanzanian Shillings (TZS) or US Dollars (USD) — switch currencies from the header. Whichever currency you browse in, the checkout confirms the exact amount that will be charged.', 'nia-theme' ),
			),
			array(
				'q' => __( 'What payment methods do you accept?', 'nia-theme' ),
				'a' => __( 'We accept Mobile Money (M-Pesa, Tigo Pesa, Airtel Money) for Tanzanian customers, plus major debit/credit cards for both local and international orders.', 'nia-theme' ),
			),
		),
	),
);

get_header();
?>

<main class="pt-32 pb-section-gap">

	<!-- Hero -->
	<section class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto mb-20 text-center">
		<span class="font-label-lg text-label-lg text-primary tracking-[0.2em] mb-4 block"><?php esc_html_e( 'SUPPORT CENTER', 'nia-theme' ); ?></span>
		<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-surface mb-6"><?php esc_html_e( 'How can we assist you?', 'nia-theme' ); ?></h1>
		<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">
			<?php esc_html_e( 'Explore our guide to premium vitality. From Tanzanian delivery to our unique Seamoss sourcing, find the clarity you need for your wellness journey.', 'nia-theme' ); ?>
		</p>
	</section>

	<!-- Category Grid (DESIGN_SYSTEM.md §4 item H tab pattern, plain anchor links) -->
	<section class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto mb-20">
		<div class="grid grid-cols-2 md:grid-cols-4 gap-gutter">
			<?php foreach ( $nia_faq_categories as $nia_slug => $nia_cat ) : ?>
				<a href="#faq-<?php echo esc_attr( $nia_slug ); ?>" class="group p-8 bg-off-white border border-warm-grey text-center hover:bg-surface-container-low transition-all duration-300 block">
					<span class="material-symbols-outlined text-primary text-4xl mb-4 block"><?php echo esc_html( $nia_cat['icon'] ); ?></span>
					<span class="font-label-lg text-label-lg block uppercase tracking-widest"><?php echo esc_html( $nia_cat['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- FAQ Content -->
	<section class="px-margin-mobile md:px-margin-desktop max-w-4xl mx-auto space-y-section-gap" x-data="{ openId: null }">
		<?php foreach ( $nia_faq_categories as $nia_slug => $nia_cat ) : ?>
			<div id="faq-<?php echo esc_attr( $nia_slug ); ?>" class="scroll-mt-32">
				<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-12 border-b border-warm-grey pb-4"><?php echo esc_html( $nia_cat['heading'] ); ?></h2>
				<div class="space-y-4">
					<?php foreach ( $nia_cat['items'] as $nia_index => $nia_item ) : ?>
						<?php $nia_item_id = esc_attr( $nia_slug . '-' . $nia_index ); ?>
						<div class="border-b border-warm-grey">
							<button
								type="button"
								class="w-full flex justify-between items-center py-6 text-left hover:text-primary transition-colors"
								@click="openId = (openId === '<?php echo esc_attr( $nia_item_id ); ?>' ? null : '<?php echo esc_attr( $nia_item_id ); ?>')"
							>
								<span class="font-headline-md text-headline-md"><?php echo esc_html( $nia_item['q'] ); ?></span>
								<span
									class="material-symbols-outlined transition-transform duration-300"
									:class="openId === '<?php echo esc_attr( $nia_item_id ); ?>' ? 'rotate-45' : ''"
								>add</span>
							</button>
							<div
								x-show="openId === '<?php echo esc_attr( $nia_item_id ); ?>'"
								x-transition:enter="transition ease-out duration-300"
								x-transition:enter-start="opacity-0"
								x-transition:enter-end="opacity-100"
								x-transition:leave="transition ease-in duration-200"
								x-transition:leave-start="opacity-100"
								x-transition:leave-end="opacity-0"
								class="pb-6"
							>
								<p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $nia_item['a'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>

	<!-- CTA -->
	<section class="mt-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
		<div class="bg-surface-container-high p-12 md:p-20 text-center relative overflow-hidden">
			<div class="relative z-10">
				<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-surface mb-8"><?php esc_html_e( 'Still have questions?', 'nia-theme' ); ?></h2>
				<p class="font-body-lg text-body-lg text-on-surface-variant mb-12 max-w-xl mx-auto">
					<?php esc_html_e( 'Our nutritionists are available for a personalized consultation to help you choose the right path for your vitality.', 'nia-theme' ); ?>
				</p>
				<a class="btn-primary inline-flex items-center gap-4" href="https://wa.me/yourwhatsapplink">
					<?php esc_html_e( 'Chat with a Wellness Expert', 'nia-theme' ); ?>
					<span class="material-symbols-outlined">arrow_forward</span>
				</a>
			</div>
			<div class="absolute -bottom-20 -right-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
			<div class="absolute -top-20 -left-20 w-96 h-96 bg-secondary/5 rounded-full blur-3xl"></div>
		</div>
	</section>

</main>

<?php get_footer(); ?>