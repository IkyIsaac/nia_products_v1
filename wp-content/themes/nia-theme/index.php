<?php
/**
 * Fallback template (required by WordPress). Page-specific templates
 * (Phase 4+) take precedence; this only needs to render safely.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_title( '<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg">', '</h1>' );
			the_content();
		}
	}
	?>
</main>

<?php
get_footer();
