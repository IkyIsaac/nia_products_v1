<?php
/**
 * Server-rendered markup for the nia/hero block.
 *
 * @package Nia_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$eyebrow        = $attributes['eyebrow'] ?? '';
$heading        = $attributes['heading'] ?? '';
$body           = $attributes['body'] ?? '';
$image_url      = $attributes['imageUrl'] ?? '';
$image_alt      = $attributes['imageAlt'] ?? '';
$primary_text   = $attributes['primaryText'] ?? '';
$primary_url    = $attributes['primaryUrl'] ?? '#';
$secondary_text = $attributes['secondaryText'] ?? '';
$secondary_url  = $attributes['secondaryUrl'] ?? '#';
$height_class   = ( 'auto' === ( $attributes['minHeight'] ?? 'screen' ) ) ? '' : 'h-screen min-h-[700px]';

$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => trim( 'relative w-full flex items-center overflow-hidden ' . $height_class ) )
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<?php if ( $image_url ) : ?>
		<img class="absolute inset-0 w-full h-full object-cover" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
		<div class="absolute inset-0 bg-gradient-to-r from-on-background/40 to-transparent"></div>
	<?php endif; ?>

	<div class="relative w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
		<div class="max-w-2xl text-off-white">
			<?php if ( $eyebrow ) : ?>
				<span class="font-label-lg text-label-lg uppercase tracking-[0.2em] mb-4 block"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-6 leading-tight"><?php echo wp_kses_post( $heading ); ?></h1>

			<?php if ( $body ) : ?>
				<p class="font-body-lg text-body-lg mb-10 max-w-lg"><?php echo wp_kses_post( $body ); ?></p>
			<?php endif; ?>

			<div class="flex flex-col md:flex-row gap-gutter">
				<?php if ( $primary_text ) : ?>
					<a class="btn-primary" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_text ); ?></a>
				<?php endif; ?>
				<?php if ( $secondary_text ) : ?>
					<a class="btn-outline-dark" href="<?php echo esc_url( $secondary_url ); ?>"><?php echo esc_html( $secondary_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
