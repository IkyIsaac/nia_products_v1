<?php
/**
 * Minimal header stub. The real nav/header markup (DESIGN_SYSTEM.md §7,
 * mobile drawer, currency/language switchers) is built in Phase 3 —
 * this file only needs to prove fonts/colors/spacing compile correctly
 * for Phase 2's exit criteria.
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;
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
