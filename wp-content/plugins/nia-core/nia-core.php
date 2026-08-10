<?php
/**
 * Plugin Name: Nia Core
 * Description: Business logic for Nia Nutrition — Gutenberg blocks, subscriptions, payments, notifications. The theme holds only presentation (ARCHITECTURE.md §4).
 * Version: 0.2.0
 * Requires PHP: 8.1
 * Requires at least: 6.5
 * Text Domain: nia-core
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

// Bump on every change to assets/ (admin.css, block JS, etc.) — this feeds
// every wp_enqueue_*()'s $ver argument for cache-busting (same convention
// as the theme's NIA_THEME_VERSION, see functions.php).
define( 'NIA_CORE_VERSION', '0.4.0' );
define( 'NIA_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'NIA_CORE_URI', plugin_dir_url( __FILE__ ) );

require_once NIA_CORE_DIR . 'includes/class-nia-blocks.php';
require_once NIA_CORE_DIR . 'includes/class-nia-woocommerce.php';
require_once NIA_CORE_DIR . 'includes/class-nia-reviews.php';
require_once NIA_CORE_DIR . 'includes/class-nia-subscriptions.php';
require_once NIA_CORE_DIR . 'includes/class-nia-admin.php';

/**
 * Boot the plugin's registered subsystems.
 */
function nia_core_init() {
	new Nia_Blocks();
	new Nia_Woocommerce();
	new Nia_Reviews();
	new Nia_Subscriptions();
	new Nia_Admin();
}
add_action( 'plugins_loaded', 'nia_core_init' );
