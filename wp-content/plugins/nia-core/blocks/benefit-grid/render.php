<?php
/**
 * Server-rendered markup for the nia/benefit-grid block.
 *
 * @package Nia_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$eyebrow = $attributes['eyebrow'] ?? '';
$heading = $attributes['heading'] ?? '';
$intro   = $attributes['intro'] ?? '';
$items   = is_array( $attributes['items'] ?? null ) ? $attributes['items'] : array();

$grid_cols = 3 === count( $items ) ? 'md:grid-cols-3' : ( 2 === count( $items ) ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-4' );

$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => 'py-section-gap px-margin-mobile md:px-margin-desktop' )
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="max-w-container-max mx-auto">
		<?php if ( $eyebrow || $heading || $intro ) : ?>
			<div class="text-center max-w-3xl mx-auto mb-20">
				<?php if ( $eyebrow ) : ?>
					<span class="font-label-lg text-primary uppercase tracking-[0.2em] mb-4 block"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-6"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="font-body-lg text-on-surface-variant"><?php echo wp_kses_post( $intro ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="grid grid-cols-1 <?php echo esc_attr( $grid_cols ); ?> gap-12">
			<?php foreach ( $items as $item ) : ?>
				<div class="flex flex-col items-center text-center p-8 bg-off-white sunlight-shadow transition-transform hover:-translate-y-2">
					<?php if ( ! empty( $item['icon'] ) ) : ?>
						<span class="material-symbols-outlined text-primary text-5xl mb-6"><?php echo esc_html( $item['icon'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h3 class="font-headline-md text-headline-md mb-4"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $item['body'] ) ) : ?>
						<p class="font-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $item['body'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
