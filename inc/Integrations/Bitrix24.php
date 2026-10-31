<?php
namespace Jubelio\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bitrix24 {

	const DEFAULT_COMPANY_ENDPOINT = 'https://jubelio.bitrix24.id/rest/1502/517r7nei9bwsf4j0/crm.company.add.json';
	const DEFAULT_LEAD_ENDPOINT    = 'https://jubelio.bitrix24.id/rest/1502/sb5ht1x3v7ewgnpt/crm.lead.add.json';

	public static function init() {
		self::seed_options();

		if ( is_admin() ) {
			add_action( 'admin_init', array( __CLASS__, 'register_admin_settings' ) );
		}
	}

	protected static function seed_options() {
		if ( false === get_option( 'jubelio_bitrix_company_endpoint' ) ) {
			add_option( 'jubelio_bitrix_company_endpoint', self::DEFAULT_COMPANY_ENDPOINT, '', 'no' );
		}
		if ( false === get_option( 'jubelio_bitrix_lead_endpoint' ) ) {
			add_option( 'jubelio_bitrix_lead_endpoint', self::DEFAULT_LEAD_ENDPOINT, '', 'no' );
		}
	}

	public static function register_admin_settings() {
		register_setting( 'general', 'jubelio_bitrix_company_endpoint', array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		) );

		register_setting( 'general', 'jubelio_bitrix_lead_endpoint', array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		) );

		add_settings_section(
			'jubelio_bitrix_section',
			__( 'Jubelio Bitrix24 CRM Integration Webhooks', 'hello-elementor-child' ),
			array( __CLASS__, 'render_settings_section_info' ),
			'general'
		);

		add_settings_field(
			'jubelio_bitrix_company_endpoint',
			__( 'Bitrix24 Company Add Webhook URL', 'hello-elementor-child' ),
			array( __CLASS__, 'render_company_field' ),
			'general',
			'jubelio_bitrix_section'
		);

		add_settings_field(
			'jubelio_bitrix_lead_endpoint',
			__( 'Bitrix24 Lead Add Webhook URL', 'hello-elementor-child' ),
			array( __CLASS__, 'render_lead_field' ),
			'general',
			'jubelio_bitrix_section'
		);
	}

	public static function render_settings_section_info() {
		echo '<p>' . esc_html__( 'Manage your Bitrix24 webhook endpoints safely. You can also define constants JUBELIO_BITRIX_COMPANY_ENDPOINT and JUBELIO_BITRIX_LEAD_ENDPOINT in wp-config.php for higher security.', 'hello-elementor-child' ) . '</p>';
	}

	public static function render_company_field() {
		$value = get_option( 'jubelio_bitrix_company_endpoint', '' );
		$is_const = defined( 'JUBELIO_BITRIX_COMPANY_ENDPOINT' );
		echo '<input type="url" name="jubelio_bitrix_company_endpoint" value="' . esc_attr( $value ) . '" class="regular-text code"' . ( $is_const ? ' disabled' : '' ) . ' />';
		if ( $is_const ) {
			echo '<p class="description">' . esc_html__( 'Overridden by JUBELIO_BITRIX_COMPANY_ENDPOINT in wp-config.php', 'hello-elementor-child' ) . '</p>';
		}
	}

	public static function render_lead_field() {
		$value = get_option( 'jubelio_bitrix_lead_endpoint', '' );
		$is_const = defined( 'JUBELIO_BITRIX_LEAD_ENDPOINT' );
		echo '<input type="url" name="jubelio_bitrix_lead_endpoint" value="' . esc_attr( $value ) . '" class="regular-text code"' . ( $is_const ? ' disabled' : '' ) . ' />';
		if ( $is_const ) {
			echo '<p class="description">' . esc_html__( 'Overridden by JUBELIO_BITRIX_LEAD_ENDPOINT in wp-config.php', 'hello-elementor-child' ) . '</p>';
		}
	}

	public static function get_company_endpoint() {
		if ( defined( 'JUBELIO_BITRIX_COMPANY_ENDPOINT' ) && ! empty( constant( 'JUBELIO_BITRIX_COMPANY_ENDPOINT' ) ) ) {
			return constant( 'JUBELIO_BITRIX_COMPANY_ENDPOINT' );
		}

		$env = getenv( 'JUBELIO_BITRIX_COMPANY_ENDPOINT' );
		if ( ! empty( $env ) ) {
			return $env;
		}

		$option = get_option( 'jubelio_bitrix_company_endpoint' );
		if ( ! empty( $option ) && 'APIKEY' !== $option ) {
			return $option;
		}

		return apply_filters( 'jubelio_bitrix_company_endpoint', '' );
	}

	public static function get_lead_endpoint() {
		if ( defined( 'JUBELIO_BITRIX_LEAD_ENDPOINT' ) && ! empty( constant( 'JUBELIO_BITRIX_LEAD_ENDPOINT' ) ) ) {
			return constant( 'JUBELIO_BITRIX_LEAD_ENDPOINT' );
		}

		$env = getenv( 'JUBELIO_BITRIX_LEAD_ENDPOINT' );
		if ( ! empty( $env ) ) {
			return $env;
		}

		$option = get_option( 'jubelio_bitrix_lead_endpoint' );
		if ( ! empty( $option ) && 'APIKEY' !== $option ) {
			return $option;
		}

		return apply_filters( 'jubelio_bitrix_lead_endpoint', '' );
	}

	public static function add_company( $company_name, $phone = '', $email = '' ) {
		$endpoint = self::get_company_endpoint();
		if ( empty( $endpoint ) || 'APIKEY' === $endpoint ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Jubelio Bitrix24] Company webhook skipped: missing endpoint URL.' );
			}
			return null;
		}

		$payload = array(
			'fields' => array(
				'TITLE' => ! empty( $company_name ) ? $company_name : 'Unknown Company',
				'PHONE' => ! empty( $phone ) ? array( array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ) ) : array(),
				'EMAIL' => ! empty( $email ) ? array( array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ) ) : array(),
			),
		);

		$response = wp_remote_post( $endpoint, array(
			'body'    => wp_json_encode( $payload ),
			'headers' => array( 'Content-Type' => 'application/json' ),
			'timeout' => 15,
		) );

		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Jubelio Bitrix24] Company add error: ' . $response->get_error_message() );
			}
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['result'] ) ) {
			return intval( $body['result'] );
		}

		return null;
	}

	public static function add_lead( array $lead_fields ) {
		$endpoint = self::get_lead_endpoint();
		if ( empty( $endpoint ) || 'APIKEY' === $endpoint ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Jubelio Bitrix24] Lead webhook skipped: missing endpoint URL.' );
			}
			return null;
		}

		$payload = array(
			'fields' => $lead_fields,
			'params' => array( 'REGISTER_SONET_EVENT' => 'Y' ),
		);

		$response = wp_remote_post( $endpoint, array(
			'body'    => wp_json_encode( $payload ),
			'headers' => array( 'Content-Type' => 'application/json' ),
			'timeout' => 15,
		) );

		if ( is_wp_error( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Jubelio Bitrix24] Lead add error: ' . $response->get_error_message() );
			}
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['result'] ) ) {
			return intval( $body['result'] );
		}

		return null;
	}
}
