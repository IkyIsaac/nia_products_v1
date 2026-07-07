<?php
/**
 * Site footer — full variant (DESIGN_SYSTEM.md §8). Checkout auto-detects
 * and includes the minimal single-row variant instead (footer-minimal.php),
 * the same way header.php auto-detects is_checkout() — WooCommerce's
 * Checkout page renders through this theme's page.php, so the swap has to
 * happen here rather than depending on every template calling get_footer()
 * with a name.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'is_checkout' ) && is_checkout() ) {
	require __DIR__ . '/footer-minimal.php';
	return;
}
?>
	<footer class="w-full px-margin-mobile md:px-margin-desktop py-section-gap max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-4 gap-gutter bg-surface-container-low border-t border-warm-grey">

		<div class="md:col-span-1">
			<a class="font-display-lg text-headline-lg text-primary mb-6 block" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php bloginfo( 'name' ); ?>
			</a>
			<p class="font-body-md text-on-surface-variant max-w-xs">
				<?php esc_html_e( 'Elevating African nutrition through science, ritual, and pure Tanzanian ingredients.', 'nia-theme' ); ?>
			</p>
		</div>

		<div class="flex flex-col gap-4">
			<h4 class="font-label-lg text-on-background uppercase tracking-widest mb-4"><?php esc_html_e( 'Discover', 'nia-theme' ); ?></h4>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="<?php echo esc_url( nia_theme_shop_url() ); ?>"><?php esc_html_e( 'Shop All', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="#"><?php esc_html_e( 'Wholesale', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="#"><?php esc_html_e( 'Bundles', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="#"><?php esc_html_e( 'The Science', 'nia-theme' ); ?></a>
		</div>

		<div class="flex flex-col gap-4">
			<h4 class="font-label-lg text-on-background uppercase tracking-widest mb-4"><?php esc_html_e( 'Support', 'nia-theme' ); ?></h4>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="<?php echo esc_url( home_url( '/shipping-policy/' ) ); ?>"><?php esc_html_e( 'Shipping', 'nia-theme' ); ?></a>
			<a class="font-label-md text-on-surface-variant hover:text-primary transition-all" href="#"><?php esc_html_e( 'Sustainability', 'nia-theme' ); ?></a>
		</div>

		<div class="flex flex-col gap-6">
			<h4 class="font-label-lg text-on-background uppercase tracking-widest mb-4"><?php esc_html_e( 'Newsletter', 'nia-theme' ); ?></h4>
			<form class="relative" method="post" action="#">
				<input class="editorial-input w-full py-3" type="email" name="nia_newsletter_email" placeholder="<?php esc_attr_e( 'Email address', 'nia-theme' ); ?>" />
				<button class="absolute right-0 top-1/2 -translate-y-1/2 material-symbols-outlined text-primary" type="submit" aria-label="<?php esc_attr_e( 'Subscribe', 'nia-theme' ); ?>">east</button>
			</form>
			<p class="font-label-md text-on-surface-variant opacity-60">
				<?php esc_html_e( '© 2024 NIA . CRAFTED IN TANZANIA.', 'nia-theme' ); ?>
			</p>
		</div>

	</footer>

<?php wp_footer(); ?>
</body>
</html>
