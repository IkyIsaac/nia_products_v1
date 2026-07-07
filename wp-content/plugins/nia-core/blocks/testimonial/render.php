<?php
/**
 * Server-rendered markup for the nia/testimonial block.
 *
 * @package Nia_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$heading = $attributes['heading'] ?? '';
$items   = is_array( $attributes['items'] ?? null ) ? $attributes['items'] : array();

$grid_cols = count( $items ) > 1 ? 'md:grid-cols-2' : 'md:grid-cols-1';

$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => 'py-section-gap px-margin-mobile md:px-margin-desktop' )
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="max-w-container-max mx-auto">
		<?php if ( $heading ) : ?>
			<h2 class="font-display-lg text-headline-lg text-center mb-16"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<div class="grid grid-cols-1 <?php echo esc_attr( $grid_cols ); ?> gap-gutter">
			<?php foreach ( $items as $item ) : ?>
				<div class="card-testimonial">
					<div>
						<div class="flex gap-1 text-primary mb-6">
							<?php for ( $i = 0; $i < (int) ( $item['rating'] ?? 5 ); $i++ ) : ?>
								<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
							<?php endfor; ?>
						</div>
						<?php if ( ! empty( $item['quote'] ) ) : ?>
							<p class="font-display-lg text-headline-md italic mb-8">&ldquo;<?php echo esc_html( $item['quote'] ); ?>&rdquo;</p>
						<?php endif; ?>
					</div>
					<div class="flex items-center gap-4">
						<?php if ( ! empty( $item['avatarUrl'] ) ) : ?>
							<div class="w-16 h-16 rounded-full overflow-hidden bg-warm-grey">
								<img class="w-full h-full object-cover" src="<?php echo esc_url( $item['avatarUrl'] ); ?>" alt="<?php echo esc_attr( $item['name'] ?? '' ); ?>" />
							</div>
						<?php endif; ?>
						<div>
							<?php if ( ! empty( $item['name'] ) ) : ?>
								<p class="font-label-lg text-label-lg"><?php echo esc_html( $item['name'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $item['role'] ) ) : ?>
								<p class="font-body-md text-xs text-outline"><?php echo esc_html( $item['role'] ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
