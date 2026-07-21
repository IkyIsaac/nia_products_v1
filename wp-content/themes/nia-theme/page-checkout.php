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
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="pt-32 min-h-screen px-margin-mobile md:px-margin-desktop pb-section-gap">
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
