/**
 * List tables (Products, Product Reviews, Subscriptions, and the single
 * product screen's "Reviews" meta box) — collapses the default inline
 * "Edit | Quick Edit | Trash | View | Duplicate"-style row actions into a
 * single kebab-menu button, moved into each table's own dedicated
 * "Actions" column (`td.column-nia_actions`, added server-side by
 * Nia_Admin/Nia_Subscriptions) rather than crowding into whichever column
 * happens to be primary. `.row-actions` still physically *renders* inside
 * the primary column — that's baked into WP/WC core's own row-rendering
 * and isn't something a filter can redirect — so this always looks the
 * actual `.row-actions` up via `td.column-primary` first, then relocates
 * it into the sibling `.column-nia_actions` cell in the same row (falling
 * back to embedding it in the primary column itself if a table doesn't
 * have a dedicated Actions column, so this script never breaks a screen
 * it hasn't been specifically wired up for).
 *
 * Deliberately *moves* the existing `.row-actions` element into the new
 * dropdown panel rather than rebuilding it from scratch — the `<a>`/
 * `<button class="editinline">` elements it contains are exactly what
 * WordPress's/WooCommerce's own JS (inline-edit-post.js, the Trash confirm
 * flow) already listens for. Reparenting a DOM node preserves its identity
 * and any delegated event listeners; cloning or rebuilding it would not.
 *
 * @package Nia_Core
 */

( function () {
	'use strict';

	function closeAllPanels() {
		document.querySelectorAll( '.nia-row-menu-panel.is-open' ).forEach( function ( panel ) {
			panel.classList.remove( 'is-open' );
		} );
		document.querySelectorAll( '.nia-row-menu-toggle.is-open' ).forEach( function ( toggle ) {
			toggle.classList.remove( 'is-open' );
		} );
	}

	function stripSeparatorTextNodes( rowActions ) {
		// WP core places the " | " separator *inside* each action's own
		// <span> (wp-admin/includes/class-wp-list-table.php:
		// "<span class='$action'>{$link}{$separator}</span>"), not as a
		// sibling of the spans — a shallow childNodes sweep on rowActions
		// itself finds nothing to strip. Walk every text node at any depth
		// and drop the ones that are pure whitespace/pipe separators.
		var walker = document.createTreeWalker( rowActions, NodeFilter.SHOW_TEXT, null );
		var toRemove = [];
		var node;
		while ( ( node = walker.nextNode() ) ) {
			if ( /^[\s|]*$/.test( node.textContent ) ) {
				toRemove.push( node );
			}
		}
		toRemove.forEach( function ( node ) {
			node.parentNode.removeChild( node );
		} );
	}

	function buildMenu( cell, rowActions, dedicated ) {
		var wrapper = document.createElement( 'span' );
		wrapper.className = dedicated ? 'nia-row-menu nia-row-menu--dedicated' : 'nia-row-menu';

		var toggle = document.createElement( 'button' );
		toggle.type = 'button';
		toggle.className = 'nia-row-menu-toggle';
		toggle.setAttribute( 'aria-label', 'Row actions' );
		toggle.innerHTML = '&#8942;';

		var panel = document.createElement( 'div' );
		panel.className = 'nia-row-menu-panel';

		stripSeparatorTextNodes( rowActions );
		panel.appendChild( rowActions );
		wrapper.appendChild( toggle );
		wrapper.appendChild( panel );
		cell.appendChild( wrapper );

		toggle.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			var wasOpen = panel.classList.contains( 'is-open' );
			closeAllPanels();
			if ( ! wasOpen ) {
				panel.classList.add( 'is-open' );
				toggle.classList.add( 'is-open' );
			}
		} );

		// Quick Edit swaps the row for an inline edit form — close the
		// (now orphaned) panel rather than leaving it dangling open.
		panel.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '.editinline' ) ) {
				closeAllPanels();
			}
		} );
	}

	function init() {
		document.querySelectorAll( 'td.column-primary .row-actions' ).forEach( function ( rowActions ) {
			var primaryCell = rowActions.closest( 'td.column-primary' );
			if ( ! primaryCell ) {
				return;
			}

			var row = primaryCell.closest( 'tr' );
			var actionsCell = row ? row.querySelector( 'td.column-nia_actions' ) : null;
			var targetCell = actionsCell || primaryCell;

			if ( targetCell.querySelector( '.nia-row-menu' ) ) {
				return;
			}

			buildMenu( targetCell, rowActions, !! actionsCell );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', init );
	document.addEventListener( 'click', closeAllPanels );
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			closeAllPanels();
		}
	} );

	// The product edit screen's "Reviews" meta box (WP core's own
	// post_comment_meta_box(), relabeled by WooCommerce) loads its rows
	// via AJAX (commentsBox.load()) after DOMContentLoaded has already
	// fired, so the one-shot init() above finds nothing there yet. A
	// MutationObserver re-runs init() (its own "already processed" guard
	// keeps it idempotent) whenever anything is added to the page —
	// covers that AJAX load plus any other list table that swaps rows in
	// dynamically (e.g. Quick Edit's own cancel/save cycle).
	if ( window.MutationObserver ) {
		new MutationObserver( function ( mutations ) {
			var hasAddedNodes = mutations.some( function ( m ) {
				return m.addedNodes && m.addedNodes.length > 0;
			} );
			if ( hasAddedNodes ) {
				init();
			}
		} ).observe( document.body, { childList: true, subtree: true } );
	}
} )();
