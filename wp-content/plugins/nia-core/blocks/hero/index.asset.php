<?php
/**
 * Manually-authored asset manifest (no build step — ARCHITECTURE.md §2).
 * Declares editor script dependencies/version explicitly since there's no
 * wp-scripts webpack build to auto-generate this file.
 *
 * @package Nia_Core
 */

return array(
	'dependencies' => array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
	'version'      => '0.1.0',
);
