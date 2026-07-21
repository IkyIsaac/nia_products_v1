/**
 * Small vanilla-JS one-off effects (ARCHITECTURE.md §3) — no SPA framework.
 * Alpine.js (assets/js/alpine.min.js) handles reactive components declared
 * inline via x-data (mobile drawer, accordions, quantity stepper).
 * Scroll-reveal / smooth-scroll modules land here as pages that need them
 * are built in Phase 4+.
 */

/**
 * Product review "Helpful" vote (PDP Reviews & Ratings, PROJECT_PLAN.md
 * Phase 6). One AJAX call to nia-core's Nia_Reviews::ajax_toggle_helpful()
 * per click, with an optimistic UI update — no page reload, no framework
 * needed for a single toggle button.
 */
document.addEventListener( 'click', function ( event ) {
	var button = event.target.closest( '.nia-helpful-btn' );
	if ( ! button || typeof niaReviews === 'undefined' ) {
		return;
	}

	if ( button.disabled ) {
		return;
	}
	button.disabled = true;

	var body = new URLSearchParams( {
		action: 'nia_toggle_helpful',
		nonce: niaReviews.nonce,
		comment_id: button.dataset.commentId,
	} );

	fetch( niaReviews.ajaxUrl, {
		method: 'POST',
		credentials: 'same-origin',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: body.toString(),
	} )
		.then( function ( response ) {
			return response.json();
		} )
		.then( function ( json ) {
			if ( ! json.success ) {
				return;
			}

			var countEl = button.querySelector( '.nia-helpful-count' );
			var iconEl = button.querySelector( '.material-symbols-outlined' );

			if ( countEl ) {
				countEl.textContent = '(' + json.data.count + ')';
			}
			button.classList.toggle( 'is-liked', json.data.liked );
			button.setAttribute( 'aria-pressed', json.data.liked ? 'true' : 'false' );
			if ( iconEl ) {
				iconEl.style.fontVariationSettings = "'FILL' " + ( json.data.liked ? 1 : 0 );
			}
		} )
		.finally( function () {
			button.disabled = false;
		} );
} );
