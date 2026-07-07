<?php
/**
 * Server-rendered markup for the nia/newsletter-signup block.
 * UI only (attributes.php) — actual submission wiring is a later phase.
 *
 * @package Nia_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$eyebrow     = $attributes['eyebrow'] ?? '';
$heading     = $attributes['heading'] ?? '';
$body        = $attributes['body'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$microcopy   = $attributes['microcopy'] ?? '';
$variant     = $attributes['variant'] ?? 'dark';
$is_dark     = 'dark' === $variant;

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'py-section-gap px-margin-mobile md:px-margin-desktop ' . ( $is_dark ? 'bg-inverse-surface' : 'bg-surface-container-low' ),
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="max-w-container-max mx-auto text-center">
		<div class="max-w-3xl mx-auto">
			<?php if ( $eyebrow ) : ?>
				<span class="font-label-lg uppercase tracking-widest block mb-6 <?php echo $is_dark ? 'text-primary-fixed' : 'text-primary'; ?>"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-8 <?php echo $is_dark ? 'text-off-white' : 'text-on-background'; ?>"><?php echo wp_kses_post( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<p class="font-body-lg text-body-lg mb-12 <?php echo $is_dark ? 'text-surface-variant/80' : 'text-on-surface-variant'; ?>"><?php echo wp_kses_post( $body ); ?></p>
			<?php endif; ?>

			<form class="flex flex-col md:flex-row gap-4 items-stretch justify-center" method="post" action="#">
				<input
					class="bg-transparent border-b px-6 py-4 outline-none font-body-md w-full md:w-[400px] <?php echo $is_dark ? 'border-warm-grey/30 text-off-white focus:border-primary-fixed' : 'border-warm-grey text-on-background focus:border-on-background'; ?>"
					type="email"
					name="nia_newsletter_email"
					placeholder="<?php esc_attr_e( 'Your email address', 'nia-core' ); ?>"
				/>
				<button
					class="font-label-lg uppercase tracking-widest px-12 py-4 transition-all duration-300 <?php echo $is_dark ? 'bg-primary-fixed text-on-primary-fixed hover:bg-off-white' : 'bg-on-background text-off-white hover:bg-primary'; ?>"
					type="submit"
				>
					<?php echo esc_html( $button_text ); ?>
				</button>
			</form>

			<?php if ( $microcopy ) : ?>
				<p class="font-label-md mt-6 italic <?php echo $is_dark ? 'text-surface-variant/40' : 'text-on-surface-variant opacity-60'; ?>"><?php echo esc_html( $microcopy ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
