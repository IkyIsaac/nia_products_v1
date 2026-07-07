<?php
/**
 * Template Name: Nia Style Guide (dev only)
 *
 * Dev-only component reference (PROJECT_PLAN.md Phase 3 exit criteria) —
 * demonstrates every button variant, card variant, and custom block against
 * real tokens. Not linked from any nav, marked noindex (functions.php).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">

	<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-4"><?php esc_html_e( 'Nia Style Guide', 'nia-theme' ); ?></h1>
	<p class="font-body-md text-on-surface-variant mb-16"><?php esc_html_e( 'Dev-only component reference. Not part of the public site.', 'nia-theme' ); ?></p>

	<!-- Buttons -->
	<section class="mb-section-gap">
		<h2 class="font-headline-md text-headline-md mb-8"><?php esc_html_e( 'Buttons', 'nia-theme' ); ?></h2>
		<div class="flex flex-wrap items-center gap-6">
			<a class="btn-primary" href="#">Primary</a>
			<a class="btn-outline-light" href="#">Outline (light bg)</a>
			<div class="bg-inverse-surface p-4"><a class="btn-outline-dark" href="#">Outline (dark bg)</a></div>
			<a class="btn-inverse-surface" href="#">Inverse-surface</a>
			<a class="btn-primary-filled" href="#">Primary-filled</a>
			<a class="btn-link" href="#">Text / underline link</a>
			<a class="btn-icon-circle" href="#"><span class="material-symbols-outlined">search</span></a>
			<label class="relative">
				<input type="radio" name="style-guide-radio" class="sr-only peer" />
				<span class="btn-radio-card peer-checked:border-primary">Payment-method radio card</span>
			</label>
		</div>
	</section>

	<!-- Badges -->
	<section class="mb-section-gap">
		<h2 class="font-headline-md text-headline-md mb-8"><?php esc_html_e( 'Badges', 'nia-theme' ); ?></h2>
		<div class="flex flex-wrap items-center gap-6">
			<span class="badge-subscribe">Subscribe &amp; Save 10%</span>
			<span class="badge-recommended">Recommended</span>
		</div>
	</section>

	<!-- Cards -->
	<section class="mb-section-gap">
		<h2 class="font-headline-md text-headline-md mb-8"><?php esc_html_e( 'Cards', 'nia-theme' ); ?></h2>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">

			<div>
				<p class="font-label-md text-outline mb-2">Product card</p>
				<div class="card-product group">
					<div class="card-product-image bg-warm-grey"></div>
					<span class="badge-subscribe absolute top-4 left-4">Subscribe &amp; Save</span>
				</div>
				<div class="mt-4">
					<span class="font-label-md text-primary uppercase">Seamoss Gel</span>
					<h3 class="font-headline-sm text-headline-sm">Golden Sea Moss Gel</h3>
					<p class="font-label-lg font-bold">45,000 TZS</p>
				</div>
			</div>

			<div>
				<p class="font-label-md text-outline mb-2">Journal card</p>
				<div class="card-journal">
					<div class="w-full h-full bg-warm-grey"></div>
				</div>
			</div>

			<div>
				<p class="font-label-md text-outline mb-2">App card (checkout/dashboard)</p>
				<div class="card-app p-8">
					<p class="font-label-lg">Order #1024</p>
					<p class="font-body-md text-on-surface-variant">Delivered</p>
				</div>
			</div>

			<div>
				<p class="font-label-md text-outline mb-2">Subscription tier card</p>
				<div class="card-subscription-tier">
					<h3 class="font-headline-sm text-headline-sm mb-2">The Enthusiast</h3>
					<p class="font-body-md text-on-surface-variant">45,000 TZS/delivery</p>
				</div>
			</div>

			<div>
				<p class="font-label-md text-outline mb-2">Subscription tier — Most Popular</p>
				<div class="card-subscription-tier card-subscription-tier--popular">
					<h3 class="font-headline-sm text-headline-sm mb-2">The Balanced</h3>
					<p class="font-body-md">82,000 TZS/delivery</p>
				</div>
			</div>

		</div>
	</section>

	<!-- Forms -->
	<section class="mb-section-gap">
		<h2 class="font-headline-md text-headline-md mb-8"><?php esc_html_e( 'Forms', 'nia-theme' ); ?></h2>
		<div class="max-w-md flex flex-col gap-6">
			<div class="flex flex-col gap-2">
				<label class="font-label-md text-outline">EMAIL</label>
				<input class="editorial-input py-2" type="email" placeholder="you@example.com" />
			</div>
			<div class="flex flex-col gap-2">
				<label class="font-label-md text-outline">INVALID EXAMPLE</label>
				<input class="editorial-input input-error py-2" type="email" value="not-an-email" />
				<span class="input-helper-error">Enter a valid email address.</span>
			</div>
		</div>
	</section>

	<!-- Custom Gutenberg blocks (rendered from this page's content — see the block editor) -->
	<section class="mb-section-gap">
		<h2 class="font-headline-md text-headline-md mb-8"><?php esc_html_e( 'Blocks', 'nia-theme' ); ?></h2>
	</section>

	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>

</main>

<?php
get_footer();
