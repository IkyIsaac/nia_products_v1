<?php
/**
 * Single Wellness Journal article template. No dedicated single-post
 * mockup exists in nia-products/ (journal.html is the index only) — this
 * layout is a reasonable extrapolation from the same editorial tokens
 * (hero image, eyebrow, prose, related articles), matching PAGE_STATUS.md's
 * "journal.html (article layout)" note.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$nia_cats      = get_the_category();
	$nia_read_time = get_post_meta( get_the_ID(), '_nia_read_time', true );
	?>

	<article class="pt-[100px]">

		<header class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-16 text-center">
			<?php if ( ! empty( $nia_cats ) ) : ?>
				<span class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-4 block"><?php echo esc_html( $nia_cats[0]->name ); ?></span>
			<?php endif; ?>
			<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-background max-w-3xl mx-auto mb-6"><?php the_title(); ?></h1>
			<?php if ( $nia_read_time ) : ?>
				<span class="font-label-md text-label-md text-on-surface-variant uppercase"><?php echo esc_html( $nia_read_time ); ?></span>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mb-section-gap">
				<div class="aspect-[16/9] overflow-hidden sunlight-shadow">
					<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop pb-section-gap">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>

	</article>

	<?php
	$nia_related = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	if ( $nia_related->have_posts() ) :
		?>
		<section class="px-margin-mobile md:px-margin-desktop py-section-gap max-w-container-max mx-auto border-t border-warm-grey">
			<h2 class="font-headline-lg text-headline-lg text-on-background mb-16"><?php esc_html_e( 'More from the Journal', 'nia-theme' ); ?></h2>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-20 gap-x-12">
				<?php
				while ( $nia_related->have_posts() ) :
					$nia_related->the_post();
					?>
					<article class="group cursor-pointer">
						<a href="<?php the_permalink(); ?>" class="card-journal block mb-8">
							<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ) ); ?>
						</a>
						<div class="px-2">
							<h3 class="font-headline-md text-headline-md text-on-background group-hover:text-primary transition-colors leading-snug">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
						</div>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</section>
	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>