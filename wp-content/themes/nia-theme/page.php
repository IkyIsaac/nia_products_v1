<?php
/**
 * Base page template. Individual static pages (Phase 4) get their own
 * dedicated templates layered on top of this where the design diverges.
 * Currently backs the 4 policy pages (Privacy/Terms/Shipping/Refund) —
 * everything else got a dedicated hero-section template with enough
 * natural spacing to avoid the bug fixed below.
 *
 * pt-56 (not py-section-gap on both sides, 2026-07-23): the fixed header's
 * logo wraps to two lines at common desktop widths, rendering ~189px tall.
 * py-section-gap is 120px — even less than the pt-32 (128px) other
 * templates used before this same bug was found and fixed there (Cart,
 * Checkout). The H1 title was rendering completely invisible, painted
 * over by the fixed header, on all 4 policy pages.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop pt-56 pb-section-gap">
	<?php
	while ( have_posts() ) {
		the_post();
		the_title( '<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-10">', '</h1>' );
		echo '<div class="entry-content">';
		the_content();
		echo '</div>';
	}
	?>
</main>

<?php
get_footer();
