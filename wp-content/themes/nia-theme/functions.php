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
