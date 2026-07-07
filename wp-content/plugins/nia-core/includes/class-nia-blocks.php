<?php
/**
 * Registers the custom Gutenberg block library (ARCHITECTURE.md §4, DESIGN_SYSTEM.md §14).
 *
 * @package Nia_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers every block found under blocks/*\/block.json.
 */
class Nia_Blocks {

	/**
	 * Wire up registration.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ) );
	}

	/**
	 * Adds a dedicated "Nia" category so the block library groups together
	 * in the inserter instead of scattering into "Text"/"Media".
	 *
	 * @param array $categories Existing block categories.
	 * @return array
	 */
	public function register_block_category( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'nia-blocks',
					'title' => __( 'Nia', 'nia-core' ),
				),
			),
			$categories
		);
	}

	/**
	 * Register each block by its block.json.
	 */
	public function register_blocks() {
		foreach ( $this->block_dirs() as $dir ) {
			register_block_type( $dir );
		}
	}

	/**
	 * Shared editor-only styling for block inspector controls (repeater rows, etc).
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_style(
			'nia-blocks-editor',
			NIA_CORE_URI . 'blocks/editor.css',
			array(),
			NIA_CORE_VERSION
		);
	}

	/**
	 * Absolute paths to every block directory that has a block.json.
	 *
	 * @return string[]
	 */
	private function block_dirs() {
		$root  = NIA_CORE_DIR . 'blocks';
		$dirs  = array();
		$found = glob( $root . '/*/block.json' );

		if ( ! $found ) {
			return $dirs;
		}

		foreach ( $found as $manifest ) {
			$dirs[] = dirname( $manifest );
		}

		return $dirs;
	}
}
