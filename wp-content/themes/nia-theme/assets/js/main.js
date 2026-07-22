/**
 * Small vanilla-JS one-off effects (ARCHITECTURE.md §3) — no SPA framework.
 * Alpine.js (assets/js/alpine.min.js) handles reactive components declared
 * inline via x-data (mobile drawer, accordions, quantity stepper).
 * Scroll-reveal / smooth-scroll modules land here as pages that need them
 * are built in Phase 4+.
 */

/**
 * Last known scroll position, tracked continuously rather than read on
 * demand. Reading window.scrollY fresh inside a click/mousedown handler
 * on this page can already reflect a scroll jump that happens as part of
 * the click itself (reproducible even on the pre-existing "Helpful"
 * button, unrelated to any of this file's own code — some interaction
 * between the fixed/backdrop-blur header and default focus handling, not
 * fully root-caused). Tracking it on 'scroll' instead captures the
 * position from just before that jump, which is what "restore the scroll
 * position" actually needs.
 */
var niaLastScrollY = window.scrollY;
var niaLastScrollX = window.scrollX;
window.addEventListener(
	'scroll',
	function () {
		niaLastScrollY = window.scrollY;
		niaLastScrollX = window.scrollX;
	},
	{ passive: true }
);

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

/**
 * Review sort/rating-filter links (PDP Reviews & Ratings). Plain <a
 * href="?review_sort=...#nia-reviews"> links used to be full page
 * reloads — landing on a hash always scrolls the browser there, so a
 * reader scrolled deep into the review list got yanked back to the top
 * of the section on every click. Intercepted here into one AJAX refresh
 * of #nia-reviews-grid instead: no reload, no navigation, so nothing
 * scrolls. Event delegation (not per-link listeners) so this keeps
 * working on the links inside the fragment that just replaced this one.
 */
document.addEventListener( 'click', function ( event ) {
	var link = event.target.closest( '.nia-review-sort-link, .nia-review-filter-link' );
	if ( ! link || typeof niaReviews === 'undefined' ) {
		return;
	}

	var grid = document.getElementById( 'nia-reviews-grid' );
	var section = document.getElementById( 'nia-reviews' );
	if ( ! grid || ! section ) {
		return;
	}

	event.preventDefault();

	var targetUrl = new URL( link.href, window.location.href );
	var productId = section.dataset.productId;

	var body = new URLSearchParams( {
		action: 'nia_filter_reviews',
		nonce: niaReviews.filterNonce,
		product_id: productId,
	} );
	var reviewSort = targetUrl.searchParams.get( 'review_sort' );
	var ratingFilter = targetUrl.searchParams.get( 'rating_filter' );
	if ( reviewSort ) {
		body.append( 'review_sort', reviewSort );
	}
	if ( ratingFilter ) {
		body.append( 'rating_filter', ratingFilter );
	}

	grid.setAttribute( 'aria-busy', 'true' );

	// Use the continuously-tracked position (see niaLastScrollY above),
	// not a fresh window.scrollY read — by the time this handler runs,
	// scrollY can already reflect a jump that happens as part of the
	// click itself. That jump also isn't a single clean event: it can
	// land a moment after the frame we first observe it in, so a single
	// restore attempt loses the race — keep re-asserting the pre-click
	// position for a short window afterward instead of trying to catch
	// the exact moment.
	var scrollX = niaLastScrollX;
	var scrollY = niaLastScrollY;
	function restoreScroll() {
		window.scrollTo( scrollX, scrollY );
	}

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
			grid.innerHTML = json.data.html;
			window.history.pushState( null, '', targetUrl.pathname + targetUrl.search + targetUrl.hash );

			restoreScroll();
			requestAnimationFrame( restoreScroll );
			[ 0, 50, 150, 300, 600 ].forEach( function ( delay ) {
				setTimeout( restoreScroll, delay );
			} );
		} )
		.finally( function () {
			grid.removeAttribute( 'aria-busy' );
		} );
} );
