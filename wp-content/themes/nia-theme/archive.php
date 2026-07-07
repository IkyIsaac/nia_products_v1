<?php
/**
 * Base archive template. The real Shop/Collection category archive
 * (ARCHITECTURE.md §9a) is a WooCommerce-specific
 * taxonomy-product_cat.php template built in Phase 5/6 — this file
 * covers non-commerce archives (e.g. the Wellness Journal index).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) {
			the_post();
			the_title( '<h2 class="font-headline-md text-headline-md"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
			the_excerpt();
		}
		the_posts_pagination();
	else :
		?>
		<p class="font-body-md text-body-md"><?php esc_html_e( 'Nothing found.', 'nia-theme' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
