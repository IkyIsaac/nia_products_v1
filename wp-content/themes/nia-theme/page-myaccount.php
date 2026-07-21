<?php
/**
 * Template Name: Nia My Account
 *
 * Wrapper for the [woocommerce_my_account] shortcode — a wider, unpadded
 * container than page.php's max-w-3xl default, needed for dashboard.html's
 * fixed sidebar + wide content layout (PROJECT_PLAN.md Phase 6). The actual
 * sidebar/content markup lives in woocommerce/myaccount/my-account.php and
 * woocommerce/myaccount/navigation.php (theme overrides).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="pt-32 min-h-screen">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php get_footer(); ?>
