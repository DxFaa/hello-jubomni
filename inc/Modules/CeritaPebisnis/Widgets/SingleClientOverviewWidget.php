<?php
/**
 * Elementor Widget: Cerita Pebisnis Single Post Client Profile & Problem-Solution Section
 *
 * Displays:
 * 1. "Profil Klien" Heading
 * 2. Spec Box: Brand Logo, Lokasi, Industri, Produk Jubelio yang Digunakan (dynamic links), Fitur yang Digunakan (dynamic links)
 * 3. Overview Paragraph
 * 4. 2-Column Problem & Solution Cards (Masalah with red X items, Solusi with green checkmark items)
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Jubelio\Modules\CeritaPebisnis\Model;

class SingleClientOverviewWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_client_overview';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis: Profil & Solusi', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'profil', 'klien', 'masalah', 'solusi', 'problem', 'solution', 'cerita pebisnis', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	/**
	 * Register Widget Controls
	 */
	protected function register_controls() {

		// ==================== SECTION: PROFIL KLIEN ====================
		$this->start_controls_section(
			'section_profile_content',
			array(
				'label' => __( 'Bagian Profil Klien', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_profile_section',
			array(
				'label'        => __( 'Tampilkan Bagian Profil Klien', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'profile_section_title',
			array(
				'label'       => __( 'Judul Bagian', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Profil Klien', 'hello-elementor-child' ),
				'placeholder' => __( 'Profil Klien', 'hello-elementor-child' ),
				'condition'   => array(
					'show_profile_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'profile_title_tag',
			array(
				'label'   => __( 'HTML Tag Judul', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
				),
				'condition' => array(
					'show_profile_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'profile_data_source',
			array(
				'label'       => __( 'Sumber Data Profil', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'dynamic',
				'description' => __( 'Dinamis otomatis mengambil dari ACF Post (cp_logo_bisnis, cp_lokasi, taksonomi industri, dll.)', 'hello-elementor-child' ),
				'options'     => array(
					'dynamic' => __( 'Dinamis (ACF & Data Post)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom (Atur Manual di Elementor)', 'hello-elementor-child' ),
				),
				'condition'   => array(
					'show_profile_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_client_logo',
			array(
				'label'     => __( 'Unggah Logo Klien', 'hello-elementor-child' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_location_label',
			array(
				'label'     => __( 'Label Lokasi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Lokasi', 'hello-elementor-child' ),
				'condition' => array(
					'show_profile_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_location_value',
			array(
				'label'     => __( 'Nilai Lokasi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Indonesia',
				'condition' => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_industry_label',
			array(
				'label'     => __( 'Label Industri', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Industri', 'hello-elementor-child' ),
				'condition' => array(
					'show_profile_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_industry_value',
			array(
				'label'     => __( 'Nilai Industri', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Fashion & Apparel',
				'condition' => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		// Produk Digunakan Repeater
		$prod_repeater = new Repeater();
		$prod_repeater->add_control(
			'product_name',
			array(
				'label'       => __( 'Nama Produk', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Jubelio Omnichannel',
				'label_block' => true,
			)
		);
		$prod_repeater->add_control(
			'product_link',
			array(
				'label'       => __( 'Link Halaman Produk', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/omnichannel/',
				'default'     => array(
					'url' => '/omnichannel/',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_products_list',
			array(
				'label'       => __( 'Daftar Produk Jubelio yang Digunakan', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $prod_repeater->get_controls(),
				'title_field' => '{{{ product_name }}}',
				'default'     => array(
					array(
						'product_name' => 'Jubelio Omnichannel',
						'product_link' => array( 'url' => '/omnichannel/' ),
					),
					array(
						'product_name' => 'Jubelio POS',
						'product_link' => array( 'url' => '/pos/' ),
					),
					array(
						'product_name' => 'Jubelio Shipment',
						'product_link' => array( 'url' => '/shipment/' ),
					),
				),
				'condition'   => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		// Fitur Digunakan Repeater
		$feat_repeater = new Repeater();
		$feat_repeater->add_control(
			'feature_name',
			array(
				'label'       => __( 'Nama Fitur', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Inventory',
				'label_block' => true,
			)
		);
		$feat_repeater->add_control(
			'feature_link',
			array(
				'label'       => __( 'Link Halaman Fitur', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/fitur/inventory/',
				'default'     => array(
					'url' => '/fitur/inventory/',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_features_list',
			array(
				'label'       => __( 'Daftar Fitur yang Digunakan', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $feat_repeater->get_controls(),
				'title_field' => '{{{ feature_name }}}',
				'default'     => array(
					array(
						'feature_name' => 'Inventory',
						'feature_link' => array( 'url' => '/fitur/inventory/' ),
					),
					array(
						'feature_name' => 'Gudang (WMS)',
						'feature_link' => array( 'url' => '/fitur/gudang-wms/' ),
					),
					array(
						'feature_name' => 'Pesanan',
						'feature_link' => array( 'url' => '/fitur/pesanan/' ),
					),
				),
				'condition'   => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_overview_text',
			array(
				'label'       => __( 'Paragraf Overview Klien (Kustom)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Kisah inspiratif bagaimana klien berhasil mengoptimalkan operasional pergudangan, integrasi multi-channel, dan mempercepat pemrosesan ribuan pesanan harian bersama Jubelio.',
				'condition'   => array(
					'show_profile_section' => 'yes',
					'profile_data_source'  => 'custom',
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: MASALAH & SOLUSI ====================
		$this->start_controls_section(
			'section_problem_solution_content',
			array(
				'label' => __( 'Bagian Masalah & Solusi', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_problem_solution_section',
			array(
				'label'        => __( 'Tampilkan Masalah & Solusi', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prob_sol_data_source',
			array(
				'label'       => __( 'Sumber Data Masalah & Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'dynamic',
				'description' => __( 'Dinamis otomatis mengambil dari ACF Post (cp_masalah_deskripsi, cp_masalah_list, cp_solusi_deskripsi, cp_solusi_list)', 'hello-elementor-child' ),
				'options'     => array(
					'dynamic' => __( 'Dinamis (ACF Post)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom (Atur Manual di Elementor)', 'hello-elementor-child' ),
				),
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
				),
			)
		);

		// --- KARTU MASALAH ---
		$this->add_control(
			'heading_problem_card',
			array(
				'label'     => __( 'Pengaturan Kartu Masalah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_problem_solution_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'problem_title',
			array(
				'label'       => __( 'Judul Masalah', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Masalah', 'hello-elementor-child' ),
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_problem_desc',
			array(
				'label'       => __( 'Deskripsi Ringkas Masalah', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Sebelum menggunakan Jubelio, pengelolaan stok dilakukan terpisah di masing-masing platform sehingga rawan terjadi overselling dan selisih data.',
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
					'prob_sol_data_source'          => 'custom',
				),
			)
		);

		$problem_item_repeater = new Repeater();
		$problem_item_repeater->add_control(
			'item_text',
			array(
				'label'       => __( 'Poin Masalah', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Pencatatan inventori manual memakan waktu lama',
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_problem_list',
			array(
				'label'       => __( 'Daftar Poin Masalah (List Merah)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $problem_item_repeater->get_controls(),
				'title_field' => '{{{ item_text }}}',
				'default'     => array(
					array( 'item_text' => 'Stok sering selisih antar channel marketplace' ),
					array( 'item_text' => 'Proses fulfillment pesanan lambat dan tidak efisien' ),
					array( 'item_text' => 'Kesulitan memantau performa penjualan secara real-time' ),
					array( 'item_text' => 'Pencatatan laporan keuangan memakan waktu berhari-hari' ),
				),
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
					'prob_sol_data_source'          => 'custom',
				),
			)
		);

		// --- KARTU SOLUSI ---
		$this->add_control(
			'heading_solution_card',
			array(
				'label'     => __( 'Pengaturan Kartu Solusi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_problem_solution_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'solution_title',
			array(
				'label'       => __( 'Judul Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Solusi dari Jubelio', 'hello-elementor-child' ),
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_solution_desc',
			array(
				'label'       => __( 'Deskripsi Ringkas Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Jubelio menyediakan sistem otomatisasi terpusat untuk menghubungkan seluruh channel penjualan, sinkronisasi stok real-time, dan manajemen gudang modern.',
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
					'prob_sol_data_source'          => 'custom',
				),
			)
		);

		$solution_item_repeater = new Repeater();
		$solution_item_repeater->add_control(
			'item_text',
			array(
				'label'       => __( 'Poin Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Otomatisasi sinkronisasi inventori multi-channel',
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_solution_list',
			array(
				'label'       => __( 'Daftar Poin Solusi (List Hijau)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $solution_item_repeater->get_controls(),
				'title_field' => '{{{ item_text }}}',
				'default'     => array(
					array( 'item_text' => 'Sinkronisasi inventori otomatis ke seluruh marketplace' ),
					array( 'item_text' => 'Pemrosesan pesanan massal cepat dalam hitungan detik' ),
					array( 'item_text' => 'Visibilitas data analitik penjualan terpusat secara real-time' ),
					array( 'item_text' => 'Laporan keuangan dan akuntansi terintegrasi otomatis' ),
				),
				'condition'   => array(
					'show_problem_solution_section' => 'yes',
					'prob_sol_data_source'          => 'custom',
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE: SECTION STYLES ====================
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Gaya Kartu & Warna', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'spec_card_bg',
			array(
				'label'     => __( 'Background Kartu Profil Klien', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F2F2F7',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-client-spec-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'problem_card_bg',
			array(
				'label'     => __( 'Background Kartu Masalah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFF2F0',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-card-problem' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solution_card_bg',
			array(
				'label'     => __( 'Background Kartu Solusi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F4F8FD',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-card-solution' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cards_border_radius',
			array(
				'label'      => __( 'Lengkungan Sudut Kartu (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-client-spec-card, {{WRAPPER}} .jubelio-card-problem, {{WRAPPER}} .jubelio-card-solution' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Widget Output
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = get_the_ID();
		$post_data = Model::get( $post_id );

		// 1. Resolve Profile Data
		$is_custom_profile = ( 'custom' === $settings['profile_data_source'] );

		// Logo
		$logo_url = '';
		if ( $is_custom_profile && ! empty( $settings['custom_client_logo']['url'] ) ) {
			$logo_url = $settings['custom_client_logo']['url'];
		} else {
			$logo_url = ! empty( $post_data['company_logo'] ) ? $post_data['company_logo'] : '';
		}

		// Location
		$location = '';
		if ( $is_custom_profile && ! empty( $settings['custom_location_value'] ) ) {
			$location = $settings['custom_location_value'];
		} else {
			$location = ! empty( $post_data['location'] ) ? $post_data['location'] : 'Indonesia';
		}

		// Industry
		$industry = '';
		if ( $is_custom_profile && ! empty( $settings['custom_industry_value'] ) ) {
			$industry = $settings['custom_industry_value'];
		} else {
			$industry = ! empty( $post_data['industry_display'] ) ? $post_data['industry_display'] : ( ! empty( $post_data['industry_name'] ) ? $post_data['industry_name'] : 'Bisnis Retail' );
		}

		// Products Used
		$products = array();
		if ( $is_custom_profile && ! empty( $settings['custom_products_list'] ) ) {
			foreach ( $settings['custom_products_list'] as $item ) {
				if ( ! empty( $item['product_name'] ) ) {
					$products[] = array(
						'name' => $item['product_name'],
						'link' => ! empty( $item['product_link']['url'] ) ? $item['product_link']['url'] : '#',
					);
				}
			}
		} elseif ( ! empty( $post_data['products_used'] ) ) {
			$products = $post_data['products_used'];
		} else {
			// Fallback sample products
			$products = array(
				array( 'name' => 'Jubelio Omnichannel', 'link' => '/omnichannel/' ),
				array( 'name' => 'Jubelio POS', 'link' => '/pos/' ),
				array( 'name' => 'Jubelio Shipment', 'link' => '/shipment/' ),
			);
		}

		// Features Used
		$features = array();
		if ( $is_custom_profile && ! empty( $settings['custom_features_list'] ) ) {
			foreach ( $settings['custom_features_list'] as $item ) {
				if ( ! empty( $item['feature_name'] ) ) {
					$features[] = array(
						'name' => $item['feature_name'],
						'link' => ! empty( $item['feature_link']['url'] ) ? $item['feature_link']['url'] : '#',
					);
				}
			}
		} elseif ( ! empty( $post_data['features_used'] ) ) {
			$features = $post_data['features_used'];
		} else {
			// Fallback sample features
			$features = array(
				array( 'name' => 'Inventory', 'link' => '/fitur/inventory/' ),
				array( 'name' => 'Gudang (WMS)', 'link' => '/fitur/gudang-wms/' ),
				array( 'name' => 'Pesanan', 'link' => '/fitur/pesanan/' ),
			);
		}

		// Overview Text
		$overview = '';
		if ( $is_custom_profile && ! empty( $settings['custom_overview_text'] ) ) {
			$overview = $settings['custom_overview_text'];
		} else {
			$overview = ! empty( $post_data['overview'] ) ? $post_data['overview'] : ( ! empty( $post_data['description'] ) ? $post_data['description'] : '' );
		}

		// 2. Resolve Problems & Solutions Data
		$is_custom_probsol = ( 'custom' === $settings['prob_sol_data_source'] );

		// Problem Card
		$prob_title = ! empty( $settings['problem_title'] ) ? $settings['problem_title'] : __( 'Masalah', 'hello-elementor-child' );
		$prob_desc  = '';
		$prob_list  = array();
		if ( $is_custom_probsol ) {
			$prob_desc = ! empty( $settings['custom_problem_desc'] ) ? $settings['custom_problem_desc'] : '';
			if ( ! empty( $settings['custom_problem_list'] ) ) {
				foreach ( $settings['custom_problem_list'] as $p_item ) {
					if ( ! empty( $p_item['item_text'] ) ) {
						$prob_list[] = $p_item['item_text'];
					}
				}
			}
		} else {
			$prob_desc = ! empty( $post_data['problem_desc'] ) ? $post_data['problem_desc'] : '';
			$prob_list = ! empty( $post_data['problem_list'] ) ? $post_data['problem_list'] : array();
		}

		if ( empty( $prob_desc ) && empty( $prob_list ) ) {
			$prob_desc = __( 'Sebelum menggunakan Jubelio, bisnis menghadapi berbagai kendala dalam operasional pengelolaan inventori dan pemrosesan pesanan multi-channel.', 'hello-elementor-child' );
			$prob_list = array(
				__( 'Stok sering selisih antar channel marketplace', 'hello-elementor-child' ),
				__( 'Proses fulfillment pesanan lambat dan tidak efisien', 'hello-elementor-child' ),
				__( 'Kesulitan memantau performa penjualan secara real-time', 'hello-elementor-child' ),
				__( 'Pencatatan laporan keuangan memakan waktu berhari-hari', 'hello-elementor-child' ),
			);
		}

		// Solution Card
		$sol_title = ! empty( $settings['solution_title'] ) ? $settings['solution_title'] : __( 'Solusi dari Jubelio', 'hello-elementor-child' );
		$sol_desc  = '';
		$sol_list  = array();
		if ( $is_custom_probsol ) {
			$sol_desc = ! empty( $settings['custom_solution_desc'] ) ? $settings['custom_solution_desc'] : '';
			if ( ! empty( $settings['custom_solution_list'] ) ) {
				foreach ( $settings['custom_solution_list'] as $s_item ) {
					if ( ! empty( $s_item['item_text'] ) ) {
						$sol_list[] = $s_item['item_text'];
					}
				}
			}
		} else {
			$sol_desc = ! empty( $post_data['solution_desc'] ) ? $post_data['solution_desc'] : '';
			$sol_list = ! empty( $post_data['solution_list'] ) ? $post_data['solution_list'] : array();
		}

		if ( empty( $sol_desc ) && empty( $sol_list ) ) {
			$sol_desc = __( 'Jubelio menyediakan platform omnichannel terpadu untuk menyederhanakan rantai operasional gudang, pesanan, dan keuangan secara instan.', 'hello-elementor-child' );
			$sol_list = array(
				__( 'Sinkronisasi inventori otomatis ke seluruh marketplace', 'hello-elementor-child' ),
				__( 'Pemrosesan pesanan massal cepat dalam hitungan detik', 'hello-elementor-child' ),
				__( 'Visibilitas data analitik penjualan terpusat secara real-time', 'hello-elementor-child' ),
				__( 'Laporan keuangan dan akuntansi terintegrasi otomatis', 'hello-elementor-child' ),
			);
		}

		$title_tag = ! empty( $settings['profile_title_tag'] ) ? esc_attr( $settings['profile_title_tag'] ) : 'h2';
		?>
		<div class="jubelio-client-overview-section">
			<div class="jubelio-client-overview-inner">

				<?php if ( 'yes' === $settings['show_profile_section'] ) : ?>
					<?php if ( ! empty( $settings['profile_section_title'] ) ) : ?>
						<<?php echo $title_tag; ?> class="jubelio-client-profile-title">
							<?php echo esc_html( $settings['profile_section_title'] ); ?>
						</<?php echo $title_tag; ?>>
					<?php endif; ?>

					<div class="jubelio-client-spec-card">
						
						<?php if ( ! empty( $logo_url ) ) : ?>
							<div class="jubelio-spec-col jubelio-spec-col-logo">
								<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $post_data['client_name'] ); ?>" class="jubelio-spec-logo-img" loading="lazy" decoding="async" />
							</div>
						<?php else : ?>
							<div class="jubelio-spec-col jubelio-spec-col-logo">
								<span class="jubelio-spec-logo-text"><?php echo esc_html( $post_data['client_name'] ); ?></span>
							</div>
						<?php endif; ?>

						<div class="jubelio-spec-details">

							<div class="jubelio-spec-col jubelio-spec-col-info">
								<span class="jubelio-spec-label"><?php echo esc_html( $settings['custom_location_label'] ); ?></span>
								<p class="jubelio-spec-value"><?php echo esc_html( $location ); ?></p>
							</div>

							<div class="jubelio-spec-col jubelio-spec-col-info">
								<span class="jubelio-spec-label"><?php echo esc_html( $settings['custom_industry_label'] ); ?></span>
								<p class="jubelio-spec-value"><?php echo esc_html( $industry ); ?></p>
							</div>

							<?php if ( ! empty( $products ) ) : ?>
								<div class="jubelio-spec-col jubelio-spec-col-list">
									<span class="jubelio-spec-label"><?php esc_html_e( 'Produk Jubelio yang Digunakan', 'hello-elementor-child' ); ?></span>
									<ul class="jubelio-spec-links-list">
										<?php foreach ( $products as $prod ) : 
											$p_name = ! empty( $prod['name'] ) ? $prod['name'] : '';
											$p_link = ! empty( $prod['link'] ) ? $prod['link'] : '#';
										?>
											<li class="jubelio-spec-link-item">
												<a href="<?php echo esc_url( $p_link ); ?>" class="jubelio-product-link">
													<span><?php echo esc_html( $p_name ); ?></span>
													<span class="jubelio-btn-arrow" aria-hidden="true">
														<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
															<line x1="5" y1="12" x2="19" y2="12"></line>
															<polyline points="12 5 19 12 12 19"></polyline>
														</svg>
													</span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $features ) ) : ?>
								<div class="jubelio-spec-col jubelio-spec-col-list">
									<span class="jubelio-spec-label"><?php esc_html_e( 'Fitur yang Digunakan', 'hello-elementor-child' ); ?></span>
									<ul class="jubelio-spec-links-list">
										<?php foreach ( $features as $feat ) : 
											$f_name = ! empty( $feat['name'] ) ? $feat['name'] : '';
											$f_link = ! empty( $feat['link'] ) ? $feat['link'] : '#';
										?>
											<li class="jubelio-spec-link-item">
												<a href="<?php echo esc_url( $f_link ); ?>" class="jubelio-feature-link">
													<span><?php echo esc_html( $f_name ); ?></span>
													<span class="jubelio-btn-arrow" aria-hidden="true">
														<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
															<line x1="5" y1="12" x2="19" y2="12"></line>
															<polyline points="12 5 19 12 12 19"></polyline>
														</svg>
													</span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

						</div>

					</div>

					<?php if ( ! empty( $overview ) ) : ?>
						<div class="jubelio-client-overview-text">
							<p><?php echo esc_html( $overview ); ?></p>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_problem_solution_section'] ) : ?>
					<div class="jubelio-prob-sol-grid">
						
						<div class="jubelio-prob-sol-card jubelio-card-problem">
							<h3 class="jubelio-prob-sol-title"><?php echo esc_html( $prob_title ); ?></h3>
							
							<?php if ( ! empty( $prob_desc ) ) : ?>
								<p class="jubelio-prob-sol-desc"><?php echo esc_html( $prob_desc ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $prob_list ) ) : ?>
								<ul class="jubelio-prob-sol-list">
									<?php foreach ( $prob_list as $item_text ) : ?>
										<li class="jubelio-prob-sol-item">
											<span class="jubelio-status-icon icon-problem" aria-hidden="true">
												<svg viewBox="0 0 20 20" width="18" height="18" fill="none" focusable="false">
													<circle cx="10" cy="10" r="10" fill="#EF4444"/>
													<path d="M7 7l6 6M13 7l-6 6" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
												</svg>
											</span>
											<span class="jubelio-item-text"><?php echo esc_html( $item_text ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

						<div class="jubelio-prob-sol-card jubelio-card-solution">
							<h3 class="jubelio-prob-sol-title"><?php echo esc_html( $sol_title ); ?></h3>
							
							<?php if ( ! empty( $sol_desc ) ) : ?>
								<p class="jubelio-prob-sol-desc"><?php echo esc_html( $sol_desc ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $sol_list ) ) : ?>
								<ul class="jubelio-prob-sol-list">
									<?php foreach ( $sol_list as $item_text ) : ?>
										<li class="jubelio-prob-sol-item">
											<span class="jubelio-status-icon icon-solution" aria-hidden="true">
												<svg viewBox="0 0 20 20" width="18" height="18" fill="none" focusable="false">
													<circle cx="10" cy="10" r="10" fill="#12B76A"/>
													<path d="M6 10l3 3 5-6" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</span>
											<span class="jubelio-item-text"><?php echo esc_html( $item_text ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
