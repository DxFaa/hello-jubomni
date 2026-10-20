<?php
/**
 * Tracking Overrides & Schema Injection
 *
 * @package Jubelio
 */

namespace Jubelio\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tracking {

	public static function init() {
		add_filter( 'googlesitekit_analytics_tracking_disabled', array( __CLASS__, 'disable_sitekit_analytics' ) );
		add_filter( 'googlesitekit_gtm_output_tag', array( __CLASS__, 'disable_sitekit_gtm' ), 10, 2 );
		add_action( 'wp_footer', array( __CLASS__, 'block_analytics_footer_scripts' ) );
		add_action( 'wp_head', array( __CLASS__, 'inject_schema_json' ) );
	}

	public static function disable_sitekit_analytics( $disabled ) {
		if ( is_page( 'update-page-new' ) ) {
			return true;
		}
		return $disabled;
	}

	public static function disable_sitekit_gtm( $output, $container_id ) {
		if ( is_page( 'update-page-new' ) ) {
			return '';
		}
		return $output;
	}

	public static function block_analytics_footer_scripts() {
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
	}

	public static function inject_schema_json() {
		if ( is_page() && function_exists( 'get_field' ) ) {
			$schema = get_field( 'schema_json' );
			if ( $schema ) {
				echo '<script type="application/ld+json">' . $schema . '</script>';
			}
		}
	}
}
