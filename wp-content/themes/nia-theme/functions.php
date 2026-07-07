<?php
/**
 * Nia Theme bootstrap.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'NIA_THEME_VERSION', '0.1.0' );
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
}
add_action( 'after_setup_theme', 'nia_theme_setup' );

/**
 * Compiled Tailwind CSS + self-hosted fonts. No CDN requests
 * (ARCHITECTURE.md §3) — fonts.css is generated in Phase 2's font step.
 */
function nia_theme_enqueue_assets() {
	wp_enqueue_style( 'nia-fonts', NIA_THEME_URI . '/assets/css/fonts.css', array(), NIA_THEME_VERSION );
	wp_enqueue_style( 'nia-theme-style', NIA_THEME_URI . '/assets/css/style.css', array( 'nia-fonts' ), NIA_THEME_VERSION );

	// Self-hosted (ARCHITECTURE.md §3) — reactive components (mobile drawer,
	// accordions, quantity stepper) declared inline via x-data/x-show.
	wp_enqueue_script( 'nia-alpine', NIA_THEME_URI . '/assets/js/alpine.min.js', array(), '3.14.1', array( 'strategy' => 'defer' ) );
	wp_enqueue_script( 'nia-main', NIA_THEME_URI . '/assets/js/main.js', array(), NIA_THEME_VERSION, array( 'strategy' => 'defer' ) );
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
