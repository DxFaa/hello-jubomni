<?php
/**
 * Contact Form 7 Lead & Publication Submission Handlers
 *
 * Handles custom dynamic tags, Bitrix24 CRM integration, category & location lookups,
 * ACF publication file downloads, and CFDB7 database optimization.
 *
 * @package Jubelio
 */

namespace Jubelio\Integrations;

use WPCF7_Submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormLeadHandler {

	/**
	 * Order volume mapping to Bitrix24 enum IDs
	 *
	 * @var array
	 */
	const ORDERAN_MAP = array(
		'<100'          => '1860',
		'100-499'       => '1862',
		'500-999'       => '1864',
		'1.000-1.999'   => '1866',
		'2.000-4.999'   => '1868',
		'5.000-9.999'   => '1870',
		'10.000-19.999' => '1872',
		'20.000-59.999' => '1874',
		'>60.000'       => '1876',
	);

	/**
	 * Discovery source mapping to Bitrix24 enum IDs
	 *
	 * @var array
	 */
	const SUMBER_TAHU_MAP = array(
		'Sosial Media'      => '9360',
		'Rekomendasi Teman' => '9362',
		'Search Engine AI'  => '9364',
		'Influencer/KOL'    => '9366',
		'Lainnya'           => '9368',
	);

	/**
	 * Product interest mapping to Bitrix24 enum IDs
	 *
	 * @var array
	 */
	const PRODUK_MAP = array(
		'Omnichannel' => '15058',
		'POS'         => '15060',
		'Store'       => '15062',
		'Chat'        => '15064',
		'Shipment'    => '15066',
	);

	/**
	 * Register all CF7 & submission hooks
	 */
	public static function init() {
		// 1. Dynamic CF7 Form Tags
		add_action( 'wpcf7_init', array( __CLASS__, 'register_form_tags' ) );

		// 2. CF7 Posted Data Shadow IDs & Mappings
		add_filter( 'wpcf7_posted_data', array( __CLASS__, 'generate_shadow_ids' ) );

		// 3. Prevent duplicate submissions before CFDB7 or Mail processing
		add_filter( 'wpcf7_validate', array( __CLASS__, 'prevent_duplicate_submission' ), 20, 2 );

		// 4. CF7 Webhook Dispatch to Bitrix24
		add_action( 'wpcf7_before_send_mail', array( __CLASS__, 'handle_form_submission' ), 10, 3 );

		// 5. Custom Special Mail Tag for Download Links
		add_filter( 'wpcf7_special_mail_tags', array( __CLASS__, 'publikasi_file_mail_tag' ), 10, 3 );

		// 6. Shortcode for Publication Thank You / Download Pages
		add_shortcode( 'get_data_from_url_id', array( __CLASS__, 'shortcode_get_data_from_url_id' ) );

		// 7. CFDB7 Database Optimization (strip large b24_trace string)
		add_filter( 'cfdb7_before_save_data', array( __CLASS__, 'exclude_trace_from_cfdb7' ) );
	}

	/**
	 * Register custom CF7 form tags: [dynamic_source_url] and [dynamic_param]
	 */
	public static function register_form_tags() {
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag( 'dynamic_source_url', array( __CLASS__, 'render_source_url_tag' ), array( 'name-attr' => true ) );
			wpcf7_add_form_tag( 'dynamic_param', array( __CLASS__, 'render_dynamic_param_tag' ), array( 'name-attr' => true ) );
		}
	}

	/**
	 * Render [dynamic_source_url] form tag
	 *
	 * @param array $tag Tag attributes.
	 * @return string HTML hidden input.
	 */
	public static function render_source_url_tag( $tag ) {
		$name = isset( $tag['name'] ) ? $tag['name'] : ( isset( $tag->name ) ? $tag->name : '' );
		if ( empty( $name ) ) {
			return '';
		}

		$url_value = 'Direct or Unknown';

		if ( isset( $_GET['ref_id'] ) && ! empty( $_GET['ref_id'] ) ) {
			$id   = intval( $_GET['ref_id'] );
			$post = get_post( $id );

			if ( $post ) {
				$url_value = get_permalink( $id );

				if ( false !== strpos( $url_value, '?' ) ) {
					$slug = $post->post_name;
					switch ( $post->post_type ) {
						case 'solusi':
							$url_value = home_url( '/solusi/' . $slug . '/' );
							break;
						case 'post':
							$url_value = home_url( '/' . $slug . '/' );
							break;
						case 'slp':
							$url_value = home_url( '/layanan/' . $slug . '/' );
							break;
						case 'whitepaper':
							$url_value = home_url( '/whitepaper/' . $slug . '/' );
							break;
						case 'ebook':
							$url_value = home_url( '/ebook/' . $slug . '/' );
							break;
						case 'template':
							$url_value = home_url( '/template/' . $slug . '/' );
							break;
					}
				}
			}
		} elseif ( isset( $_GET['ref_url'] ) && ! empty( $_GET['ref_url'] ) ) {
			$url_value = esc_url_raw( wp_unslash( $_GET['ref_url'] ) );
		}

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" class="wpcf7-form-control wpcf7-hidden" />',
			esc_attr( $name ),
			esc_attr( $url_value )
		);
	}

	/**
	 * Render [dynamic_param] form tag from query string
	 *
	 * @param array $tag Tag attributes.
	 * @return string HTML hidden input.
	 */
	public static function render_dynamic_param_tag( $tag ) {
		$name = isset( $tag['name'] ) ? $tag['name'] : ( isset( $tag->name ) ? $tag->name : '' );
		if ( empty( $name ) ) {
			return '';
		}

		$param_value = '';
		if ( isset( $_GET[ $name ] ) && ! empty( $_GET[ $name ] ) ) {
			$param_value = sanitize_text_field( wp_unslash( $_GET[ $name ] ) );
		}

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" class="wpcf7-form-control wpcf7-hidden" />',
			esc_attr( $name ),
			esc_attr( $param_value )
		);
	}

	/**
	 * Map human-readable form selections to Bitrix24 IDs
	 *
	 * @param array $posted_data Submitted CF7 data.
	 * @return array Augmented data with shadow Bitrix fields.
	 */
	public static function generate_shadow_ids( $posted_data ) {
		if ( ! class_exists( 'WPCF7_Submission' ) ) {
			return $posted_data;
		}

		$submission = WPCF7_Submission::get_instance();
		if ( ! $submission ) {
			return $posted_data;
		}

		$contact_form = $submission->get_contact_form();
		$form_title   = $contact_form ? $contact_form->title() : '';

		// 1. Orderan mapping (Daftar Jubelio & Download Publikasi)
		if ( isset( $posted_data['Orderan'] ) ) {
			$ord_val      = $posted_data['Orderan'];
			$orderan_text = is_array( $ord_val ) ? ( isset( $ord_val[0] ) ? $ord_val[0] : '' ) : $ord_val;
			$posted_data['bitrix_orderan'] = isset( self::ORDERAN_MAP[ $orderan_text ] ) ? self::ORDERAN_MAP[ $orderan_text ] : '';
		}

		// Only continue for Daftar Jubelio forms
		if ( 'Daftar Jubelio' !== $form_title && 'Daftar Jubelio EN' !== $form_title ) {
			return $posted_data;
		}

		// 2. Category mapping (using 1-year transient cache)
		if ( isset( $posted_data['select-category'] ) ) {
			$cat_val  = $posted_data['select-category'];
			$cat_text = is_array( $cat_val ) ? ( isset( $cat_val[0] ) ? $cat_val[0] : '' ) : $cat_val;

			$cat_map = get_transient( 'jubelio_bitrix_cat_map_cache' );
			if ( false === $cat_map || empty( $cat_map ) ) {
				$cat_map   = array();
				$json_path = WP_CONTENT_DIR . '/uploads/bitrix_categories_.json';
				if ( file_exists( $json_path ) ) {
					$json_data   = file_get_contents( $json_path );
					$parsed_data = json_decode( $json_data, true );
					if ( isset( $parsed_data['LIST'] ) && is_array( $parsed_data['LIST'] ) ) {
						foreach ( $parsed_data['LIST'] as $item ) {
							if ( isset( $item['VALUE'], $item['ID'] ) ) {
								$cat_map[ trim( $item['VALUE'] ) ] = trim( $item['ID'] );
							}
						}
						if ( ! empty( $cat_map ) ) {
							set_transient( 'jubelio_bitrix_cat_map_cache', $cat_map, YEAR_IN_SECONDS );
						}
					}
				}
			}
			$posted_data['bitrix_kategori'] = isset( $cat_map[ $cat_text ] ) ? $cat_map[ $cat_text ] : '';
		}

		// 3. Location mapping (using 1-year transient cache)
		if ( isset( $posted_data['select-location'] ) ) {
			$loc_val  = $posted_data['select-location'];
			$loc_text = is_array( $loc_val ) ? ( isset( $loc_val[0] ) ? $loc_val[0] : '' ) : $loc_val;

			$loc_map = get_transient( 'jubelio_bitrix_loc_map_cache' );
			if ( false === $loc_map || empty( $loc_map ) ) {
				$loc_map   = array();
				$json_path = WP_CONTENT_DIR . '/uploads/bitrix_provinsikota_.json';
				if ( file_exists( $json_path ) ) {
					$json_data   = file_get_contents( $json_path );
					$parsed_data = json_decode( $json_data, true );
					if ( isset( $parsed_data['LIST'] ) && is_array( $parsed_data['LIST'] ) ) {
						foreach ( $parsed_data['LIST'] as $item ) {
							if ( isset( $item['VALUE'], $item['ID'] ) ) {
								$loc_map[ trim( $item['VALUE'] ) ] = trim( $item['ID'] );
							}
						}
						if ( ! empty( $loc_map ) ) {
							set_transient( 'jubelio_bitrix_loc_map_cache', $loc_map, YEAR_IN_SECONDS );
						}
					}
				}
			}
			$posted_data['bitrix_provinsi'] = isset( $loc_map[ $loc_text ] ) ? $loc_map[ $loc_text ] : '';
		}

		return $posted_data;
	}

	/**
	 * Detect locale (ID vs EN) based on referral post or Polylang
	 *
	 * @param string $ref_id Referral post ID.
	 * @param string $form_title Contact form title.
	 * @return string 'ID' or 'EN'
	 */
	public static function resolve_locale( $ref_id, $form_title = '' ) {
		$locale_val = 'ID';

		if ( ! empty( $ref_id ) ) {
			$id = intval( $ref_id );
			if ( function_exists( 'pll_get_post_language' ) ) {
				$post_lang = pll_get_post_language( $id, 'slug' );
				if ( $post_lang ) {
					$locale_val = strtoupper( $post_lang );
				}
			} else {
				$locale_val = ( false !== strpos( $form_title, 'EN' ) ) ? 'EN' : 'ID';
			}
		} elseif ( function_exists( 'pll_current_language' ) ) {
			$current_lang = pll_current_language( 'slug' );
			if ( $current_lang ) {
				$locale_val = strtoupper( $current_lang );
			}
		}

		return $locale_val;
	}

	/**
	 * Process form submission and dispatch to Bitrix24 CRM
	 *
	 * @param \WPCF7_ContactForm $contact_form CF7 form instance.
	 * @param bool               $abort Abort flag.
	 * @param \WPCF7_Submission  $submission CF7 submission instance.
	 */
	public static function handle_form_submission( $contact_form, &$abort, $submission ) {
		// 1. In-process idempotency guard (prevent duplicate execution within the same PHP lifecycle)
		static $processed_submissions = array();
		$submission_hash = spl_object_hash( $submission );
		if ( isset( $processed_submissions[ $submission_hash ] ) ) {
			return;
		}
		$processed_submissions[ $submission_hash ] = true;

		$form_title = $contact_form->title();

		if ( 'Daftar Jubelio' !== $form_title && 'Daftar Jubelio EN' !== $form_title && 'Download Publikasi' !== $form_title ) {
			return;
		}

		$posted_data = $submission->get_posted_data();

		$nama_toko    = isset( $posted_data['NamaToko'] ) ? trim( $posted_data['NamaToko'] ) : 'Unknown Company';
		$nama_lengkap = isset( $posted_data['NamaLengkap'] ) ? trim( $posted_data['NamaLengkap'] ) : 'Unknown Contact';
		$phone        = isset( $posted_data['phone'] ) ? trim( $posted_data['phone'] ) : '';
		$email        = isset( $posted_data['email'] ) ? trim( $posted_data['email'] ) : '';

		// 2. Cross-request debounce guard (prevent rapid double-click submissions within 15 seconds)
		if ( ! empty( $email ) || ! empty( $phone ) ) {
			$clean_phone = preg_replace( '/\D/', '', $phone );
			$dedup_key   = 'jubelio_b24_sub_' . md5( strtolower( $email ) . '|' . $clean_phone . '|' . $form_title );
			if ( false !== get_transient( $dedup_key ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( '[Jubelio Bitrix24] Duplicate submission blocked: ' . $email . ' / ' . $phone );
				}
				return;
			}
			set_transient( $dedup_key, time(), 15 );
		}

		$bitrix_orderan = isset( $posted_data['bitrix_orderan'] ) ? $posted_data['bitrix_orderan'] : '';
		$ref_id        = isset( $posted_data['ref_id'] ) ? sanitize_text_field( $posted_data['ref_id'] ) : '';
		$referral_link = isset( $posted_data['ref_url'] ) ? sanitize_text_field( $posted_data['ref_url'] ) : 'Direct or Unknown';

		// Clean up any legacy or escaped DirectorUnknown strings
		if ( empty( $referral_link ) || 'http://DirectorUnknown' === $referral_link || false !== strpos( $referral_link, 'DirectorUnknown' ) ) {
			$referral_link = 'Direct or Unknown';
		}

		// Security: Validate URL to prevent protocol hijacking (javascript:) and open redirects
		if ( 'Direct or Unknown' !== $referral_link ) {
			$validated_url = wp_http_validate_url( $referral_link );
			$referral_link = $validated_url ? esc_url_raw( $validated_url ) : 'Direct or Unknown';
		}

		$first_url    = isset( $posted_data['first_url'] ) ? sanitize_text_field( $posted_data['first_url'] ) : '';
		$bitrix_trace = isset( $posted_data['b24_trace'] ) ? sanitize_text_field( $posted_data['b24_trace'] ) : '';

		$locale_val = self::resolve_locale( $ref_id, $form_title );

		// Standard UTM tracking fields
		$utm_source   = isset( $posted_data['utm_source'] ) ? sanitize_text_field( $posted_data['utm_source'] ) : '';
		$utm_medium   = isset( $posted_data['utm_medium'] ) ? sanitize_text_field( $posted_data['utm_medium'] ) : '';
		$utm_campaign = isset( $posted_data['utm_campaign'] ) ? sanitize_text_field( $posted_data['utm_campaign'] ) : '';
		$utm_content  = isset( $posted_data['utm_content'] ) ? sanitize_text_field( $posted_data['utm_content'] ) : '';
		$utm_term     = isset( $posted_data['utm_term'] ) ? sanitize_text_field( $posted_data['utm_term'] ) : '';

		// 1. Create or match company in Bitrix24
		$company_id = Bitrix24::add_company( $nama_toko, $phone, $email );

		// 2. Prepare Lead fields
		$lead_fields = array(
			'TITLE'             => $nama_toko . ' - ' . $nama_lengkap,
			'NAME'              => $nama_lengkap,
			'STATUS_ID'         => 'UC_P5VBGE',
			'SOURCE_ID'         => '12',
			'TRACE'             => $bitrix_trace,
			'OPENED'            => 'Y',
			'PHONE'             => ! empty( $phone ) ? array( array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ) ) : array(),
			'EMAIL'             => ! empty( $email ) ? array( array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ) ) : array(),

			'UF_CRM_1771383453' => $bitrix_orderan,
			'UF_CRM_1776150772' => $ref_id,
			'UF_CRM_1776150786' => $referral_link,
			'UF_CRM_1776150757' => $locale_val,

			'UTM_SOURCE'        => $utm_source,
			'UTM_MEDIUM'        => $utm_medium,
			'UTM_CAMPAIGN'      => $utm_campaign,
			'UTM_CONTENT'       => $utm_content,
			'UTM_TERM'          => $utm_term,
		);

		if ( $first_url ) {
			$lead_fields['UF_CRM_1786678759'] = $first_url;
		}

		if ( $company_id ) {
			$lead_fields['COMPANY_ID'] = $company_id;
		}

		// Extra fields for Daftar Jubelio
		if ( 'Daftar Jubelio' === $form_title || 'Daftar Jubelio EN' === $form_title ) {
			$bitrix_provinsi = isset( $posted_data['bitrix_provinsi'] ) ? $posted_data['bitrix_provinsi'] : '';
			$bitrix_kategori = isset( $posted_data['bitrix_kategori'] ) ? $posted_data['bitrix_kategori'] : '';

			$sumber_tahu_text = isset( $posted_data['darimanataujubelio'] ) ? $posted_data['darimanataujubelio'] : '';
			if ( is_array( $sumber_tahu_text ) ) {
				$sumber_tahu_text = isset( $sumber_tahu_text[0] ) ? $sumber_tahu_text[0] : '';
			}
			$bitrix_darimana_tahu = isset( self::SUMBER_TAHU_MAP[ $sumber_tahu_text ] ) ? self::SUMBER_TAHU_MAP[ $sumber_tahu_text ] : '';

			$produk_raw = isset( $posted_data['product'] ) ? $posted_data['product'] : array();
			if ( ! is_array( $produk_raw ) ) {
				$produk_raw = array( $produk_raw );
			}

			$bitrix_produk_array = array();
			foreach ( $produk_raw as $prod ) {
				$prod_clean = trim( $prod );
				if ( isset( self::PRODUK_MAP[ $prod_clean ] ) ) {
					$bitrix_produk_array[] = self::PRODUK_MAP[ $prod_clean ];
				}
			}

			$lead_fields['UF_CRM_1771557371'] = $bitrix_kategori;
			$lead_fields['UF_CRM_1772507718'] = $bitrix_produk_array;
			$lead_fields['UF_CRM_1771557061'] = $bitrix_provinsi;
			$lead_fields['UF_CRM_1771557606'] = $bitrix_darimana_tahu;
		}

		// 3. Create Lead in Bitrix24
		Bitrix24::add_lead( $lead_fields );
	}

	/**
	 * CF7 special mail tag: [publikasi_file_url]
	 *
	 * Replaces tag in email notifications with the ACF e-book_file URL from the referral post.
	 *
	 * @param string $output Default output.
	 * @param string $name Tag name.
	 * @param bool   $html Whether HTML is enabled.
	 * @return string File download URL.
	 */
	public static function publikasi_file_mail_tag( $output, $name, $html ) {
		if ( 'publikasi_file_url' !== $name ) {
			return $output;
		}

		if ( ! class_exists( 'WPCF7_Submission' ) ) {
			return $output;
		}

		$submission = WPCF7_Submission::get_instance();
		if ( ! $submission ) {
			return $output;
		}

		$posted_data = $submission->get_posted_data();
		$ref_id      = isset( $posted_data['ref_id'] ) ? intval( $posted_data['ref_id'] ) : 0;

		if ( $ref_id && function_exists( 'get_field' ) ) {
			$file_data = get_field( 'e-book_file', $ref_id );

			if ( is_array( $file_data ) && isset( $file_data['url'] ) ) {
				return $file_data['url'];
			} elseif ( is_string( $file_data ) && ! empty( $file_data ) ) {
				return $file_data;
			}
		}

		return '#';
	}

	/**
	 * Shortcode [get_data_from_url_id param="ref_id" field="e-book_file"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Resolved data from referral post.
	 */
	public static function shortcode_get_data_from_url_id( $atts ) {
		$atts = shortcode_atts( array(
			'param' => 'ref_id',
			'field' => 'e-book_file',
		), $atts );

		$post_id = isset( $_GET[ $atts['param'] ] ) ? intval( $_GET[ $atts['param'] ] ) : 0;
		if ( ! $post_id ) {
			return '';
		}

		switch ( $atts['field'] ) {
			case 'title':
				return get_the_title( $post_id );

			case 'image':
				return (string) get_the_post_thumbnail_url( $post_id, 'full' );

			default:
				if ( function_exists( 'get_field' ) ) {
					$file_data = get_field( $atts['field'], $post_id );
					if ( is_array( $file_data ) && isset( $file_data['url'] ) ) {
						return $file_data['url'];
					}
					return (string) $file_data;
				}
				return '';
		}
	}

	/**
	 * Prevent duplicate CF7 submissions from double clicks or rapid retries.
	 * Blocks duplicate processing at validation stage before CFDB7 or mail fires.
	 *
	 * @param \WPCF7_Validation $result Validation result object.
	 * @param array             $tags Form tags.
	 * @return \WPCF7_Validation
	 */
	public static function prevent_duplicate_submission( $result, $tags ) {
		if ( ! is_object( $result ) || ! method_exists( $result, 'is_valid' ) || ! $result->is_valid() ) {
			return $result;
		}

		if ( ! class_exists( 'WPCF7_Submission' ) ) {
			return $result;
		}

		$submission = \WPCF7_Submission::get_instance();
		if ( ! $submission ) {
			return $result;
		}

		$posted_data = $submission->get_posted_data();
		$email       = isset( $posted_data['email'] ) ? trim( $posted_data['email'] ) : '';
		$phone       = isset( $posted_data['phone'] ) ? trim( $posted_data['phone'] ) : '';

		if ( empty( $email ) && empty( $phone ) ) {
			return $result;
		}

		$clean_phone = preg_replace( '/\D/', '', $phone );
		$lock_key    = 'jubelio_cf7_sub_lock_' . md5( strtolower( $email ) . '|' . $clean_phone );

		if ( false !== get_transient( $lock_key ) ) {
			$result->invalidate( 'email', __( 'Formulir Anda sedang diproses, mohon tunggu sebentar.', 'hello-elementor-child' ) );
			return $result;
		}

		set_transient( $lock_key, time(), 15 );

		return $result;
	}

	/**
	 * Strip b24_trace from Contact Form DB 7 storage to avoid database bloat
	 *
	 * @param array $form_data Form data before database save.
	 * @return array Form data without b24_trace.
	 */
	public static function exclude_trace_from_cfdb7( $form_data ) {
		if ( isset( $form_data['b24_trace'] ) ) {
			unset( $form_data['b24_trace'] );
		}

		return $form_data;
	}
}
