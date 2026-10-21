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

		return $data;
	}
}
