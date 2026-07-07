<?php
/**
 * Template Name: Nia Wellness Journal
 *
 * Transcribed from nia-products/journal.html — the Wellness Journal index.
 * The most recent post is the featured article; the next 3 form the grid.
 * Category bar links to real WP category archives (Recipes/Science/
 * Lifestyle/Community/Tradition), which use category.php for styling.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$nia_featured_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$nia_featured_id = $nia_featured_query->have_posts() ? $nia_featured_query->posts[0]->ID : 0;

$nia_grid_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => array( $nia_featured_id ),
	)
);
?>

<main class="pt-[100px]">

	<!-- Page Header -->
	<section class="px-margin-mobile md:px-margin-desktop py-12 md:py-20 text-center max-w-container-max mx-auto">
		<span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em] block mb-4"><?php esc_html_e( 'Wellness Journal', 'nia-theme' ); ?></span>
		<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-background"><?php esc_html_e( 'The Journal: Afya Bora.', 'nia-theme' ); ?></h1>
		<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto mt-6">
			<?php esc_html_e( 'Discover the intersection of ancient African wisdom and modern nutritional science. A curated space for your vitality journey.', 'nia-theme' ); ?>
		</p>
	</section>

	<!-- Category Bar -->
	<section class="px-margin-mobile md:px-margin-desktop mb-12 overflow-x-auto">
		<div class="flex justify-center items-center gap-8 md:gap-12 min-w-max border-y border-warm-grey py-6 max-w-container-max mx-auto">
			<a class="font-label-lg text-label-lg text-primary hover:text-on-background transition-colors" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'All Stories', 'nia-theme' ); ?></a>
			<?php
			$nia_journal_cats = array( 'recipes', 'science', 'lifestyle', 'community' );
			foreach ( $nia_journal_cats as $nia_cat_slug ) :
				$nia_term = get_term_by( 'slug', $nia_cat_slug, 'category' );
				if ( ! $nia_term ) {
					continue;
				}
				?>
				<a class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors" href="<?php echo esc_url( get_category_link( $nia_term ) ); ?>"><?php echo esc_html( $nia_term->name ); ?></a>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $nia_featured_id ) : ?>
		<!-- Featured Article -->
		<section class="px-margin-mobile md:px-margin-desktop mb-section-gap max-w-container-max mx-auto">
			<article class="group relative grid grid-cols-1 lg:grid-cols-12 gap-gutter bg-surface-container-low overflow-hidden rounded-lg">
				<a href="<?php echo esc_url( get_permalink( $nia_featured_id ) ); ?>" class="lg:col-span-7 overflow-hidden h-[400px] lg:h-[600px] block">
					<?php echo get_the_post_thumbnail( $nia_featured_id, 'large', array( 'class' => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105' ) ); ?>
				</a>
				<div class="lg:col-span-5 flex flex-col justify-center p-8 md:p-16">
					<span class="font-label-lg text-label-lg text-primary uppercase tracking-widest mb-6 block"><?php esc_html_e( 'Featured', 'nia-theme' ); ?></span>
					<h2 class="font-display-lg text-display-lg-mobile md:text-[48px] leading-tight text-on-background mb-8 group-hover:text-primary transition-colors">
						<a href="<?php echo esc_url( get_permalink( $nia_featured_id ) ); ?>"><?php echo esc_html( get_the_title( $nia_featured_id ) ); ?></a>
					</h2>
					<p class="font-body-lg text-body-lg text-on-surface-variant mb-10"><?php echo esc_html( wp_trim_words( get_the_excerpt( $nia_featured_id ), 24 ) ); ?></p>
					<a class="inline-flex items-center group/btn" href="<?php echo esc_url( get_permalink( $nia_featured_id ) ); ?>">
						<span class="bg-on-background text-off-white px-8 py-4 font-label-lg text-label-lg uppercase tracking-widest group-hover/btn:bg-primary transition-all duration-300"><?php esc_html_e( 'Read Article', 'nia-theme' ); ?></span>
						<span class="bg-primary px-4 py-4 text-off-white group-hover/btn:bg-on-background transition-all duration-300">
							<span class="material-symbols-outlined">arrow_forward</span>
						</span>
					</a>
				</div>
			</article>
		</section>
	<?php endif; ?>

	<!-- Article Grid -->
	<section class="px-margin-mobile md:px-margin-desktop py-section-gap max-w-container-max mx-auto border-t border-warm-grey">
		<div class="flex flex-col md:flex-row justify-between items-end mb-16">
			<div>
				<h2 class="font-headline-lg text-headline-lg text-on-background"><?php esc_html_e( 'Latest Explorations', 'nia-theme' ); ?></h2>
				<p class="font-body-lg text-body-lg text-on-surface-variant mt-2"><?php esc_html_e( 'Nurturing your body through knowledge and tradition.', 'nia-theme' ); ?></p>
			</div>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-20 gap-x-12">
			<?php
			while ( $nia_grid_query->have_posts() ) :
				$nia_grid_query->the_post();
				?>
				<article class="group cursor-pointer">
					<a href="<?php the_permalink(); ?>" class="card-journal block mb-8">
						<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ) ); ?>
						<?php
						$nia_cats = get_the_category();
						if ( ! empty( $nia_cats ) ) :
							?>
							<div class="absolute top-6 left-6">
								<span class="bg-off-white/90 px-4 py-2 font-label-md text-label-md uppercase tracking-wider text-on-background backdrop-blur-md"><?php echo esc_html( $nia_cats[0]->name ); ?></span>
							</div>
						<?php endif; ?>
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
			<?php wp_reset_postdata(); ?>
		</div>
	</section>

</main>

<?php
echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block() output comes from our own escaped render.php templates.
	array(
		'blockName' => 'nia/newsletter-signup',
		'attrs'     => array(
			'eyebrow'    => __( 'The Vitality List', 'nia-theme' ),
			'heading'    => __( 'Weekly insight into the art of living well.', 'nia-theme' ),
			'body'       => __( 'Join 10,000+ others receiving curated nutritional science and luxury wellness inspiration directly to their inbox.', 'nia-theme' ),
			'buttonText' => __( 'Subscribe', 'nia-theme' ),
			'microcopy'  => __( 'Respecting your privacy. Unsubscribe at any time.', 'nia-theme' ),
			'variant'    => 'dark',
		),
	)
);

get_footer();