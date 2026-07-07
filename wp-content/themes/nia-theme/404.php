<?php
/**
 * 404 template.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap text-center">
	<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg"><?php esc_html_e( 'Page not found', 'nia-theme' ); ?></h1>
	<p class="font-body-lg text-body-lg text-on-surface-variant">
		<?php esc_html_e( 'The page you are looking for does not exist.', 'nia-theme' ); ?>
	</p>
</main>

<?php
get_footer();
