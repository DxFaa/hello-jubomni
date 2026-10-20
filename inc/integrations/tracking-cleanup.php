<?php
/**
 * Tracking Overrides & Schema Injection
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Disable SiteKit Analytics on update-page-new
add_filter( 'googlesitekit_analytics_tracking_disabled', function( $disabled ) {
	if ( is_page( 'update-page-new' ) ) {
		return true;
	}
	return $disabled;
} );

// Prevent GTM script on update-page-new
add_filter( 'googlesitekit_gtm_output_tag', function( $output, $container_id ) {
	if ( is_page( 'update-page-new' ) ) {
		return '';
	}
	return $output;
}, 10, 2 );

// Stop tracking pixel on update-page-new
add_action( 'wp_head', function() {
	if ( is_page( 'update-page-new' ) ) {
		return;
	}
} );

// JavaScript fallback to block GA4 page_view if injected elsewhere on update-page-new
add_action( 'wp_footer', function() {
	if ( is_page( array( 'update-page-new' ) ) ) : ?>
		<script>
		(function() {
			document.querySelectorAll('script[src*="googletagmanager.com/gtag/js"]').forEach(el => el.remove());
			window.dataLayer = window.dataLayer || [];
			const originalPush = window.dataLayer.push;
			window.dataLayer.push = function() {
				if (arguments[0] && arguments[0].event === 'page_view') {
					console.log('GA4 page_view blocked on this page');
					return;
				}
				return originalPush.apply(window.dataLayer, arguments);
			};
			if (typeof window.gtag === 'function') {
				const originalGtag = window.gtag;
				window.gtag = function() {
					if (arguments[0] === 'event' && arguments[1] === 'page_view') {
						console.log('GA4 gtag page_view blocked');
						return;
					}
					return originalGtag.apply(this, arguments);
				};
			}
			document.querySelectorAll('script[src*="googletagmanager.com/gtm"]').forEach(el => el.remove());
			document.querySelectorAll('noscript iframe[src*="googletagmanager.com/ns.html"]').forEach(el => el.remove());
		})();
		</script>
	<?php endif;
} );

// ACF Schema JSON in wp_head
add_action( 'wp_head', function() {
	if ( is_page() && function_exists( 'get_field' ) ) {
		$schema = get_field( 'schema_json' );
		if ( $schema ) {
			echo '<script type="application/ld+json">' . $schema . '</script>';
		}
	}
} );
