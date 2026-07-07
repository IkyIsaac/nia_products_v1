<?php
/**
 * Base page template. Individual static pages (Phase 4) get their own
 * dedicated templates layered on top of this where the design diverges.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">
	<?php
	while ( have_posts() ) {
		the_post();
		the_title( '<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg">', '</h1>' );
		the_content();
	}
	?>
</main>

<?php
get_footer();
