<?php
/**
 * Custom nav walker — active-state driven by the current page/menu location,
 * not hardcoded per template (DESIGN_SYSTEM.md §7, PROJECT_PLAN.md Phase 3).
 *
 * @package Nia_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders top-level primary-nav links with the mockup's active/inactive classes.
 */
class Nia_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Output a single nav link.
	 *
	 * @param string   $output Passed by reference, appended to.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Menu args.
	 * @param int      $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$active_states = array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' );
		$is_active     = (bool) array_intersect( $active_states, $item->classes );

		$classes = $is_active
			? 'font-label-lg text-label-lg text-primary border-b-2 border-primary pb-1 transition-all duration-300'
			: 'font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-all duration-300';

		$output .= sprintf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			esc_attr( $classes ),
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}
}
