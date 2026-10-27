<?php
/**
 * Data Model / Transformer: Cerita Pebisnis
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Model {

	/**
	 * Extract and normalize data for a Cerita Pebisnis post
	 *
	 * @param int   $post_id Post ID.
	 * @param array $field_keys Optional custom field mappings.
	 * @return array Normalized post data.
	 */
	public static function get( $post_id, $field_keys = array() ) {
		$data = array(
			'id'            => $post_id,
			'title'         => get_the_title( $post_id ),
			'permalink'     => get_permalink( $post_id ),
			'headline'      => '',
			'description'   => '',
			'company_logo'  => '',
			'card_image'    => '',
			'client_name'   => '',
			'cta_link'      => '',
			'video_url'     => '',
			'industry_name' => '',
			'industry_slug' => '',
		);

		// Helper to resolve ACF field with fallback key list
		$get_val = function( $custom_key, $fallback_keys ) use ( $post_id ) {
			if ( ! empty( $custom_key ) && function_exists( 'get_field' ) ) {
				$val = get_field( $custom_key, $post_id );
				if ( ! empty( $val ) ) {
					return $val;
				}
			}
			if ( function_exists( 'get_field' ) ) {
				foreach ( $fallback_keys as $k ) {
					$val = get_field( $k, $post_id );
					if ( ! empty( $val ) ) {
						return $val;
					}
				}
			}
			return null;
		};

		// 1. Headline / Solution (e.g. "Optimalkan Stok dari Banyak Channel")
		$headline_val = $get_val(
			isset( $field_keys['headline'] ) ? $field_keys['headline'] : '',
			array( 'cp_headline', 'headline', 'subjudul', 'highlight', 'solusi', 'tagline', 'ringkasan_solusi' )
		);
		$data['headline'] = ! empty( $headline_val ) ? esc_html( $headline_val ) : '';

		// 2. Description (excerpt or content or ACF)
		$desc_val = $get_val(
			isset( $field_keys['description'] ) ? $field_keys['description'] : '',
			array( 'cp_deskripsi', 'deskripsi', 'description', 'cerita_singkat', 'short_description', 'ringkasan' )
		);
		if ( ! empty( $desc_val ) ) {
			$data['description'] = is_string( $desc_val ) ? $desc_val : '';
		} else {
			$excerpt = get_the_excerpt( $post_id );
			$data['description'] = ! empty( $excerpt ) ? $excerpt : wp_trim_words( get_post_field( 'post_content', $post_id ), 26 );
		}

		// 3. Company Logo (array, id, or url)
		$logo_val = $get_val(
			isset( $field_keys['company_logo'] ) ? $field_keys['company_logo'] : '',
			array( 'cp_logo_bisnis', 'logo_bisnis', 'logo', 'logo_toko', 'brand_logo', 'client_logo', 'company_logo' )
		);
		if ( is_array( $logo_val ) && isset( $logo_val['url'] ) ) {
			$data['company_logo'] = $logo_val['url'];
		} elseif ( is_numeric( $logo_val ) ) {
			$data['company_logo'] = wp_get_attachment_image_url( $logo_val, 'full' );
		} elseif ( is_string( $logo_val ) ) {
			$data['company_logo'] = $logo_val;
		}

		// 4. Card Main Image (Store / Warehouse photo)
		$img_val = $get_val(
			isset( $field_keys['card_image'] ) ? $field_keys['card_image'] : '',
			array( 'cp_foto_toko', 'foto_toko', 'store_image', 'card_image', 'gambar_utama', 'foto_gudang' )
		);
		if ( is_array( $img_val ) && isset( $img_val['url'] ) ) {
			$data['card_image'] = $img_val['url'];
		} elseif ( is_numeric( $img_val ) ) {
			$data['card_image'] = wp_get_attachment_image_url( $img_val, 'large' );
		} elseif ( is_string( $img_val ) && ! empty( $img_val ) ) {
			$data['card_image'] = $img_val;
		} elseif ( has_post_thumbnail( $post_id ) ) {
			$data['card_image'] = get_the_post_thumbnail_url( $post_id, 'large' );
		}

		// 5. CTA Link URL (defaults to the post's own URL; custom_link preserved if ACF field is set)
		$link_val = $get_val(
			isset( $field_keys['cta_link'] ) ? $field_keys['cta_link'] : '',
			array( 'cp_link_konsultasi', 'link_konsultasi', 'cta_link', 'url_konsultasi', 'link_cerita' )
		);
		$data['custom_link'] = ! empty( $link_val ) ? esc_url( $link_val ) : '';
		$data['cta_link']    = ! empty( $data['permalink'] ) ? $data['permalink'] : get_permalink( $post_id );

		// Video URL (YouTube, Vimeo, or self-hosted MP4)
		$video_val = $get_val(
			isset( $field_keys['video_url'] ) ? $field_keys['video_url'] : '',
			array( 'cp_video_url', 'video_url', 'youtube_url', 'video', 'link_video' )
		);
		$data['video_url'] = ! empty( $video_val ) ? esc_url( $video_val ) : '';

		// 6. Industry Taxonomy ('industri')
		$terms = get_the_terms( $post_id, 'industri' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$data['industry_name'] = $terms[0]->name;
			$data['industry_slug'] = $terms[0]->slug;
		} else {
			$ind_val = $get_val( '', array( 'industri', 'kategori', 'kategori_bisnis' ) );
			if ( ! empty( $ind_val ) ) {
				$data['industry_name'] = is_string( $ind_val ) ? esc_html( $ind_val ) : '';
				$data['industry_slug'] = sanitize_title( $data['industry_name'] );
			}
		}

		// 7. Client Name / Brand Name
		$name_val = $get_val(
			isset( $field_keys['client_name'] ) ? $field_keys['client_name'] : '',
			array( 'nama_bisnis', 'nama_toko', 'client_name', 'nama_klien', 'brand_name' )
		);
		$data['client_name'] = ! empty( $name_val ) ? esc_html( $name_val ) : $data['title'];

		// 8. Client Quote (Kutipan Langsung)
		$quote_val = $get_val(
			isset( $field_keys['quote'] ) ? $field_keys['quote'] : '',
			array( 'cp_quote', 'quote', 'kutipan', 'testimoni', 'client_quote', 'testimonial_quote' )
		);
		if ( ! empty( $quote_val ) ) {
			$data['quote'] = is_string( $quote_val ) ? trim( $quote_val ) : '';
		} else {
			// Fallback to description
			$data['quote'] = ! empty( $data['description'] ) ? wp_strip_all_tags( $data['description'] ) : '';
		}

		// 9. Client Person / Spokesperson Name (e.g. Hansa Artupas)
		$person_name_val = $get_val(
			isset( $field_keys['client_person_name'] ) ? $field_keys['client_person_name'] : '',
			array( 'cp_client_name', 'client_person_name', 'nama_tokoh', 'narasumber', 'person_name' )
		);
		$data['client_person_name'] = ! empty( $person_name_val ) ? esc_html( $person_name_val ) : $data['client_name'];

		// 10. Client Person Role / Business Title (e.g. Owner Cuit BabyWear)
		$person_role_val = $get_val(
			isset( $field_keys['client_person_role'] ) ? $field_keys['client_person_role'] : '',
			array( 'cp_client_role', 'client_person_role', 'jabatan', 'role', 'posisi' )
		);
		if ( ! empty( $person_role_val ) ) {
			$data['client_person_role'] = esc_html( $person_role_val );
		} else {
			$data['client_person_role'] = ! empty( $data['client_name'] ) ? 'Owner ' . $data['client_name'] : ( ! empty( $data['industry_name'] ) ? $data['industry_name'] : 'Klien Jubelio' );
		}

		// 11. Location
		$loc_val = $get_val( '', array( 'cp_lokasi', 'lokasi', 'lokasi_klien', 'client_location' ) );
		$data['location'] = ! empty( $loc_val ) ? esc_html( $loc_val ) : 'Indonesia';

		// 12. Custom Industry Override
		$ind_custom = $get_val( '', array( 'cp_industri_custom', 'cp_industri', 'industri_custom' ) );
		$data['industry_display'] = ! empty( $ind_custom ) ? esc_html( $ind_custom ) : ( ! empty( $data['industry_name'] ) ? $data['industry_name'] : 'Bisnis Retail' );

		// 13. Products Used (Repeater)
		$data['products_used'] = array();
		if ( function_exists( 'get_field' ) ) {
			$raw_prods = get_field( 'cp_produk_list', $post_id );
			if ( ! empty( $raw_prods ) && is_array( $raw_prods ) ) {
				foreach ( $raw_prods as $p ) {
					$p_name = ! empty( $p['nama_produk'] ) ? trim( $p['nama_produk'] ) : '';
					$p_link = ! empty( $p['link_produk'] ) ? trim( $p['link_produk'] ) : '#';
					if ( ! empty( $p_name ) ) {
						$data['products_used'][] = array(
							'name' => $p_name,
							'link' => $p_link,
						);
					}
				}
			}
		}

		// 14. Features Used (Repeater)
		$data['features_used'] = array();
		if ( function_exists( 'get_field' ) ) {
			$raw_feats = get_field( 'cp_fitur_list', $post_id );
			if ( ! empty( $raw_feats ) && is_array( $raw_feats ) ) {
				foreach ( $raw_feats as $f ) {
					$f_name = ! empty( $f['nama_fitur'] ) ? trim( $f['nama_fitur'] ) : '';
					$f_link = ! empty( $f['link_fitur'] ) ? trim( $f['link_fitur'] ) : '#';
					if ( ! empty( $f_name ) ) {
						$data['features_used'][] = array(
							'name' => $f_name,
							'link' => $f_link,
						);
					}
				}
			}
		}

		// 15. Overview Text
		$overview_val = $get_val( '', array( 'cp_overview_klien', 'cp_overview', 'overview_klien', 'cerita_lengkap' ) );
		$data['overview'] = ! empty( $overview_val ) ? wp_kses_post( $overview_val ) : $data['description'];

		// 16. Problems (Deskripsi & List)
		$prob_desc = $get_val( '', array( 'cp_masalah_deskripsi', 'masalah_deskripsi', 'problem_description' ) );
		$data['problem_desc'] = ! empty( $prob_desc ) ? wp_kses_post( $prob_desc ) : '';
		$data['problem_list'] = array();
		if ( function_exists( 'get_field' ) ) {
			$raw_probs = get_field( 'cp_masalah_list', $post_id );
			if ( ! empty( $raw_probs ) && is_array( $raw_probs ) ) {
				foreach ( $raw_probs as $item ) {
					$txt = is_array( $item ) && isset( $item['item'] ) ? trim( $item['item'] ) : ( is_string( $item ) ? trim( $item ) : '' );
					if ( ! empty( $txt ) ) {
						$data['problem_list'][] = esc_html( $txt );
					}
				}
			}
		}

		// 17. Solutions (Deskripsi & List)
		$sol_desc = $get_val( '', array( 'cp_solusi_deskripsi', 'solusi_deskripsi', 'solution_description' ) );
		$data['solution_desc'] = ! empty( $sol_desc ) ? wp_kses_post( $sol_desc ) : '';
		$data['solution_list'] = array();
		if ( function_exists( 'get_field' ) ) {
			$raw_sols = get_field( 'cp_solusi_list', $post_id );
			if ( ! empty( $raw_sols ) && is_array( $raw_sols ) ) {
				foreach ( $raw_sols as $item ) {
					$txt = is_array( $item ) && isset( $item['item'] ) ? trim( $item['item'] ) : ( is_string( $item ) ? trim( $item ) : '' );
					if ( ! empty( $txt ) ) {
						$data['solution_list'][] = esc_html( $txt );
					}
				}
			}
		}

		// 18. Result Metrics ("Hasil Nyata Transformasi")
		$data['result_metrics'] = array();
		if ( function_exists( 'get_field' ) ) {
			$raw_metrics = get_field( 'cp_metrik_hasil_list', $post_id );
			if ( empty( $raw_metrics ) ) {
				$raw_metrics = get_field( 'cp_hasil_transformasi_list', $post_id );
			}
			if ( ! empty( $raw_metrics ) && is_array( $raw_metrics ) ) {
				foreach ( $raw_metrics as $m ) {
					$lbl = ! empty( $m['label_metrik'] ) ? trim( $m['label_metrik'] ) : ( ! empty( $m['label'] ) ? trim( $m['label'] ) : '' );
					$val = ! empty( $m['nilai_metrik'] ) ? trim( $m['nilai_metrik'] ) : ( ! empty( $m['value'] ) ? trim( $m['value'] ) : '' );

					$icon_url = '';
					$icon_field = ! empty( $m['ikon_metrik'] ) ? $m['ikon_metrik'] : ( ! empty( $m['icon'] ) ? $m['icon'] : '' );
					if ( ! empty( $icon_field ) ) {
						if ( is_array( $icon_field ) && ! empty( $icon_field['url'] ) ) {
							$icon_url = esc_url( $icon_field['url'] );
						} elseif ( is_numeric( $icon_field ) ) {
							$icon_url = wp_get_attachment_image_url( $icon_field, 'full' );
						} elseif ( is_string( $icon_field ) ) {
							$icon_url = esc_url( $icon_field );
						}
					}

					if ( ! empty( $lbl ) || ! empty( $val ) ) {
						$data['result_metrics'][] = array(
							'label'    => esc_html( $lbl ),
							'value'    => esc_html( $val ),
							'icon_url' => $icon_url,
						);
					}
				}
			}
		}

		// Default fallback sample metrics if none set
		if ( empty( $data['result_metrics'] ) ) {
			$data['result_metrics'] = array(
				array(
					'label' => 'Stock Accuracy',
					'value' => '96% → 97%',
				),
				array(
					'label' => 'Picking Performance',
					'value' => '>60%',
				),
				array(
					'label' => 'Putaway Output',
					'value' => '+68,95% /hari',
				),
				array(
					'label' => 'Lebih Efisien',
					'value' => '4x',
				),
			);
		}

		return $data;
	}
}
