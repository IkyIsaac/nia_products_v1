<?php
/**
 * Template Name: Nia Checkout
 *
 * Wrapper for the WooCommerce Checkout block — a wider container matching
 * the site's max-w-container-max convention (Home/PDP/Contact), instead of
 * page.php's max-w-3xl default. At max-w-3xl the Checkout block's two-
 * column fields+summary layout had no room and content was visibly cut
 * off (PROJECT_PLAN.md Phase 6 gap — no dedicated template existed for
 * this page before, so it fell back to the generic page.php).
 *
 * pt-56 (not the sitewide pt-32 convention, 2026-07-23): the fixed
 * header's logo wraps to two lines at common desktop widths, rendering
 * ~189px tall — taller than pt-32's 128px reserves. The H1 below was
 * rendering directly underneath the fixed header, fully hidden (not a
 * spacing nitpick — the page title was completely invisible). Found while
 * fixing the identical bug on page-cart.php, same template shape.
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
