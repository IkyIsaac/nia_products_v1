<?php
/**
 * Server-rendered markup for the nia/cta-banner block.
 *
 * @package Nia_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$heading         = $attributes['heading'] ?? '';
$body            = $attributes['body'] ?? '';
$primary_text    = $attributes['primaryText'] ?? '';
$primary_url     = $attributes['primaryUrl'] ?? '#';
$secondary_text  = $attributes['secondaryText'] ?? '';
$secondary_url   = $attributes['secondaryUrl'] ?? '#';
$variant         = $attributes['variant'] ?? 'light';
$show_decorative = $attributes['showDecorative'] ?? true;

$variant_classes = array(
	'light'             => 'bg-transparent text-on-background',
	'inverse'           => 'bg-inverse-surface text-off-white',
	'primary-container' => 'bg-primary-container text-on-primary-container',
);
$bg_class        = $variant_classes[ $variant ] ?? $variant_classes['light'];

$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => 'py-section-gap relative overflow-hidden ' . $bg_class )
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop text-center relative z-10">
		<?php if ( $heading ) : ?>
			<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-8"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<p class="font-body-lg text-body-lg max-w-xl mx-auto mb-12 opacity-80"><?php echo wp_kses_post( $body ); ?></p>
		<?php endif; ?>
		<div class="flex flex-col md:flex-row gap-gutter justify-center items-center">
			<?php if ( $primary_text ) : ?>
				<a class="btn-primary-filled sunlight-shadow hover:bg-inverse-surface hover:text-off-white" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_text ); ?></a>
			<?php endif; ?>
			<?php if ( $secondary_text ) : ?>
				<a class="btn-outline-light" href="<?php echo esc_url( $secondary_url ); ?>"><?php echo esc_html( $secondary_text ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $show_decorative ) : ?>
		<div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-primary-fixed-dim/20 rounded-full blur-3xl"></div>
		<div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-tertiary-fixed/20 rounded-full blur-3xl"></div>
	<?php endif; ?>
</section>
