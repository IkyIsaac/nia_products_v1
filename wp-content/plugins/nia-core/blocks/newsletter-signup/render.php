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

// Three recurring section treatments seen across the mockups (journal.html's
// dark inverse-surface, a lighter panel variant, and index.html's solid
// primary-color newsletter section).
$variants = array(
	'dark'    => array(
		'section'   => 'bg-inverse-surface',
		'eyebrow'   => 'text-primary-fixed',
		'heading'   => 'text-off-white',
		'body'      => 'text-surface-variant/80',
		'input'     => 'border-warm-grey/30 text-off-white focus:border-primary-fixed placeholder:text-off-white/60',
		'button'    => 'bg-primary-fixed text-on-primary-fixed hover:bg-off-white',
		'microcopy' => 'text-surface-variant/40',
	),
	'light'   => array(
		'section'   => 'bg-surface-container-low',
		'eyebrow'   => 'text-primary',
		'heading'   => 'text-on-background',
		'body'      => 'text-on-surface-variant',
		'input'     => 'border-warm-grey text-on-background focus:border-on-background',
		'button'    => 'bg-on-background text-off-white hover:bg-primary',
		'microcopy' => 'text-on-surface-variant opacity-60',
	),
	'primary' => array(
		'section'   => 'bg-primary text-off-white',
		'eyebrow'   => 'text-off-white',
		'heading'   => 'text-off-white',
		'body'      => 'text-off-white opacity-90',
		'input'     => 'border-off-white/40 text-off-white focus:border-off-white placeholder:text-off-white/60',
		'button'    => 'bg-off-white text-on-background hover:bg-on-background hover:text-off-white',
		'microcopy' => 'text-off-white opacity-70',
	),
);
$v        = $variants[ $variant ] ?? $variants['dark'];

$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => 'py-section-gap px-margin-mobile md:px-margin-desktop ' . $v['section'] )
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="max-w-container-max mx-auto text-center">
		<div class="max-w-3xl mx-auto">
			<?php if ( $eyebrow ) : ?>
				<span class="font-label-lg uppercase tracking-widest block mb-6 <?php echo esc_attr( $v['eyebrow'] ); ?>"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 class="font-display-lg text-display-lg-mobile md:text-display-lg mb-8 <?php echo esc_attr( $v['heading'] ); ?>"><?php echo wp_kses_post( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<p class="font-body-lg text-body-lg mb-12 <?php echo esc_attr( $v['body'] ); ?>"><?php echo wp_kses_post( $body ); ?></p>
			<?php endif; ?>

			<form class="flex flex-col md:flex-row gap-4 items-stretch justify-center" method="post" action="#">
				<input
					class="bg-transparent border-b px-6 py-4 outline-none font-body-md w-full md:w-[400px] <?php echo esc_attr( $v['input'] ); ?>"
					type="email"
					name="nia_newsletter_email"
					placeholder="<?php esc_attr_e( 'Your email address', 'nia-core' ); ?>"
				/>
				<button
					class="font-label-lg uppercase tracking-widest px-12 py-4 transition-all duration-300 <?php echo esc_attr( $v['button'] ); ?>"
					type="submit"
				>
					<?php echo esc_html( $button_text ); ?>
				</button>
			</form>

			<?php if ( $microcopy ) : ?>
				<p class="font-label-md mt-6 italic <?php echo esc_attr( $v['microcopy'] ); ?>"><?php echo esc_html( $microcopy ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
