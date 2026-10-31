<?php
namespace Jubelio\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ElementorQueries {

	public static function init() {
		add_action( 'elementor/query/combined_resource_query', array( __CLASS__, 'combined_resource_query' ) );
		add_action( 'elementor/query/cerita_pebisnis_query', array( __CLASS__, 'cerita_pebisnis_query' ) );
	}

	public static function combined_resource_query( $query ) {
		$query->set( 'post_type', array( 'ebook', 'whitepaper', 'template' ) );
		$query->set( 'post_status', 'publish' );
	}

	public static function cerita_pebisnis_query( $query ) {
		$query->set( 'post_type', 'cerita-pebisnis' );
		$query->set( 'post_status', 'publish' );
	}
}
