<?php
/**
 * Base archive template — Wellness Journal category archives
 * (Recipes/Science/Lifestyle/Community/Tradition). The real Shop/Collection
 * category archive (ARCHITECTURE.md §9a) is a WooCommerce-specific
 * taxonomy-product_cat.php template built in Phase 5/6, not this file.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">
	<header class="text-center mb-16">
		<span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em] block mb-4"><?php esc_html_e( 'Wellness Journal', 'nia-theme' ); ?></span>
		<h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg"><?php the_archive_title(); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-20 gap-x-12">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article class="group cursor-pointer">
					<a href="<?php the_permalink(); ?>" class="card-journal block mb-8">
						<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ) ); ?>
					</a>
					<div class="px-2">
						<span class="font-label-md text-label-md text-primary uppercase mb-3 block"><?php echo esc_html( get_post_meta( get_the_ID(), '_nia_read_time', true ) ); ?></span>
						<h3 class="font-headline-md text-headline-md text-on-background group-hover:text-primary transition-colors leading-snug">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="font-body-md text-body-md text-on-surface-variant mt-4 line-clamp-3"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<div class="mt-section-gap">
			<?php the_posts_pagination(); ?>
		</div>
	else :
		?>
		<p class="font-body-md text-body-md text-center"><?php esc_html_e( 'Nothing found.', 'nia-theme' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
