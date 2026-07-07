<?php
/**
 * Template Name: Nia Homepage
 *
 * Transcribed from nia-products/index.html (DESIGN_SYSTEM.md source of
 * truth). Built as a dedicated page template rather than block-editor
 * content — this is a fixed, bespoke layout (ARCHITECTURE.md §2), not a
 * pattern the owner rearranges. Reuses the custom block library via
 * render_block() for the repeating sections (Hero, Benefit Grid, Newsletter)
 * so those stay in sync with the same blocks used elsewhere.
 *
 * Featured Products below is a static placeholder (4 hardcoded cards) —
 * Phase 5 replaces it with a live WooCommerce Featured-product query
 * (ARCHITECTURE.md §9a). All photography is AI-generated placeholder
 * imagery pending real product/lifestyle photos (RISKS.md R6).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/hero',
		'attrs'     => array(
			'heading'       => __( 'Nature’s Gold, Rooted in Wellness.', 'nia-theme' ),
			'body'          => __( 'Discover the pure power of Tanzanian Seamoss for your daily ritual.', 'nia-theme' ),
			'imageUrl'      => esc_url( NIA_THEME_URI . '/assets/images/placeholders/homepage-hero.jpg' ),
			'imageAlt'      => __( 'Woman in cream loungewear beside a Nia seamoss jar in a sunlit studio', 'nia-theme' ),
			'primaryText'   => __( 'Shop the Collection', 'nia-theme' ),
			'primaryUrl'    => nia_theme_shop_url(),
			'secondaryText' => __( 'Explore the Ritual', 'nia-theme' ),
			'secondaryUrl'  => home_url( '/subscription/' ),
		),
	)
);
?>

<!-- Brand Story -->
<section class="py-section-gap px-margin-mobile md:px-margin-desktop bg-warm-ivory">
	<div class="max-w-container-max mx-auto editorial-grid">
		<div class="col-span-12 md:col-span-6 flex flex-col justify-center order-2 md:order-1">
			<span class="font-label-lg text-primary uppercase tracking-[0.2em] mb-4"><?php esc_html_e( 'Our Heritage', 'nia-theme' ); ?></span>
			<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-8 italic">&ldquo;<?php esc_html_e( 'Let food be the medicine, but make it fun.', 'nia-theme' ); ?>&rdquo;</h2>
			<p class="font-body-lg text-body-lg text-on-surface-variant mb-8 leading-relaxed">
				<?php esc_html_e( 'Nia Nutrition was born in the heart of Dar es Salaam with a singular vision: to elevate Tanzanian nutrient-rich superfoods into a global luxury standard. Founded by visionary nutritionists, we bridge the gap between ancestral wisdom and contemporary vitality.', 'nia-theme' ); ?>
			</p>
			<p class="font-body-md text-body-md text-on-surface-variant mb-10">
				<?php esc_html_e( "Our Seamoss is wild-harvested along the pristine coastlines of the Indian Ocean, ensuring every gram is packed with 92 essential minerals. We don't just sell nutrition; we offer a path to a more vibrant, energized life.", 'nia-theme' ); ?>
			</p>
			<div class="flex items-center gap-6">
				<div class="w-16 h-[1px] bg-primary"></div>
				<span class="font-label-lg text-on-background font-bold uppercase tracking-widest"><?php esc_html_e( 'Lydia C., Founder', 'nia-theme' ); ?></span>
			</div>
		</div>
		<div class="col-span-12 md:col-span-5 md:col-start-8 order-1 md:order-2 mb-12 md:mb-0 relative">
			<div class="absolute -top-12 -left-12 w-full h-full bg-warm-grey -z-10 translate-x-4 translate-y-4"></div>
			<img
				class="w-full h-[600px] object-cover grayscale-[20%] hover:grayscale-0 transition-all duration-700"
				src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/homepage-brand-story.jpg' ); ?>"
				alt="<?php esc_attr_e( "Portrait of Nia Nutrition's founder", 'nia-theme' ); ?>"
			/>
		</div>
	</div>
</section>

<!-- Featured Products (static placeholder — Phase 5 wires a live WooCommerce Featured-product query, ARCHITECTURE.md §9a) -->
<section class="py-section-gap bg-surface px-margin-mobile md:px-margin-desktop overflow-hidden">
	<div class="max-w-container-max mx-auto">
		<div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
			<div>
				<span class="font-label-lg text-primary uppercase tracking-[0.2em] mb-4 block"><?php esc_html_e( 'Selection', 'nia-theme' ); ?></span>
				<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg"><?php esc_html_e( 'Core Collection', 'nia-theme' ); ?></h2>
			</div>
			<a class="btn-link" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'View All Products', 'nia-theme' ); ?></a>
		</div>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
			<?php
			$nia_featured_placeholder = array(
				array(
					'eyebrow' => __( 'VITALITY', 'nia-theme' ),
					'title'   => __( 'Seamoss Powder', 'nia-theme' ),
					'price'   => '45,000 TZS',
					'image'   => 'product-powder.jpg',
				),
				array(
					'eyebrow' => __( 'HYDRATION', 'nia-theme' ),
					'title'   => __( 'Seamoss Gel', 'nia-theme' ),
					'price'   => '55,000 TZS',
					'image'   => 'product-gel.jpg',
				),
				array(
					'eyebrow' => __( 'PURE EARTH', 'nia-theme' ),
					'title'   => __( 'Raw Seamoss', 'nia-theme' ),
					'price'   => '35,000 TZS',
					'image'   => 'product-raw.jpg',
				),
				array(
					'eyebrow' => __( 'DAILY RITUAL', 'nia-theme' ),
					'title'   => __( 'Seamoss Capsules', 'nia-theme' ),
					'price'   => '65,000 TZS',
					'image'   => 'product-capsules.jpg',
				),
			);
			foreach ( $nia_featured_placeholder as $nia_product ) :
				?>
				<div class="group cursor-pointer">
					<a href="<?php echo esc_url( nia_theme_shop_url() ); ?>">
						<div class="card-product mb-6">
							<img class="card-product-image" src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/' . $nia_product['image'] ); ?>" alt="<?php echo esc_attr( $nia_product['title'] ); ?>" />
						</div>
						<div class="flex flex-col items-center text-center">
							<span class="font-label-md text-primary mb-2"><?php echo esc_html( $nia_product['eyebrow'] ); ?></span>
							<h3 class="font-headline-sm text-on-background mb-2"><?php echo esc_html( $nia_product['title'] ); ?></h3>
							<p class="font-label-lg text-on-surface font-bold"><?php echo esc_html( $nia_product['price'] ); ?></p>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/benefit-grid',
		'attrs'     => array(
			'eyebrow' => __( 'Afya Bora', 'nia-theme' ),
			'heading' => __( '92 Minerals, One Ritual.', 'nia-theme' ),
			'intro'   => __( "The ocean's most nutrient-dense superfood, crafted for your peak performance.", 'nia-theme' ),
			'items'   => array(
				array(
					'icon'  => 'energy_savings_leaf',
					'title' => __( 'Sustained Vitality', 'nia-theme' ),
					'body'  => __( 'Boost your energy levels naturally without the crash of caffeine. Seamoss supports metabolic function for all-day focus.', 'nia-theme' ),
				),
				array(
					'icon'  => 'ecg_heart',
					'title' => __( 'Natural Healing', 'nia-theme' ),
					'body'  => __( 'High in anti-inflammatory properties and potassium chloride, it supports your respiratory and immune health year-round.', 'nia-theme' ),
				),
				array(
					'icon'  => 'diamond',
					'title' => __( 'Mineral Rich', 'nia-theme' ),
					'body'  => __( 'Containing 92 of the 102 minerals your body needs, including iodine, calcium, and magnesium for optimal well-being.', 'nia-theme' ),
				),
			),
		),
	)
);
?>

<!-- Daily Ritual -->
<section class="py-section-gap px-margin-mobile md:px-margin-desktop bg-off-white">
	<div class="max-w-container-max mx-auto flex flex-col md:flex-row items-center gap-16">
		<div class="w-full md:w-1/2 relative">
			<img
				class="w-full h-[500px] object-cover rounded-sm"
				src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/homepage-daily-ritual.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Morning wellness ritual with seamoss gel and smoothie', 'nia-theme' ); ?>"
			/>
			<div class="absolute -bottom-8 -right-8 w-48 h-48 bg-tertiary-fixed -z-10"></div>
		</div>
		<div class="w-full md:w-1/2">
			<span class="font-label-lg text-primary uppercase tracking-[0.2em] mb-4 block"><?php esc_html_e( 'Habitual Beauty', 'nia-theme' ); ?></span>
			<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-8"><?php esc_html_e( 'The Morning Ritual.', 'nia-theme' ); ?></h2>
			<p class="font-body-lg text-on-surface-variant mb-6">
				<?php esc_html_e( "Integrating Nia Nutrition into your day is a moment of self-connection. Whether it's a spoon of gel in your herbal tea or powder in your morning smoothie, it's the simplest way to honor your body.", 'nia-theme' ); ?>
			</p>
			<ul class="space-y-4 mb-10">
				<?php
				$nia_ritual_points = array(
					__( 'Seamlessly dissolves in hot or cold liquids', 'nia-theme' ),
					__( 'Neutral taste profile for any culinary creation', 'nia-theme' ),
					__( 'Noticeable results within 14 days of consistent use', 'nia-theme' ),
				);
				foreach ( $nia_ritual_points as $nia_point ) :
					?>
					<li class="flex items-center gap-4 text-on-surface">
						<span class="material-symbols-outlined text-primary">check_circle</span>
						<span class="font-body-md"><?php echo esc_html( $nia_point ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="btn-primary" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'Start Your Ritual', 'nia-theme' ); ?></a>
		</div>
	</div>
</section>

<?php
echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/newsletter-signup',
		'attrs'     => array(
			'eyebrow'    => '',
			'heading'    => __( 'Join the Inner Circle', 'nia-theme' ),
			'body'       => __( 'Receive expert wellness tips, traditional recipes, and exclusive early access to our limited batches.', 'nia-theme' ),
			'buttonText' => __( 'Subscribe', 'nia-theme' ),
			'microcopy'  => '',
			'variant'    => 'primary',
		),
	)
);

get_footer();