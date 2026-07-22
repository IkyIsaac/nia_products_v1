<?php
/**
 * Nia Theme bootstrap.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

// Bump on every CSS/JS change, not just theme releases — this string is the
// cache-busting ?ver= query param on every enqueued style/script. It sat at
// 0.1.0 unchanged since Phase 2 despite main.js/style.css changing across
// many commits since, including today's — a returning visitor's browser can
// cache main.js indefinitely against that unchanging URL and silently keep
// running old JS after a fix ships, with no error to show for it.
define( 'NIA_THEME_VERSION', '0.2.0' );
define( 'NIA_THEME_DIR', get_template_directory() );
define( 'NIA_THEME_URI', get_template_directory_uri() );

require_once NIA_THEME_DIR . '/includes/class-nia-nav-walker.php';

/**
 * Theme setup: supports declared here rather than in theme.json alone,
 * since this is a classic theme (template hierarchy), not a block/FSE theme —
 * ARCHITECTURE.md §2.
 */
function nia_theme_setup() {
	load_theme_textdomain( 'nia-theme', NIA_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'nia-theme' ),
			'footer'  => __( 'Footer Navigation', 'nia-theme' ),
		)
	);

	// PROJECT_PLAN.md Phase 5 — declares this theme as WooCommerce-ready and
	// enables the product gallery's zoom/lightbox/slider behavior.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'nia_theme_setup' );

/**
 * WooCommerce's own default content wrapper (a generic <div id="primary">)
 * doesn't match this theme's <main> markup used by page.php/archive.php.
 * Swap it for our own so shop/product/cart/checkout/account pages sit inside
 * the same header/footer chrome as every other page — ARCHITECTURE.md §9a.
 * Full visual styling of the content itself is Phase 6; this only fixes the
 * wrapper so WooCommerce's default markup renders inside our theme correctly.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'nia_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'nia_woocommerce_wrapper_end', 10 );

/**
 * Opening half of the WooCommerce content wrapper — see above.
 */
function nia_woocommerce_wrapper_start() {
	echo '<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-section-gap">';
}

/**
 * Closing half of the WooCommerce content wrapper — see above.
 */
function nia_woocommerce_wrapper_end() {
	echo '</main>';
}

/**
 * Every WooCommerce-rendered page is fully re-themed with this project's own
 * Tailwind classes (Phase 6) — WooCommerce's own default frontend stylesheets
 * (woocommerce-layout.css, woocommerce.css, and especially
 * woocommerce-smallscreen.css's higher-specificity mobile-only rules) were
 * still being enqueued alongside ours and fighting with it: product card
 * badges/quick-add overlays losing their absolute positioning and collapsing
 * into normal document flow, stray list-style bullets on card titles, etc.
 * `woocommerce_enqueue_styles` is WooCommerce's own sanctioned filter for a
 * theme that supplies 100% of its own styling. Doesn't touch wc-blocks.css
 * (Cart/Checkout blocks' own handle, unaffected by this filter) — that one's
 * still needed and already re-themed separately in input.css.
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Compiled Tailwind CSS + self-hosted fonts. No CDN requests
 * (ARCHITECTURE.md §3) — fonts.css is generated in Phase 2's font step.
 */
function nia_theme_enqueue_assets() {
	wp_enqueue_style( 'nia-fonts', NIA_THEME_URI . '/assets/css/fonts.css', array(), NIA_THEME_VERSION );
	wp_enqueue_style( 'nia-theme-style', NIA_THEME_URI . '/assets/css/style.css', array( 'nia-fonts' ), NIA_THEME_VERSION );

	// Self-hosted (ARCHITECTURE.md §3) — reactive components (mobile drawer,
	// accordions, quantity stepper) declared inline via x-data/x-show.
	// nia-main must execute before nia-alpine, not just be enqueued before
	// it: both load with the `defer` attribute, and by the time deferred
	// scripts run the document is already past 'loading', so Alpine's CDN
	// build auto-starts (dispatching alpine:init) as part of its own
	// script execution rather than waiting for anything — if nia-main
	// executes even one tick later, its addEventListener( 'alpine:init', ... )
	// (main.js's Alpine.data() registrations) registers after the event
	// already fired. Declaring nia-alpine "dependent on" nia-main is not a
	// real code dependency, just how WP_Scripts is told to guarantee that
	// output order.
	wp_enqueue_script( 'nia-main', NIA_THEME_URI . '/assets/js/main.js', array(), NIA_THEME_VERSION, array( 'strategy' => 'defer' ) );
	wp_enqueue_script( 'nia-alpine', NIA_THEME_URI . '/assets/js/alpine.min.js', array( 'nia-main' ), '3.14.1', array( 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'nia_theme_enqueue_assets' );

/**
 * Same compiled stylesheet in the block editor so Gutenberg content
 * previews match the front end (PROJECT_PLAN.md Phase 2 exit criteria).
 */
function nia_theme_editor_assets() {
	add_editor_style( array( 'assets/css/fonts.css', 'assets/css/style.css' ) );
}
add_action( 'after_setup_theme', 'nia_theme_editor_assets' );

/**
 * Shop link target: the real WooCommerce shop page once it exists, else a
 * placeholder slug (DESIGN_SYSTEM.md §7 nav; page itself is Phase 4/5 work).
 */
function nia_theme_shop_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			return get_permalink( $shop_id );
		}
	}
	return home_url( '/shop/' );
}

/**
 * Static fallback links matching DESIGN_SYSTEM.md §7, used only until a
 * 'primary' menu is assigned in Appearance → Menus (Phase 4 will create the
 * pages these point to).
 */
function nia_theme_primary_nav_fallback() {
	$links = array(
		array(
			'label' => __( 'Shop', 'nia-theme' ),
			'url'   => nia_theme_shop_url(),
		),
		array(
			'label' => __( 'About', 'nia-theme' ),
			'url'   => home_url( '/about/' ),
		),
		array(
			'label' => __( 'Subscription', 'nia-theme' ),
			'url'   => home_url( '/subscription/' ),
		),
		array(
			'label' => __( 'Wellness', 'nia-theme' ),
			'url'   => home_url( '/journal/' ),
		),
	);

	foreach ( $links as $link ) {
		printf(
			'<a class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-all duration-300" href="%1$s">%2$s</a>',
			esc_url( $link['url'] ),
			esc_html( $link['label'] )
		);
	}
}

/**
 * Keep the dev-only style-guide page (PROJECT_PLAN.md Phase 3 exit
 * criteria) out of search engines — it's a component reference, not a
 * public page.
 */
function nia_theme_style_guide_noindex() {
	if ( is_page_template( 'template-style-guide.php' ) ) {
		echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
	}
}
add_action( 'wp_head', 'nia_theme_style_guide_noindex' );
