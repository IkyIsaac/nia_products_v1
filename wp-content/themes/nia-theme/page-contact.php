<?php
/**
 * Template Name: Nia Contact
 *
 * Transcribed from nia-products/contact.html. The form itself is Fluent
 * Forms (form #5, "Contact Page Inquiry") rather than dead markup — see
 * CUSTOMIZATIONS.md. All photography is AI-generated placeholder imagery
 * pending real photos (RISKS.md R6).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="pt-32">

	<!-- Hero -->
	<section class="px-margin-mobile md:px-margin-desktop mb-section-gap">
		<div class="max-w-container-max mx-auto">
			<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter items-center">
				<div class="flex flex-col gap-6">
					<span class="font-label-lg text-label-lg text-primary tracking-widest"><?php esc_html_e( 'TUKO HAPA KWA AJILI YAKO', 'nia-theme' ); ?></span>
					<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-background max-w-xl"><?php esc_html_e( 'We’re Here for Your Wellness Journey.', 'nia-theme' ); ?></h1>
					<p class="font-body-lg text-body-lg text-on-surface-variant max-w-md">
						<?php esc_html_e( "Whether you're starting a new regimen or looking to optimize your health, our specialists in Dar es Salaam are ready to guide you.", 'nia-theme' ); ?>
					</p>
				</div>
				<div class="relative h-[400px] md:h-[600px] overflow-hidden rounded-lg sunlight-shadow">
					<img
						class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700"
						src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/contact-hero.jpg' ); ?>"
						alt="<?php esc_attr_e( 'Serene minimalist wellness clinic interior in Dar es Salaam', 'nia-theme' ); ?>"
					/>
				</div>
			</div>
		</div>
	</section>

	<!-- Contact & Inquiry Form -->
	<section class="px-margin-mobile md:px-margin-desktop mb-section-gap bg-surface py-24">
		<div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-12 gap-gutter">

			<div class="md:col-span-5 flex flex-col gap-12">
				<div class="flex flex-col gap-4">
					<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background"><?php esc_html_e( 'Inquiry', 'nia-theme' ); ?></h2>
					<p class="font-body-md text-body-md text-on-surface-variant"><?php esc_html_e( 'Request a private wellness consultation or inquire about our premium subscription plans.', 'nia-theme' ); ?></p>
				</div>
				<div class="grid grid-cols-1 gap-8">
					<div class="flex flex-col gap-2">
						<span class="font-label-lg text-label-lg text-primary"><?php esc_html_e( 'DAR ES SALAAM', 'nia-theme' ); ?></span>
						<p class="font-body-md text-body-md text-on-surface"><?php esc_html_e( 'Masasani Peninsula, Plot 142', 'nia-theme' ); ?><br /><?php esc_html_e( 'Tanzania', 'nia-theme' ); ?></p>
					</div>
					<div class="flex flex-col gap-2">
						<span class="font-label-lg text-label-lg text-primary"><?php esc_html_e( 'EMAIL', 'nia-theme' ); ?></span>
						<p class="font-body-md text-body-md text-on-surface">hello@nianutrition.co.tz</p>
					</div>
					<div class="flex flex-col gap-2">
						<span class="font-label-lg text-label-lg text-primary"><?php esc_html_e( 'WHATSAPP VITALITY LINE', 'nia-theme' ); ?></span>
						<a class="font-body-md text-body-md text-on-surface flex items-center gap-2 hover:text-primary transition-colors" href="#">
							+255 700 000 000
							<span class="material-symbols-outlined text-[16px]">north_east</span>
						</a>
					</div>
				</div>
			</div>

			<div class="md:col-span-7 bg-off-white p-8 md:p-16 sunlight-shadow rounded-lg">
				<?php echo do_shortcode( '[fluentform id="5"]' ); ?>
			</div>

		</div>
	</section>

	<!-- Location -->
	<section class="px-margin-mobile md:px-margin-desktop mb-section-gap">
		<div class="max-w-container-max mx-auto">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter h-auto md:h-[500px]">
				<div class="md:col-span-2 relative rounded-lg overflow-hidden group">
					<div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-all duration-500"></div>
					<div class="absolute bottom-8 left-8 z-10 text-off-white">
						<h3 class="font-headline-md text-headline-md"><?php esc_html_e( 'Visit Our Atelier', 'nia-theme' ); ?></h3>
						<p class="font-body-md text-body-md opacity-80"><?php esc_html_e( 'Experience Vitality in Person', 'nia-theme' ); ?></p>
					</div>
					<img
						class="w-full h-full object-cover"
						src="<?php echo esc_url( NIA_THEME_URI . '/assets/images/placeholders/contact-atelier.jpg' ); ?>"
						alt="<?php esc_attr_e( 'Nia Nutrition retail atelier in Masaki, Dar es Salaam', 'nia-theme' ); ?>"
					/>
				</div>
				<div class="bg-tertiary-fixed p-12 rounded-lg flex flex-col justify-between">
					<div class="flex flex-col gap-4">
						<span class="material-symbols-outlined text-primary text-4xl">location_on</span>
						<h4 class="font-headline-md text-headline-md text-on-tertiary-fixed"><?php esc_html_e( 'Dar es Salaam Studio', 'nia-theme' ); ?></h4>
						<p class="font-body-md text-body-md text-on-tertiary-fixed-variant"><?php esc_html_e( 'Open Mon - Sat', 'nia-theme' ); ?><br /><?php esc_html_e( '09:00 - 18:00 EAT', 'nia-theme' ); ?></p>
					</div>
					<div class="flex flex-col gap-2">
						<a class="font-label-lg text-label-lg text-on-tertiary-fixed flex items-center gap-2 group" href="#">
							<?php esc_html_e( 'OPEN IN MAPS', 'nia-theme' ); ?>
							<span class="material-symbols-outlined transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>