<?php
/**
 * Template Name: Nia Our Heritage / About
 *
 * Transcribed from nia-products/about-nia.html — NOT about.html, which is
 * an abandoned rebrand concept excluded from the build (DESIGN_SYSTEM.md §0).
 * All photography is AI-generated placeholder imagery pending real
 * product/lifestyle photos (RISKS.md R6).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<!-- Hero -->
<header class="relative min-h-screen flex items-center pt-24 overflow-hidden">
	<div class="max-w-container-max mx-auto w-full px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
		<div class="md:col-span-6 z-10">
			<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-6">
				<?php esc_html_e( 'Rooted in Vitality.', 'nia-theme' ); ?><br />
				<span class="italic font-normal"><?php esc_html_e( 'Crafted in Tanzania.', 'nia-theme' ); ?></span>
			</h1>
			<p class="font-body-lg text-body-lg text-on-surface-variant max-w-md mb-8">
				<?php esc_html_e( 'Bridging the gap between ancient East African wisdom and modern bio-available nutrition for the global high-achiever.', 'nia-theme' ); ?>
			</p>
			<a class="btn-inverse-surface" href="<?php echo esc_url( home_url( '/subscription/' ) ); ?>"><?php esc_html_e( 'The Ritual', 'nia-theme' ); ?></a>
		</div>
		<div class="md:col-span-6 relative">
			<div class="aspect-[4/5] bg-warm-grey overflow-hidden rounded-lg sunlight-shadow">
				<img
					class="w-full h-full object-cover grayscale-[20%]"
					src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/about-hero.jpg' ); ?>"
					alt="<?php esc_attr_e( "Portrait of Nia Nutrition's founder in a sunlit architectural space", 'nia-theme' ); ?>"
				/>
			</div>
			<div class="absolute -bottom-6 -left-6 bg-primary-container p-8 rounded-full text-on-primary-container hidden md:block">
				<span class="font-label-lg text-label-lg block text-center uppercase tracking-tighter"><?php esc_html_e( 'Est.', 'nia-theme' ); ?><br />2024</span>
			</div>
		</div>
	</div>
</header>

<!-- Founder's Story -->
<section class="py-section-gap bg-surface">
	<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
		<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
			<div class="md:col-span-4 md:sticky md:top-32 h-fit">
				<span class="text-primary font-label-lg text-label-lg uppercase tracking-widest block mb-4"><?php esc_html_e( 'The Genesis', 'nia-theme' ); ?></span>
				<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-12"><?php esc_html_e( 'From the shores of Dar es Salaam', 'nia-theme' ); ?></h2>
			</div>
			<div class="md:col-start-6 md:col-span-6 space-y-8">
				<p class="font-body-lg text-body-lg leading-relaxed text-on-surface">
					<?php esc_html_e( 'Growing up in the vibrant bustle of Dar es Salaam, our founder watched the coastal rituals of the Swahili people—a life lived in harmony with the Indian Ocean and the fertile Tanzanian soil. It was here that the power of Seamoss was first witnessed, not as a trend, but as a multi-generational cornerstone of vitality.', 'nia-theme' ); ?>
				</p>
				<blockquote class="font-headline-md text-headline-md italic text-primary leading-snug py-4">
					&ldquo;<?php esc_html_e( 'We aren’t just selling nutrients; we are reclaiming a heritage of health that the modern world forgot.', 'nia-theme' ); ?>&rdquo;
				</blockquote>
				<p class="font-body-md text-body-md leading-relaxed text-on-surface-variant">
					<?php esc_html_e( 'NIA was born from a desire to translate these ancestral secrets into a premium experience for the modern professional. We spent three years perfecting our extraction processes, ensuring that the 92 essential minerals found in our Tanzanian Seamoss remain as bio-active as the day they were harvested. Today, we stand as a bridge between the botanical wealth of Africa and the precision of modern clinical wellness.', 'nia-theme' ); ?>
				</p>
				<div class="aspect-video bg-warm-grey mt-12 overflow-hidden sunlight-shadow">
					<img
						class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
						src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/about-seamoss-texture.jpg' ); ?>"
						alt="<?php esc_attr_e( 'Macro photography of raw Tanzanian golden seamoss', 'nia-theme' ); ?>"
					/>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Our Mission Bento -->
<section class="py-section-gap">
	<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
		<div class="text-center max-w-2xl mx-auto mb-20">
			<span class="text-primary font-label-lg text-label-lg uppercase tracking-widest block mb-4"><?php esc_html_e( 'Our Mission', 'nia-theme' ); ?></span>
			<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg"><?php esc_html_e( 'High-end nutrition for the modern African lifestyle.', 'nia-theme' ); ?></h2>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-gutter md:h-[600px]">
			<div class="md:col-span-2 md:row-span-2 bg-off-white p-12 flex flex-col justify-end sunlight-shadow group overflow-hidden relative min-h-[300px]">
				<img
					class="absolute inset-0 w-full h-full object-cover opacity-10 group-hover:scale-110 group-hover:opacity-20 transition-all duration-700"
					src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/about-wellness-lifestyle.jpg' ); ?>"
					alt="<?php esc_attr_e( 'Wellness lifestyle in a sunlit minimalist space', 'nia-theme' ); ?>"
				/>
				<div class="relative z-10">
					<h3 class="font-headline-md text-headline-md mb-4"><?php esc_html_e( 'Ethical Sourcing', 'nia-theme' ); ?></h3>
					<p class="font-body-md text-body-md text-on-surface-variant max-w-xs"><?php esc_html_e( 'Direct partnerships with coastal farmers in Zanzibar and Tanga to ensure fair-trade and the highest quality harvest.', 'nia-theme' ); ?></p>
				</div>
			</div>
			<div class="md:col-span-2 bg-primary text-off-white p-12 flex flex-col justify-center min-h-[200px]">
				<div class="flex gap-4 mb-6">
					<span class="bg-off-white/20 px-3 py-1 rounded-full text-label-md font-label-md"><?php esc_html_e( '100% Organic', 'nia-theme' ); ?></span>
					<span class="bg-off-white/20 px-3 py-1 rounded-full text-label-md font-label-md"><?php esc_html_e( 'Non-GMO', 'nia-theme' ); ?></span>
				</div>
				<h3 class="font-headline-md text-headline-md mb-2"><?php esc_html_e( 'Purity First', 'nia-theme' ); ?></h3>
				<p class="font-body-md text-body-md opacity-90"><?php esc_html_e( 'Every batch is third-party tested in labs to guarantee zero heavy metals or contaminants.', 'nia-theme' ); ?></p>
			</div>
			<div class="md:col-span-1 bg-surface-container p-12 flex flex-col items-center justify-center text-center min-h-[200px]">
				<span class="material-symbols-outlined text-4xl mb-4 text-primary">biotech</span>
				<h4 class="font-label-lg text-label-lg uppercase mb-2"><?php esc_html_e( 'Bio-available', 'nia-theme' ); ?></h4>
				<p class="font-label-md text-label-md text-on-surface-variant"><?php esc_html_e( 'Cellular Absorption', 'nia-theme' ); ?></p>
			</div>
			<div class="md:col-span-1 bg-warm-grey p-12 flex flex-col items-center justify-center text-center min-h-[200px]">
				<span class="material-symbols-outlined text-4xl mb-4 text-primary">eco</span>
				<h4 class="font-label-lg text-label-lg uppercase mb-2"><?php esc_html_e( 'Sustainable', 'nia-theme' ); ?></h4>
				<p class="font-label-md text-label-md text-on-surface-variant"><?php esc_html_e( 'Carbon Neutral Delivery', 'nia-theme' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Wellness Philosophy -->
<section class="py-section-gap bg-inverse-surface text-off-white overflow-hidden">
	<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
		<div class="flex flex-col md:flex-row gap-16 items-center">
			<div class="md:w-1/2">
				<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-8"><?php esc_html_e( 'The 92 Minerals', 'nia-theme' ); ?></h2>
				<p class="font-body-lg text-body-lg text-surface-variant mb-12">
					<?php esc_html_e( 'Ancient wisdom tells us the body is made of the same elements as the earth. Modern science agrees. Our Seamoss contains 92 of the 102 minerals the human body needs to thrive—magnesium, potassium, iodine, and sulfur—delivered in a matrix the body recognizes.', 'nia-theme' ); ?>
				</p>
				<div class="space-y-6">
					<?php
					$nia_philosophy_items = array(
						__( 'Ancestral Wisdom', 'nia-theme' ),
						__( 'Modern Science', 'nia-theme' ),
						__( 'Regenerative Future', 'nia-theme' ),
					);
					foreach ( $nia_philosophy_items as $nia_item ) :
						?>
						<div class="border-b border-outline-variant/30 pb-4 flex justify-between items-center group cursor-pointer hover:border-primary transition-colors">
							<span class="font-headline-md text-headline-md"><?php echo esc_html( $nia_item ); ?></span>
							<span class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="md:w-1/2 relative">
				<div class="w-[120%] h-[600px] bg-primary-fixed/10 absolute -right-20 -top-20 rounded-full blur-[100px]"></div>
				<div class="relative z-10 aspect-square rounded-full border border-outline-variant/20 flex items-center justify-center p-8">
					<div class="aspect-square w-full rounded-full overflow-hidden">
						<img
							class="w-full h-full object-cover grayscale brightness-75 hover:grayscale-0 hover:brightness-100 transition-all duration-700"
							src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/about-philosophy.jpg' ); ?>"
							alt="<?php esc_attr_e( 'Editorial product shot of Nia Nutrition supplement jar on raw seamoss', 'nia-theme' ); ?>"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/cta-banner',
		'attrs'     => array(
			'heading'       => __( 'Join the Ritual', 'nia-theme' ),
			'body'          => __( 'Experience the transformative power of Tanzanian vitality. Subscribe today for 15% off your first curation.', 'nia-theme' ),
			'primaryText'   => __( 'Shop the Collection', 'nia-theme' ),
			'primaryUrl'    => nia_theme_shop_url(),
			'secondaryText' => __( 'Consult a Specialist', 'nia-theme' ),
			'secondaryUrl'  => home_url( '/contact/' ),
			'variant'       => 'light',
		),
	)
);

get_footer();