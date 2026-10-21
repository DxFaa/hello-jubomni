<?php
/**
 * Elementor Widget: Cerita Pebisnis Cards Grid (Filterable by Industri)
 * Matches Jubelio Wireframe Section: "PILIH BERDASARKAN INDUSTRI"
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Jubelio\Modules\CeritaPebisnis\Model;

class GridWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_grid';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis Grid (by Industri)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'cerita pebisnis', 'industri', 'testimoni', 'testimonial', 'grid', 'case study', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	/**
	 * Get industry terms for dropdown control
	 */
	protected function get_industry_options() {
		$options = array( '' => __( 'Semua Industri', 'hello-elementor-child' ) );
		$terms   = get_terms( array(
			'taxonomy'   => 'industri',
			'hide_empty' => false,
		) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		return $options;
	}

	/**
	 * Register Widget Controls
	 */
	protected function register_controls() {

		// ==================== SECTION: HEADER & FILTER ====================
		$this->start_controls_section(
			'section_header_filter',
			array(
				'label' => __( 'Header & Filter Industri', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_eyebrow',
			array(
				'label'        => __( 'Tampilkan Eyebrow / Subjudul', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'eyebrow_text',
			array(
				'label'       => __( 'Teks Eyebrow', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'PILIH BERDASARKAN INDUSTRI',
				'placeholder' => 'PILIH BERDASARKAN INDUSTRI',
				'condition'   => array(
					'show_eyebrow' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_filter_tabs',
			array(
				'label'        => __( 'Tampilkan Filter Tab Industri', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'filter_all_label',
			array(
				'label'     => __( 'Label Tab Semua', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Semua', 'hello-elementor-child' ),
				'condition' => array(
					'show_filter_tabs' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'filter_align',
			array(
				'label'     => __( 'Perataan Filter Bar', 'hello-elementor-child' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Kiri (Left)', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Tengah (Center)', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Kanan (Right)', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'condition' => array(
					'show_filter_tabs' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-bar-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'preview_sample_data',
			array(
				'label'        => __( 'Tampilkan Demo Preview Jika Kosong', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Menampilkan 3 kartu sampel sesuai wireframe ketika belum ada postingan Cerita Pebisnis.', 'hello-elementor-child' ),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: QUERY ====================
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query Cerita Pebisnis', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Jumlah Kartu Ditampilkan', 'hello-elementor-child' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 30,
				'step'    => 1,
			)
		);

		$this->add_control(
			'filter_industry_slug',
			array(
				'label'   => __( 'Filter Industri Tertentu (Opsional)', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_industry_options(),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Urutkan Berdasarkan', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => __( 'Tanggal Terbaru', 'hello-elementor-child' ),
					'title'      => __( 'Nama Brand (A-Z)', 'hello-elementor-child' ),
					'menu_order' => __( 'Menu Order', 'hello-elementor-child' ),
					'rand'       => __( 'Acak (Random)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Arah Urutan', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => __( 'Descending (Z-A / Baru ke Lama)', 'hello-elementor-child' ),
					'ASC'  => __( 'Ascending (A-Z / Lama ke Baru)', 'hello-elementor-child' ),
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: LAYOUT & SLIDER NAVBAR ====================
		$this->start_controls_section(
			'section_layout_slider',
			array(
				'label' => __( 'Tipe Layout & Slider Navbar', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'layout_type',
			array(
				'label'   => __( 'Tipe Tampilan Layout', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slider',
				'options' => array(
					'slider' => __( 'Touch Slider & Carousel (Recommended)', 'hello-elementor-child' ),
					'grid'   => __( 'Classic Static Grid', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'show_navigation',
			array(
				'label'        => __( 'Tampilkan Tombol Navigasi Panah (Prev/Next)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Menampilkan tombol panah di sisi kiri & kanan kartu (desktop & tablet) dan di navbar bawah (mobile).', 'hello-elementor-child' ),
				'condition'    => array(
					'layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Tampilkan Indikator Halaman (Dots)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'layout_type' => 'slider',
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: GRID & CARD CONTENT ====================
		$this->start_controls_section(
			'section_card_elements',
			array(
				'label' => __( 'Struktur & Elemen Kartu', 'hello-elementor-child' ),
			)
		);

		$this->add_responsive_control(
			'grid_columns',
			array(
				'label'          => __( 'Jumlah Kolom Grid', 'hello-elementor-child' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1 Kolom',
					'2' => '2 Kolom',
					'3' => '3 Kolom',
					'4' => '4 Kolom',
				),
			)
		);

		$this->add_control(
			'show_image',
			array(
				'label'        => __( 'Tampilkan Foto / Gambar Toko', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_industry_badge',
			array(
				'label'        => __( 'Tampilkan Badge Industri di Foto', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_image' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_logo',
			array(
				'label'        => __( 'Tampilkan Logo Brand / Toko', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_brand_title',
			array(
				'label'        => __( 'Tampilkan Nama Brand / Toko', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_headline',
			array(
				'label'        => __( 'Tampilkan Headline Solusi', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_description',
			array(
				'label'        => __( 'Tampilkan Deskripsi Cerita', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_cta_link',
			array(
				'label'        => __( 'Tampilkan Link Aksi (CTA)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Teks Link CTA', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Baca Selengkapnya',
				'condition' => array(
					'show_cta_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_link_target',
			array(
				'label'       => __( 'Tujuan Link CTA', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'permalink',
				'options'     => array(
					'permalink'    => __( 'URL Halaman Cerita (Post Permalink - Default)', 'hello-elementor-child' ),
					'custom_field' => __( 'Field ACF Khusus (cp_link_konsultasi)', 'hello-elementor-child' ),
					'custom_url'   => __( 'URL Eksternal Kustom (Global)', 'hello-elementor-child' ),
				),
				'description' => __( 'Secara default mengarah langsung ke URL postingan masing-masing (misal: /cerita-pebisnis/cuit-baby-wear/).', 'hello-elementor-child' ),
				'condition'   => array(
					'show_cta_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_cta_url',
			array(
				'label'       => __( 'URL Eksternal Kustom (Global)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://jubelio.com/demo atau link WhatsApp',
				'description' => __( 'Digunakan jika Tujuan Link CTA diset ke "URL Eksternal Kustom (Global)".', 'hello-elementor-child' ),
				'condition'   => array(
					'show_cta_link'   => 'yes',
					'cta_link_target' => 'custom_url',
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: ACF FIELD MAPPING ====================
		$this->start_controls_section(
			'section_acf_keys',
			array(
				'label' => __( 'Pemetaan Field ACF Pro', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'acf_info',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<p style="margin:0; font-size:12px; line-height:1.4;">Widget otomatis mendeteksi field standar ACF Pro. Isi jika Anda menggunakan field name khusus.</p>', 'hello-elementor-child' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'field_headline',
			array(
				'label'       => __( 'Field Name: Headline / Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'cp_headline (default)',
			)
		);

		$this->add_control(
			'field_company_logo',
			array(
				'label'       => __( 'Field Name: Logo Brand', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'cp_logo_bisnis (default)',
			)
		);

		$this->add_control(
			'field_card_image',
			array(
				'label'       => __( 'Field Name: Foto Toko (jika bukan Featured Image)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'cp_foto_toko (default)',
			)
		);

		$this->add_control(
			'field_description',
			array(
				'label'       => __( 'Field Name: Deskripsi Cerita', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'cp_deskripsi (default)',
			)
		);

		$this->add_control(
			'field_cta_link',
			array(
				'label'       => __( 'Field Name: Link Khusus Kartu', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'cp_link_konsultasi (default)',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: EYEBROW & TABS ====================
		// ==================== STYLE TAB: EYEBROW & UNIFIED PILLS ====================
		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => __( 'Eyebrow & Unified Filter Pills', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Warna Eyebrow', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .jubelio-grid-eyebrow',
			)
		);

		$this->add_control(
			'pill_container_heading',
			array(
				'label'     => __( 'Segmented Pill Container', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pill_container_bg',
			array(
				'label'     => __( 'Background Container Pill', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EEF2F6',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pill_container_padding',
			array(
				'label'      => __( 'Padding Container Pill', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '5',
					'right'    => '6',
					'bottom'   => '5',
					'left'     => '6',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-filter-pill-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pill_active_heading',
			array(
				'label'     => __( 'Tombol Pill Aktif (Active State - Only One Active)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pill_active_bg',
			array(
				'label'     => __( 'Background Pill Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4361EE',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn.active' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'pill_active_color',
			array(
				'label'     => __( 'Warna Teks Pill Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn.active' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'pill_default_heading',
			array(
				'label'     => __( 'Tombol Pill Default & Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pill_inactive_color',
			array(
				'label'     => __( 'Warna Teks Default', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5A6B82',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn:not(.active)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_hover_color',
			array(
				'label'     => __( 'Warna Teks (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn:not(.active):hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_hover_bg',
			array(
				'label'     => __( 'Background (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn:not(.active):hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pill_typography',
				'selector' => '{{WRAPPER}} .jubelio-filter-pill-btn',
			)
		);

		$this->add_responsive_control(
			'pill_btn_padding',
			array(
				'label'      => __( 'Padding Tombol Pill', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '24',
					'bottom'   => '10',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-filter-pill-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: SLIDER NAVBAR & CONTROLS ====================
		$this->start_controls_section(
			'section_style_slider_nav',
			array(
				'label'     => __( 'Slider Navbar & Controls', 'hello-elementor-child' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'nav_btn_color',
			array(
				'label'     => __( 'Warna Ikon Panah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-slider-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_btn_bg',
			array(
				'label'     => __( 'Background Tombol Panah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-slider-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_btn_hover_bg',
			array(
				'label'     => __( 'Background Panah (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-slider-btn:hover:not(:disabled)' => 'background-color: {{VALUE}}; border-color: {{VALUE}}; color: #FFFFFF;',
				),
			)
		);

		$this->add_responsive_control(
			'nav_side_offset',
			array(
				'label'      => __( 'Jarak Tombol Panah dari Sisi (Offset)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 40,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => -72,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => -32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-side-prev' => 'left: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .jubelio-side-next' => 'right: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'layout_type'     => 'slider',
					'show_navigation' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'nav_btn_size',
			array(
				'label'      => __( 'Ukuran Tombol Panah (Diameter)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 32,
						'max'  => 64,
						'step' => 2,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-slider-btn' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; min-width: {{SIZE}}{{UNIT}} !important;',
				),
				'condition'  => array(
					'layout_type'     => 'slider',
					'show_navigation' => 'yes',
				),
			)
		);

		$this->add_control(
			'dot_active_color',
			array(
				'label'     => __( 'Warna Dot Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: KARTU (CARD) ====================
		$this->start_controls_section(
			'section_style_card',
			array(
				'label' => __( 'Kartu (Card Box)', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background Kartu', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Sudut Melengkung (Radius)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-grid-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .jubelio-grid-card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .jubelio-grid-card',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TYPOGRAPHY & COLORS ====================
		$this->start_controls_section(
			'section_style_typography',
			array(
				'label' => __( 'Tipografi & Warna Konten Kartu', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'brand_title_heading',
			array(
				'label' => __( 'Nama Brand / Toko', 'hello-elementor-child' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'brand_title_color',
			array(
				'label'     => __( 'Warna Nama Brand', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card-brand' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'brand_title_typography',
				'selector' => '{{WRAPPER}} .jubelio-grid-card-brand',
			)
		);

		$this->add_control(
			'headline_heading',
			array(
				'label'     => __( 'Headline Solusi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => __( 'Warna Headline', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card-headline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .jubelio-grid-card-headline',
			)
		);

		$this->add_control(
			'desc_heading',
			array(
				'label'     => __( 'Deskripsi Cerita', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Warna Deskripsi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#606C75',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .jubelio-grid-card-desc',
			)
		);

		$this->add_control(
			'cta_heading',
			array(
				'label'     => __( 'Link CTA Konsultasi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Warna Link CTA', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_hover_color',
			array(
				'label'     => __( 'Warna Link CTA (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3350D9',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-grid-card-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Direct Native Render with View Delegation
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Query Args
		$query_args = array(
			'post_type'      => 'cerita-pebisnis',
			'post_status'    => 'publish',
			'posts_per_page' => intval( $settings['posts_per_page'] ),
			'orderby'        => sanitize_key( $settings['orderby'] ),
			'order'          => sanitize_text_field( $settings['order'] ),
		);

		if ( ! empty( $settings['filter_industry_slug'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'industri',
					'field'    => 'slug',
					'terms'    => sanitize_title( $settings['filter_industry_slug'] ),
				),
			);
		}

		$query = new \WP_Query( $query_args );

		$is_sample_preview = false;
		$items             = array();
		$industries        = array();

		// Check if Elementor editor, preview mode, or fallback is enabled
		$is_editor    = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$is_preview   = is_preview() || ( isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() );
		$allow_sample = ( 'yes' === $settings['preview_sample_data'] );

		if ( ! $query->have_posts() ) {
			if ( $is_editor || $is_preview || $allow_sample ) {
				$is_sample_preview = true;
				$items             = $this->get_sample_items( $settings );
				$industries        = $this->get_sample_industries();
			} else {
				echo '<div class="jubelio-grid-empty"><p>' . esc_html__( 'Belum ada Cerita Pebisnis yang dipublikasikan.', 'hello-elementor-child' ) . '</p></div>';
				return;
			}
		} else {
			// Field mapping config
			$field_keys = array(
				'headline'     => $settings['field_headline'],
				'company_logo' => $settings['field_company_logo'],
				'card_image'   => $settings['field_card_image'],
				'description'  => $settings['field_description'],
				'cta_link'     => $settings['field_cta_link'],
			);

			$cta_target = ! empty( $settings['cta_link_target'] ) ? $settings['cta_link_target'] : 'permalink';

			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id   = get_the_ID();
				$item_data = Model::get( $post_id, $field_keys );

				// CTA Link Resolution: by default redirect to post permalink
				if ( 'custom_url' === $cta_target && ! empty( $settings['custom_cta_url'] ) ) {
					$item_data['cta_link'] = esc_url( $settings['custom_cta_url'] );
				} elseif ( 'custom_field' === $cta_target && ! empty( $item_data['custom_link'] ) ) {
					$item_data['cta_link'] = esc_url( $item_data['custom_link'] );
				} else {
					$item_data['cta_link'] = esc_url( get_permalink( $post_id ) );
				}

				$items[] = $item_data;

				if ( ! empty( $item_data['industry_slug'] ) ) {
					$industries[ $item_data['industry_slug'] ] = $item_data['industry_name'];
				}
			}
			wp_reset_postdata();

			// Query all available terms from 'industri' taxonomy
			$all_terms = get_terms( array(
				'taxonomy'   => 'industri',
				'hide_empty' => false,
			) );
			if ( ! empty( $all_terms ) && ! is_wp_error( $all_terms ) ) {
				foreach ( $all_terms as $t ) {
					$industries[ $t->slug ] = $t->name;
				}
			}
		}

		// Ensure $industries is never empty so the pill bar always renders
		if ( empty( $industries ) ) {
			$industries = $this->get_sample_industries();
		}

		// Layout and navigation guaranteed defaults
		$layout_type     = ! empty( $settings['layout_type'] ) ? $settings['layout_type'] : 'slider';
		$show_navigation = ! isset( $settings['show_navigation'] ) || 'yes' === $settings['show_navigation'];
		$show_pagination = ! isset( $settings['show_pagination'] ) || 'yes' === $settings['show_pagination'];

		// Delegate HTML rendering to the view template partial
		include dirname( __DIR__ ) . '/views/grid.php';
	}

	/**
	 * Provide realistic wireframe sample data when 0 posts exist
	 * Matches Jubelio Client Success Stories: Cuit Baby Wear, Clowny, Rodeo Junior, Aerostreet, Somethinc, Kopi Kenangan
	 *
	 * @param array $settings Widget settings.
	 * @return array Array of sample items.
	 */
	protected function get_sample_items( $settings ) {
		$cta_target = ! empty( $settings['cta_link_target'] ) ? $settings['cta_link_target'] : 'permalink';
		$global_cta = ! empty( $settings['custom_cta_url'] ) ? esc_url( $settings['custom_cta_url'] ) : '';

		// Dynamic link resolver: checks if actual post exists by slug, otherwise builds standard post URL
		$resolve_link = function( $slug ) use ( $cta_target, $global_cta ) {
			if ( 'custom_url' === $cta_target && ! empty( $global_cta ) ) {
				return $global_cta;
			}
			$existing = get_page_by_path( $slug, OBJECT, 'cerita-pebisnis' );
			if ( $existing && ! empty( $existing->ID ) ) {
				$permalink = get_permalink( $existing->ID );
				if ( ! empty( $permalink ) ) {
					return esc_url( $permalink );
				}
			}
			return esc_url( home_url( '/cerita-pebisnis/' . sanitize_title( $slug ) . '/' ) );
		};

		return array(
			array(
				'id'            => 1001,
				'title'         => 'Cuit Baby Wear',
				'client_name'   => 'Cuit Baby Wear',
				'headline'      => 'Optimalkan Stok dari Banyak Channel',
				'description'   => 'Cuit Baby Wear meningkatkan efisiensi pengelolaan stok untuk berbagai channel penjualan dan operasional gudang dengan Jubelio.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Pakaian Bayi & Anak',
				'industry_slug' => 'pakaian-bayi-anak',
				'cta_link'      => $resolve_link( 'cuit-baby-wear' ),
			),
			array(
				'id'            => 1002,
				'title'         => 'Clowny',
				'client_name'   => 'Clowny',
				'headline'      => 'Kelola Ribuan Pesanan Tanpa Selisih Stok',
				'description'   => 'Clowny berhasil memproses ribuan order harian dari berbagai marketplace secara otomatis dan real-time bersama Jubelio.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Fashion',
				'industry_slug' => 'fashion',
				'cta_link'      => $resolve_link( 'clowny' ),
			),
			array(
				'id'            => 1003,
				'title'         => 'Rodeo Junior',
				'client_name'   => 'Rodeo Junior',
				'headline'      => 'Sinkronisasi Multi-Store Online & Offline',
				'description'   => 'Rodeo Junior mengintegrasikan sistem POS toko fisik dengan marketplace online secara mulus dalam satu dashboard Jubelio Omnichannel.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Fashion & Apparel',
				'industry_slug' => 'fashion-apparel',
				'cta_link'      => $resolve_link( 'rodeo-junior' ),
			),
			array(
				'id'            => 1004,
				'title'         => 'Aerostreet',
				'client_name'   => 'Aerostreet',
				'headline'      => 'Fulfillment Otomatis Puluhan Ribu Pesanan',
				'description'   => 'Aerostreet memproses pesanan flash sale viral hingga puluhan ribu paket per hari dengan integrasi WMS & Omnichannel Jubelio.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Fashion',
				'industry_slug' => 'fashion',
				'cta_link'      => $resolve_link( 'aerostreet' ),
			),
			array(
				'id'            => 1005,
				'title'         => 'Somethinc',
				'client_name'   => 'Somethinc',
				'headline'      => 'Akurasi Stok & Integrasi Marketplace Tercepat',
				'description'   => 'Brand kecantikan nomor satu Somethinc mengelola inventaris real-time di seluruh platform e-commerce dan toko retail.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Kecantikan',
				'industry_slug' => 'kecantikan',
				'cta_link'      => $resolve_link( 'somethinc' ),
			),
			array(
				'id'            => 1006,
				'title'         => 'Kopi Kenangan',
				'client_name'   => 'Kopi Kenangan',
				'headline'      => 'Supply Chain Ratusan Outlet Terintegrasi',
				'description'   => 'Manajemen rantai pasok dan procurement bahan baku ratusan gerai kopi dipusatkan secara efisien dengan solusi ERP Jubelio.',
				'company_logo'  => '',
				'card_image'    => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80',
				'industry_name' => 'Food & Beverage',
				'industry_slug' => 'food-beverage',
				'cta_link'      => $resolve_link( 'kopi-kenangan' ),
			),
		);
	}

	/**
	 * Provide sample industry terms for filter tabs in preview mode
	 *
	 * @return array Slugs and labels.
	 */
	protected function get_sample_industries() {
		return array(
			'fashion'           => __( 'Fashion', 'hello-elementor-child' ),
			'fashion-apparel'   => __( 'Fashion & Apparel', 'hello-elementor-child' ),
			'pakaian-bayi-anak' => __( 'Pakaian Bayi & Anak', 'hello-elementor-child' ),
			'kecantikan'        => __( 'Kecantikan', 'hello-elementor-child' ),
			'food-beverage'     => __( 'Food & Beverage', 'hello-elementor-child' ),
		);
	}
}
