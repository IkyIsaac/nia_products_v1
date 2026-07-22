<?php
/**
 * Template Name: Nia Cart
 *
 * Wrapper for the WooCommerce Cart block — a wider container matching the
 * site's max-w-container-max convention (Home/PDP/Checkout), instead of
 * page.php's max-w-3xl default. Same gap as Checkout had before it: no
 * dedicated template existed for this page, so it fell back to the generic
 * page.php and the Cart block's two-column items+totals layout had no
 * room, collapsing to a cramped single column with the totals/checkout
 * sidebar blending into the page background (no width to render as the
 * card it's styled as).
 *
 * pt-56 (not the sitewide pt-32 convention): the fixed header's logo wraps
 * to two lines at common desktop widths, rendering ~189px tall — taller
 * than pt-32's 128px reserves. The H1 below was rendering directly
 * underneath the fixed header, fully hidden (not a spacing nitpick — the
 * page title was completely invisible). page-checkout.php had the exact
 * same bug (same template shape); fixed there too.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="pt-56 min-h-screen px-margin-mobile md:px-margin-desktop pb-section-gap">
	<div class="max-w-container-max mx-auto">
		<?php
		while ( have_posts() ) :
			the_post();
			the_title( '<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-10">', '</h1>' );
			the_content();
		endwhile;
		?>
	</div>
</main>

<?php get_footer(); ?>
