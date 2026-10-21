<?php
/**
 * Demo Data Seeder for Cerita Pebisnis
 * Matches Jubelio Wireframe: Cuit Baby Wear, Clowny, Rodeo Junior
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Seeder {

	/**
	 * Initialize seeder hooks
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_notices', array( __CLASS__, 'display_admin_notice' ) );
		add_action( 'admin_post_jubelio_seed_cerita_pebisnis', array( __CLASS__, 'handle_seed_request' ) );
	}

	/**
	 * Display admin notice on Cerita Pebisnis screen when 0 posts exist
	 */
	public static function display_admin_notice() {
		global $pagenow, $post_type;

		if ( 'edit.php' !== $pagenow || 'cerita-pebisnis' !== $post_type ) {
			return;
		}

		// Success notice after seeding
		if ( isset( $_GET['seeded'] ) && '1' === $_GET['seeded'] ) {
			?>
			<div class="notice notice-success is-dismissible" style="padding: 12px 18px; border-left-color: #4B68EF;">
				<p style="font-size: 14px; font-weight: 600; color: #163A50; margin: 0;">
					🎉 <?php esc_html_e( 'Sukses! 3 postingan demo Cerita Pebisnis (Cuit Baby Wear, Clowny, Rodeo Junior) berhasil dibuat beserta kategori Industri dan ACF field. Anda sekarang dapat melihat preview lengkapnya di Elementor!', 'hello-elementor-child' ); ?>
				</p>
			</div>
			<?php
			return;
		}

		// Check count of published posts
		$counts = wp_count_posts( 'cerita-pebisnis' );
		$published = isset( $counts->publish ) ? intval( $counts->publish ) : 0;

		if ( 0 === $published ) {
			$seed_url = wp_nonce_url(
				admin_url( 'admin-post.php?action=jubelio_seed_cerita_pebisnis' ),
				'jubelio_seed_action',
				'jubelio_nonce'
			);
			?>
			<div class="notice notice-info" style="padding: 14px 18px; border-left: 4px solid #4B68EF; background: #FFFFFF; box-shadow: 0 4px 12px rgba(22, 58, 80, 0.06); border-radius: 4px; margin-top: 15px; margin-bottom: 20px;">
				<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
					<div>
						<h3 style="margin: 0 0 4px 0; color: #163A50; font-size: 15px; font-weight: 700;">
							🚀 <?php esc_html_e( 'Belum ada postingan Cerita Pebisnis', 'hello-elementor-child' ); ?>
						</h3>
						<p style="margin: 0; color: #606C75; font-size: 13px; line-height: 1.5;">
							<?php esc_html_e( 'Ingin mengisi 3 data demo (Cuit Baby Wear, Clowny, Rodeo Junior) secara otomatis sesuai wireframe agar kartu langsung tampil di Elementor?', 'hello-elementor-child' ); ?>
						</p>
					</div>
					<div>
						<a href="<?php echo esc_url( $seed_url ); ?>" class="button button-primary" style="background: #4B68EF; border-color: #3350D9; font-weight: 700; padding: 4px 16px; height: auto; font-size: 13px; border-radius: 6px;">
							+ <?php esc_html_e( 'Buat 3 Postingan Demo', 'hello-elementor-child' ); ?>
						</a>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Handle seed action triggered from admin notice
	 */
	public static function handle_seed_request() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Unauthorized user.', 'hello-elementor-child' ) );
		}

		if ( ! isset( $_GET['jubelio_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['jubelio_nonce'] ) ), 'jubelio_seed_action' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'hello-elementor-child' ) );
		}

		self::seed();

		wp_safe_redirect( admin_url( 'edit.php?post_type=cerita-pebisnis&seeded=1' ) );
		exit;
	}

	/**
	 * Seed demo data into WordPress
	 */
	public static function seed() {
		// 1. Ensure Taxonomy terms exist
		$terms = array(
			'Pakaian Bayi & Anak' => 'pakaian-bayi-anak',
			'Fashion'             => 'fashion',
			'Fashion & Apparel'   => 'fashion-apparel',
		);

		$term_ids = array();
		foreach ( $terms as $name => $slug ) {
			$existing = get_term_by( 'slug', $slug, 'industri' );
			if ( ! $existing ) {
				$inserted = wp_insert_term( $name, 'industri', array( 'slug' => $slug ) );
				if ( ! is_wp_error( $inserted ) ) {
					$term_ids[ $slug ] = $inserted['term_id'];
				}
			} else {
				$term_ids[ $slug ] = $existing->term_id;
			}
		}

		// 2. Demo Posts matching Wireframe
		$posts = array(
			array(
				'title'     => 'Cuit Baby Wear',
				'slug'      => 'cuit-baby-wear',
				'industry'  => 'pakaian-bayi-anak',
				'headline'  => 'Optimalkan Stok dari Banyak Channel',
				'desc'      => 'Cuit Baby Wear meningkatkan efisiensi pengelolaan stok untuk berbagai channel penjualan dan operasional gudang dengan Jubelio.',
				'cta_link'  => '',
			),
			array(
				'title'     => 'Clowny',
				'slug'      => 'clowny',
				'industry'  => 'fashion',
				'headline'  => 'Kelola Ribuan Pesanan Tanpa Selisih Stok',
				'desc'      => 'Clowny berhasil memproses ribuan order harian dari berbagai marketplace secara otomatis dan real-time bersama Jubelio.',
				'cta_link'  => '',
			),
			array(
				'title'     => 'Rodeo Junior',
				'slug'      => 'rodeo-junior',
				'industry'  => 'fashion-apparel',
				'headline'  => 'Sinkronisasi Multi-Store Online & Offline',
				'desc'      => 'Rodeo Junior mengintegrasikan sistem POS toko fisik dengan marketplace online secara mulus dalam satu dashboard Jubelio Omnichannel.',
				'cta_link'  => '',
			),
		);

		foreach ( $posts as $p ) {
			// Check if post already exists by slug
			$existing_post = get_page_by_path( $p['slug'], OBJECT, 'cerita-pebisnis' );
			if ( $existing_post ) {
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_title'   => sanitize_text_field( $p['title'] ),
				'post_name'    => sanitize_title( $p['slug'] ),
				'post_content' => wp_kses_post( $p['desc'] ),
				'post_excerpt' => wp_kses_post( $p['desc'] ),
				'post_status'  => 'publish',
				'post_type'    => 'cerita-pebisnis',
			) );

			if ( ! empty( $post_id ) && ! is_wp_error( $post_id ) ) {
				// Assign taxonomy
				if ( isset( $term_ids[ $p['industry'] ] ) ) {
					wp_set_object_terms( $post_id, array( intval( $term_ids[ $p['industry'] ] ) ), 'industri' );
				}

				// Populate ACF Fields & post meta
				if ( function_exists( 'update_field' ) ) {
					update_field( 'field_cp_headline', $p['headline'], $post_id );
					update_field( 'field_cp_deskripsi', $p['desc'], $post_id );
					update_field( 'field_cp_link_konsultasi', $p['cta_link'], $post_id );
				}

				// Fallback standard post meta (both cp_ prefix and legacy)
				update_post_meta( $post_id, 'cp_headline', sanitize_text_field( $p['headline'] ) );
				update_post_meta( $post_id, 'cp_deskripsi', sanitize_textarea_field( $p['desc'] ) );
				update_post_meta( $post_id, 'cp_link_konsultasi', esc_url_raw( $p['cta_link'] ) );
				update_post_meta( $post_id, 'headline', sanitize_text_field( $p['headline'] ) );
				update_post_meta( $post_id, 'deskripsi', sanitize_textarea_field( $p['desc'] ) );
				update_post_meta( $post_id, 'link_konsultasi', esc_url_raw( $p['cta_link'] ) );
			}
		}
	}
}
