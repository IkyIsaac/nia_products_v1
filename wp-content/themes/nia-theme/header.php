<?php
/**
 * Site header (DESIGN_SYSTEM.md §7). Two variants:
 * - Full: logo, desktop nav, currency/language switchers (UI only — wired to
 *   WPML in Phase 5), account/cart icons, mobile hamburger + overlay drawer.
 * - Minimal (Checkout only): logo/tagline + "Back to Shop", no nav/icons —
 *   intentional funnel design, not an oversight.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

$nia_is_checkout = function_exists( 'is_checkout' ) && is_checkout();
$nia_cart_count  = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-warm-ivory text-on-background font-body-md overflow-x-hidden' ); ?>>
<?php wp_body_open(); ?>

<?php if ( $nia_is_checkout ) : ?>

	<header class="fixed top-0 left-0 w-full z-50 bg-off-white/80 backdrop-blur-xl px-margin-mobile md:px-margin-desktop py-base flex items-center justify-between">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex flex-col">
			<span class="font-display-lg text-headline-md md:text-display-lg-mobile tracking-tighter text-on-background"><?php bloginfo( 'name' ); ?></span>
			<span class="font-label-md text-primary tracking-[0.2em] -mt-1"><?php esc_html_e( 'LISHE BORA', 'nia-theme' ); ?></span>
		</a>
		<a class="flex items-center gap-2 group" href="<?php echo esc_url( nia_theme_shop_url() ); ?>">
			<span class="material-symbols-outlined text-on-surface transition-transform group-hover:-translate-x-1">arrow_back</span>
			<span class="font-label-lg text-label-lg uppercase"><?php esc_html_e( 'Back to Shop', 'nia-theme' ); ?></span>
		</a>
	</header>

<?php else : ?>

	<header x-data="{ mobileOpen: false }" class="fixed top-0 left-0 w-full z-50 flex flex-col items-center justify-between px-margin-mobile md:px-margin-desktop py-base bg-off-white/80 backdrop-blur-xl">
		<div class="w-full max-w-container-max mx-auto flex items-center justify-between py-4">

			<div class="flex items-center gap-12">
				<a class="font-display-lg text-display-lg-mobile md:text-display-lg tracking-tighter text-on-background" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav class="hidden md:flex items-center gap-8">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'items_wrap'     => '%3$s',
								'walker'         => new Nia_Nav_Walker(),
							)
						);
						?>
					</nav>
				<?php else : ?>
					<nav class="hidden md:flex items-center gap-8">
						<?php nia_theme_primary_nav_fallback(); ?>
					</nav>
				<?php endif; ?>
			</div>

			<div class="flex items-center gap-6 text-on-surface">
				<button class="hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Search', 'nia-theme' ); ?>">
					<span class="material-symbols-outlined">search</span>
				</button>

				<!-- Currency switcher — UI only, wired to WPML Multicurrency in Phase 5 (ARCHITECTURE.md §11). -->
				<button class="hidden md:flex items-center gap-0.5 font-label-lg text-label-lg hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Currency', 'nia-theme' ); ?>">
					TZS
					<span class="material-symbols-outlined text-base">expand_more</span>
				</button>

				<!-- Language switcher — UI only, wired to WPML in Phase 5 (DESIGN_SYSTEM.md §10). -->
				<button class="hidden md:flex items-center gap-1 font-label-lg text-label-lg hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Language', 'nia-theme' ); ?>">
					<span class="text-primary"><?php esc_html_e( 'EN', 'nia-theme' ); ?></span>
					<span aria-hidden="true">/</span>
					<span><?php esc_html_e( 'SW', 'nia-theme' ); ?></span>
				</button>

				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ) ); ?>">
					<button class="hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Account', 'nia-theme' ); ?>">
						<span class="material-symbols-outlined">person</span>
					</button>
				</a>

				<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>">
					<button class="relative hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Cart', 'nia-theme' ); ?>">
						<span class="material-symbols-outlined">shopping_bag</span>
						<span class="absolute -top-1 -right-1 bg-primary text-off-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold"><?php echo (int) $nia_cart_count; ?></span>
					</button>
				</a>

				<button class="md:hidden hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'nia-theme' ); ?>" @click="mobileOpen = true">
					<span class="material-symbols-outlined">menu</span>
				</button>
			</div>
		</div>

		<!-- Mobile nav — hamburger + full-screen overlay drawer (DESIGN_SYSTEM.md §7, client-approved 2026-07-07). -->
		<div
			x-cloak
			x-show="mobileOpen"
			x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="opacity-0"
			x-transition:enter-end="opacity-100"
			x-transition:leave="transition ease-in duration-200"
			x-transition:leave-start="opacity-100"
			x-transition:leave-end="opacity-0"
			class="fixed inset-0 z-50 bg-off-white flex flex-col md:hidden"
			role="dialog"
			aria-modal="true"
		>
			<div class="flex items-center justify-between px-margin-mobile py-base">
				<span class="font-display-lg text-display-lg-mobile tracking-tighter text-on-background"><?php bloginfo( 'name' ); ?></span>
				<button class="hover:text-primary transition-colors" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'nia-theme' ); ?>" @click="mobileOpen = false">
					<span class="material-symbols-outlined">close</span>
				</button>
			</div>

			<nav class="flex-1 flex flex-col items-center justify-center gap-10">
				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'walker'         => new Nia_Nav_Walker(),
							'menu_class'     => 'flex flex-col items-center gap-10',
						)
					);
					?>
				<?php else : ?>
					<?php nia_theme_primary_nav_fallback(); ?>
				<?php endif; ?>
			</nav>

			<div class="flex items-center justify-center gap-10 pb-section-gap">
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ) ); ?>" class="hover:text-primary transition-colors">
					<span class="material-symbols-outlined">person</span>
				</a>
				<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" class="relative hover:text-primary transition-colors">
					<span class="material-symbols-outlined">shopping_bag</span>
					<span class="absolute -top-1 -right-1 bg-primary text-off-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold"><?php echo (int) $nia_cart_count; ?></span>
				</a>
			</div>
		</div>
	</header>

<?php endif; ?>
