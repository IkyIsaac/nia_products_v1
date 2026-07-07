<?php
/**
 * Minimal single-row footer — Checkout only (DESIGN_SYSTEM.md §8).
 * Deliberately stripped down (copyright + Privacy/Terms/Help only) to
 * reduce funnel exit points — not an oversight, keep this distinction.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
	<footer class="w-full px-margin-mobile md:px-margin-desktop py-base bg-surface-container-low border-t border-warm-grey">
		<div class="max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
			<span class="font-label-md text-on-surface-variant"><?php esc_html_e( '© 2024 NIA . CRAFTED IN TANZANIA.', 'nia-theme' ); ?></span>
			<div class="flex gap-8">
				<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'nia-theme' ); ?></a>
				<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'nia-theme' ); ?></a>
				<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Help Center', 'nia-theme' ); ?></a>
			</div>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
