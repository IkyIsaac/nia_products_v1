<?php
/**
 * WooCommerce storefront customizations (PROJECT_PLAN.md Phase 5).
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Nia_Woocommerce.
 */
class Nia_Woocommerce {

	/**
	 * Wire hooks.
	 */
	public function __construct() {
		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_downloads_tab' ) );
	}

	/**
	 * The catalog is entirely physical products (seamoss powder/gel/raw/
	 * capsules) — no downloadable products exist or are planned, so the
	 * Downloads tab is dead UI in My Account (PROJECT_PLAN.md Phase 5).
	 *
	 * @param array $items My Account menu items.
	 * @return array
	 */
	public function remove_downloads_tab( $items ) {
		unset( $items['downloads'] );
		return $items;
	}
}
