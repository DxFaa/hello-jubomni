<?php
/**
 * Custom Post Type: Cerita Pebisnis (Client Testimonials & Success Stories)
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post type & taxonomy as safe fallback if not already registered via ACF Pro UI.
 */
add_action( 'init', function() {
	// Register Category / Industry taxonomy if not exists
	if ( ! taxonomy_exists( 'kategori_cerita_pebisnis' ) ) {
		register_taxonomy(
			'kategori_cerita_pebisnis',
			array( 'cerita-pebisnis' ),
			array(
				'labels'            => array(
					'name'          => __( 'Kategori Bisnis', 'hello-elementor-child' ),
					'singular_name' => __( 'Kategori Bisnis', 'hello-elementor-child' ),
					'search_items'  => __( 'Cari Kategori Bisnis', 'hello-elementor-child' ),
					'all_items'     => __( 'Semua Kategori Bisnis', 'hello-elementor-child' ),
					'edit_item'     => __( 'Edit Kategori Bisnis', 'hello-elementor-child' ),
					'update_item'   => __( 'Update Kategori Bisnis', 'hello-elementor-child' ),
					'add_new_item'  => __( 'Tambah Kategori Bisnis Baru', 'hello-elementor-child' ),
					'new_item_name' => __( 'Nama Kategori Bisnis Baru', 'hello-elementor-child' ),
					'menu_name'     => __( 'Kategori Bisnis', 'hello-elementor-child' ),
				),
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'kategori-cerita-pebisnis' ),
			)
		);
	}

	// Register Post Type if not exists
	if ( ! post_type_exists( 'cerita-pebisnis' ) ) {
		register_post_type(
			'cerita-pebisnis',
			array(
				'labels'             => array(
					'name'               => _x( 'Cerita Pebisnis', 'post type general name', 'hello-elementor-child' ),
					'singular_name'      => _x( 'Cerita Pebisnis', 'post type singular name', 'hello-elementor-child' ),
					'add_new'            => _x( 'Tambah Baru', 'cerita-pebisnis', 'hello-elementor-child' ),
					'add_new_item'       => __( 'Tambah Cerita Pebisnis Baru', 'hello-elementor-child' ),
					'edit_item'          => __( 'Edit Cerita Pebisnis', 'hello-elementor-child' ),
					'new_item'           => __( 'Cerita Pebisnis Baru', 'hello-elementor-child' ),
					'view_item'          => __( 'Lihat Cerita Pebisnis', 'hello-elementor-child' ),
					'search_items'       => __( 'Cari Cerita Pebisnis', 'hello-elementor-child' ),
					'not_found'          => __( 'Tidak ada Cerita Pebisnis ditemukan', 'hello-elementor-child' ),
					'not_found_in_trash' => __( 'Tidak ada Cerita Pebisnis di Kotak Sampah', 'hello-elementor-child' ),
					'menu_name'          => __( 'Cerita Pebisnis', 'hello-elementor-child' ),
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug'       => 'cerita-pebisnis',
					'with_front' => false,
				),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-format-chat',
				'show_in_rest'       => true,
				'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
				'taxonomies'         => array( 'kategori_cerita_pebisnis' ),
			)
		);
	}
}, 5 );

/**
 * Universal Data Extractor for Cerita Pebisnis
 * Smartly pulls values from custom ACF fields, fallback ACF field names, or native WordPress post data.
 *
 * @param int   $post_id Post ID.
 * @param array $field_keys Optional array of custom ACF keys configured in Elementor.
 * @return array Normalized testimonial data.
 */
function jubelio_get_cerita_pebisnis_data( $post_id, $field_keys = array() ) {
	$data = array(
		'id'              => $post_id,
		'title'           => get_the_title( $post_id ),
		'permalink'       => get_permalink( $post_id ),
		'client_name'     => '',
		'client_role'     => '',
		'company_name'    => '',
		'company_logo'    => '',
		'client_photo'    => '',
		'quote'           => '',
		'metric_badge'    => '',
		'rating'          => 5,
		'video_url'       => '',
		'category_name'   => '',
		'category_slug'   => '',
	);

	// Helper to get ACF field with fallback list
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

	// 1. Client Name
	$client_name = $get_val(
		isset( $field_keys['client_name'] ) ? $field_keys['client_name'] : '',
		array( 'client_name', 'nama_klien', 'nama_pebisnis', 'client_person', 'pebisnis' )
	);
	$data['client_name'] = ! empty( $client_name ) ? esc_html( $client_name ) : esc_html( $data['title'] );

	// 2. Client Role / Position
	$client_role = $get_val(
		isset( $field_keys['client_role'] ) ? $field_keys['client_role'] : '',
		array( 'client_role', 'jabatan', 'role', 'posisi', 'job_title' )
	);
	$data['client_role'] = ! empty( $client_role ) ? esc_html( $client_role ) : '';

	// 3. Company / Store Name
	$company_name = $get_val(
		isset( $field_keys['company_name'] ) ? $field_keys['company_name'] : '',
		array( 'company_name', 'nama_toko', 'nama_bisnis', 'nama_perusahaan', 'brand_name', 'company' )
	);
	$data['company_name'] = ! empty( $company_name ) ? esc_html( $company_name ) : '';

	// 4. Company Logo (array, id, or url)
	$logo_val = $get_val(
		isset( $field_keys['company_logo'] ) ? $field_keys['company_logo'] : '',
		array( 'company_logo', 'logo_bisnis', 'logo_toko', 'logo_perusahaan', 'brand_logo', 'client_logo' )
	);
	if ( is_array( $logo_val ) && isset( $logo_val['url'] ) ) {
		$data['company_logo'] = $logo_val['url'];
	} elseif ( is_numeric( $logo_val ) ) {
		$data['company_logo'] = wp_get_attachment_image_url( $logo_val, 'full' );
	} elseif ( is_string( $logo_val ) ) {
		$data['company_logo'] = $logo_val;
	}

	// 5. Client Photo (array, id, url, or featured image fallback)
	$photo_val = $get_val(
		isset( $field_keys['client_photo'] ) ? $field_keys['client_photo'] : '',
		array( 'client_photo', 'foto_klien', 'foto_pebisnis', 'avatar', 'photo' )
	);
	if ( is_array( $photo_val ) && isset( $photo_val['url'] ) ) {
		$data['client_photo'] = $photo_val['url'];
	} elseif ( is_numeric( $photo_val ) ) {
		$data['client_photo'] = wp_get_attachment_image_url( $photo_val, 'full' );
	} elseif ( is_string( $photo_val ) && ! empty( $photo_val ) ) {
		$data['client_photo'] = $photo_val;
	} elseif ( has_post_thumbnail( $post_id ) ) {
		$data['client_photo'] = get_the_post_thumbnail_url( $post_id, 'medium_large' );
	}

	// 6. Testimonial Quote
	$quote_val = $get_val(
		isset( $field_keys['quote'] ) ? $field_keys['quote'] : '',
		array( 'quote', 'kutipan', 'testimoni', 'testimonial', 'cerita_singkat', 'short_story' )
	);
	if ( ! empty( $quote_val ) ) {
		$data['quote'] = is_string( $quote_val ) ? $quote_val : '';
	} else {
		$excerpt = get_the_excerpt( $post_id );
		$data['quote'] = ! empty( $excerpt ) ? $excerpt : wp_trim_words( get_post_field( 'post_content', $post_id ), 28 );
	}

	// 7. Metric / Highlight Badge (e.g., "+300% Efisiensi", "10.000 Order / Hari")
	$metric_val = $get_val(
		isset( $field_keys['metric_badge'] ) ? $field_keys['metric_badge'] : '',
		array( 'metric_badge', 'stat_highlight', 'hasil_utama', 'key_result', 'highlight_metric', 'pencapaian' )
	);
	$data['metric_badge'] = ! empty( $metric_val ) ? esc_html( $metric_val ) : '';

	// 8. Rating (defaults to 5)
	$rating_val = $get_val(
		isset( $field_keys['rating'] ) ? $field_keys['rating'] : '',
		array( 'rating', 'star_rating', 'bintang', 'nilai' )
	);
	$data['rating'] = ( ! empty( $rating_val ) && is_numeric( $rating_val ) ) ? min( 5, max( 1, floatval( $rating_val ) ) ) : 5;

	// 9. Video URL (YouTube, Vimeo, MP4)
	$video_val = $get_val(
		isset( $field_keys['video_url'] ) ? $field_keys['video_url'] : '',
		array( 'video_url', 'youtube_url', 'link_video', 'video_interview' )
	);
	$data['video_url'] = ! empty( $video_val ) ? esc_url( $video_val ) : '';

	// 10. Category / Industry
	$terms = get_the_terms( $post_id, 'kategori_cerita_pebisnis' );
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$data['category_name'] = $terms[0]->name;
		$data['category_slug'] = $terms[0]->slug;
	} else {
		$cat_field = $get_val(
			isset( $field_keys['category'] ) ? $field_keys['category'] : '',
			array( 'kategori', 'industry', 'kategori_bisnis', 'bidang_usaha' )
		);
		if ( ! empty( $cat_field ) ) {
			$data['category_name'] = esc_html( $cat_field );
			$data['category_slug'] = sanitize_title( $cat_field );
		}
	}

	return $data;
}
