<?php
/**
 * Elementor Widget: Cerita Pebisnis Testimonials (Kata Pelanggan)
 * Touch Slider with Dynamic Pagination & Centered Active Card
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
use Elementor\Group_Control_Border;
use Jubelio\Modules\CeritaPebisnis\Model;

class TestimonialsWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_testimonials';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis Testimonials (Kata Pelanggan)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'testimonials', 'testimoni', 'quote', 'kata pelanggan', 'cerita pebisnis', 'jubelio', 'slider' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	/**
	 * Retrieve list of all published Cerita Pebisnis posts for multi-select control
	 */
	protected function get_posts_options() {
		$options = array();
		$posts   = get_posts( array(
			'post_type'      => 'cerita-pebisnis',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		if ( ! empty( $posts ) && ! is_wp_error( $posts ) ) {
			foreach ( $posts as $p ) {
				$options[ $p->ID ] = $p->post_title;
			}
		}

		return $options;
	}

	/**
	 * Register Widget Controls
	 */
	protected function register_controls() {

		// ==================== SECTION: HEADER COPY ====================
		$this->start_controls_section(
			'section_header_content',
			array(
				'label' => __( 'Teks Header Section', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => __( 'Tampilkan Header', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label'       => __( 'Judul Utama (Headline)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Kata Pelanggan Tentang Jubelio', 'hello-elementor-child' ),
				'placeholder' => __( 'Kata Pelanggan Tentang Jubelio', 'hello-elementor-child' ),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'HTML Tag Judul', 'hello-elementor-child' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'DIV',
					'span' => 'SPAN',
				),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'description_text',
			array(
				'label'       => __( 'Subjudul / Deskripsi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( 'Simak langsung pengalaman mereka dalam menggunakan Jubelio untuk mengembangkan bisnis.', 'hello-elementor-child' ),
				'placeholder' => __( 'Simak langsung pengalaman mereka...', 'hello-elementor-child' ),
				'condition'   => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_action_link',
			array(
				'label'        => __( 'Tampilkan Link Aksi (Lihat Semua)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'action_link_text',
			array(
				'label'       => __( 'Teks Link Aksi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Lihat Semua Cerita Pebisnis →', 'hello-elementor-child' ),
				'placeholder' => __( 'Lihat Semua Cerita Pebisnis →', 'hello-elementor-child' ),
				'condition'   => array(
					'show_header'      => 'yes',
					'show_action_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'action_link_url',
			array(
				'label'       => __( 'URL Link Aksi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://jubelio.com/cerita-pebisnis',
				'default'     => array(
					'url'         => home_url( '/cerita-pebisnis/' ),
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'   => array(
					'show_header'      => 'yes',
					'show_action_link' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: DATA SOURCE & SELECTION ====================
		$this->start_controls_section(
			'section_data_source',
			array(
				'label' => __( 'Sumber Konten & Pilihan Kartu', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'content_source',
			array(
				'label'       => __( 'Metode Pengambilan Konten', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default_query',
				'options'     => array(
					'default_query'   => __( 'Otomatis: Semua Cerita Pebisnis (Global Post Type)', 'hello-elementor-child' ),
					'select_posts'    => __( 'Pilih Post Tertentu (Kurasi Khusus Halaman Ini)', 'hello-elementor-child' ),
					'manual_repeater' => __( 'Kustom Manual (Repeater Khusus)', 'hello-elementor-child' ),
				),
				'description' => __( 'Bisa menggunakan default global (otomatis dari CPT Cerita Pebisnis) atau dipilih/dikustomisasi per halaman.', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'selected_posts',
			array(
				'label'       => __( 'Pilih Cerita Pebisnis yang Ditampilkan', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_posts_options(),
				'label_block' => true,
				'condition'   => array(
					'content_source' => 'select_posts',
				),
				'description' => __( 'Pilih kartu-kartu mana saja yang ingin dimunculkan pada section ini.', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'posts_limit',
			array(
				'label'       => __( 'Maksimal Kartu', 'hello-elementor-child' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6,
				'min'         => 1,
				'max'         => 30,
				'step'        => 1,
				'condition'   => array(
					'content_source' => array( 'default_query', 'select_posts' ),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Urutkan Berdasarkan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => __( 'Tanggal Rilis Terbaru', 'hello-elementor-child' ),
					'title'      => __( 'Nama Brand / Judul (A-Z)', 'hello-elementor-child' ),
					'menu_order' => __( 'Urutan Menu Order', 'hello-elementor-child' ),
					'rand'       => __( 'Acak (Random)', 'hello-elementor-child' ),
				),
				'condition' => array(
					'content_source' => 'default_query',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Arah Urutan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => __( 'Menurun (Z-A / Terbaru)', 'hello-elementor-child' ),
					'ASC'  => __( 'Menaik (A-Z / Terlama)', 'hello-elementor-child' ),
				),
				'condition' => array(
					'content_source' => 'default_query',
				),
			)
		);

		// Manual Repeater Items
		$repeater = new Repeater();

		$repeater->add_control(
			'client_photo',
			array(
				'label'   => __( 'Foto Klien / Toko', 'hello-elementor-child' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
			)
		);

		$repeater->add_control(
			'client_logo',
			array(
				'label'   => __( 'Logo Brand (Badge Putih)', 'hello-elementor-child' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
			)
		);

		$repeater->add_control(
			'quote',
			array(
				'label'       => __( 'Kutipan Testimoni (Quote)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'Gak perlu buat software sendiri, Jubelio sudah sangat efisien buat kami...', 'hello-elementor-child' ),
				'placeholder' => __( 'Tulis kutipan testimoni klien...', 'hello-elementor-child' ),
			)
		);

		$repeater->add_control(
			'client_name',
			array(
				'label'       => __( 'Nama Narasumber / Klien', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Hansa Artupas',
				'placeholder' => 'Hansa Artupas',
			)
		);

		$repeater->add_control(
			'client_role',
			array(
				'label'       => __( 'Jabatan / Usaha', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Owner Cuit BabyWear',
				'placeholder' => 'Owner Cuit BabyWear',
			)
		);

		$repeater->add_control(
			'card_link',
			array(
				'label'       => __( 'Link Halaman Testimoni / Cerita', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://jubelio.com/cerita-pebisnis/cuit-baby-wear/',
				'default'     => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
			)
		);

		$this->add_control(
			'manual_items',
			array(
				'label'       => __( 'Daftar Kartu Testimoni Manual', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ client_name }}} - {{{ client_role }}}',
				'condition'   => array(
					'content_source' => 'manual_repeater',
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
				'description'  => __( 'Menampilkan 3 kartu sampel sesuai Figma ketika belum ada postingan Cerita Pebisnis.', 'hello-elementor-child' ),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: SLIDER & NAVIGATION ====================
		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Pengaturan Slider & Navigasi', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_navigation',
			array(
				'label'        => __( 'Tampilkan Tombol Navigasi (Panah Bulat)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => __( 'Tampilkan Titik Pagination', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'dynamic_pagination',
			array(
				'label'        => __( 'Pagination Dinamis (Dynamic Bullets)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_pagination' => 'yes',
				),
				'description'  => __( 'Ukuran titik mengecil halus secara dinamis mengikuti slide aktif (sesuai Swiper JS).', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Putar Otomatis (Autoplay)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Kecepatan Autoplay (ms)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4500,
				'min'       => 1500,
				'max'       => 10000,
				'step'      => 500,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: HEADER ====================
		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => __( 'Header Section', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Warna Judul', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-title' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonials-title',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Warna Subjudul', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748B',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-desc' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonials-desc',
			)
		);

		$this->add_control(
			'action_link_color',
			array(
				'label'     => __( 'Warna Link Aksi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-action-link' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'action_link_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonials-action-link',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TESTIMONIAL CARDS ====================
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Kartu Testimoni', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Warna Background Kartu', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonial-card' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_active_bg',
			array(
				'label'     => __( 'Background Kartu Aktif (Tengah)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6FAFF',
				'selectors' => array(
					'{{WRAPPER}} .swiper-slide-active .jubelio-testimonial-card' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_active_border',
			array(
				'label'     => __( 'Border Kartu Aktif (Tengah)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#BAE6FD',
				'selectors' => array(
					'{{WRAPPER}} .swiper-slide-active .jubelio-testimonial-card' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius Kartu', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-testimonial-card' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .jubelio-testimonial-card',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: KUTIPAN & NARASUMBER ====================
		$this->start_controls_section(
			'section_style_quote',
			array(
				'label' => __( 'Kutipan & Narasumber', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => __( 'Warna Teks Kutipan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonial-quote' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'quote_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonial-quote',
			)
		);

		$this->add_control(
			'author_name_color',
			array(
				'label'     => __( 'Warna Nama Narasumber', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonial-author-name' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_name_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonial-author-name',
			)
		);

		$this->add_control(
			'author_role_color',
			array(
				'label'     => __( 'Warna Jabatan Narasumber', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748B',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonial-author-role' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_role_typography',
				'selector' => '{{WRAPPER}} .jubelio-testimonial-author-role',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TOMBOL & PAGINASI ====================
		$this->start_controls_section(
			'section_style_nav',
			array(
				'label' => __( 'Tombol Navigasi & Titik Paginasi', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'testimonials_nav_btn_color',
			array(
				'label'     => __( 'Warna Ikon Panah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn svg' => 'color: {{VALUE}}; stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'testimonials_nav_btn_bg',
			array(
				'label'     => __( 'Background Tombol Panah', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'testimonials_nav_btn_hover_bg',
			array(
				'label'     => __( 'Background Panah (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn:hover:not(:disabled)' => 'background-color: {{VALUE}}; border-color: {{VALUE}}; color: #FFFFFF;',
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn:hover:not(:disabled) svg' => 'color: #FFFFFF; stroke: #FFFFFF;',
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
					'{{WRAPPER}} .jubelio-testimonials-section .jubelio-slider-btn' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; min-width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Warna Titik Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-testimonials-section .swiper-pagination-bullet-active' => 'background: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Widget HTML
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = ! empty( $settings['content_source'] ) ? $settings['content_source'] : 'default_query';
		$items    = array();

		if ( 'manual_repeater' === $source ) {
			if ( ! empty( $settings['manual_items'] ) && is_array( $settings['manual_items'] ) ) {
				foreach ( $settings['manual_items'] as $index => $row ) {
					$card_url = ! empty( $row['card_link']['url'] ) ? esc_url( $row['card_link']['url'] ) : '#';
					$items[]  = array(
						'id'                 => 'manual_' . $index,
						'card_image'         => ! empty( $row['client_photo']['url'] ) ? $row['client_photo']['url'] : '',
						'company_logo'       => ! empty( $row['client_logo']['url'] ) ? $row['client_logo']['url'] : '',
						'quote'              => ! empty( $row['quote'] ) ? $row['quote'] : '',
						'client_person_name' => ! empty( $row['client_name'] ) ? $row['client_name'] : '',
						'client_person_role' => ! empty( $row['client_role'] ) ? $row['client_role'] : '',
						'cta_link'           => $card_url,
					);
				}
			}
		} else {
			// Query from CPT Cerita Pebisnis
			$limit      = ! empty( $settings['posts_limit'] ) ? intval( $settings['posts_limit'] ) : 6;
			$query_args = array(
				'post_type'      => 'cerita-pebisnis',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
			);

			if ( 'select_posts' === $source && ! empty( $settings['selected_posts'] ) && is_array( $settings['selected_posts'] ) ) {
				$query_args['post__in'] = array_map( 'intval', $settings['selected_posts'] );
				$query_args['orderby']  = 'post__in';
			} else {
				$query_args['orderby'] = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
				$query_args['order']   = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
			}

			$query = new \WP_Query( $query_args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id   = get_the_ID();
					$item_data = Model::get( $post_id );
					$items[]   = $item_data;
				}
				wp_reset_postdata();
			}
		}

		// Fallback sample data if empty
		if ( empty( $items ) && ( ! isset( $settings['preview_sample_data'] ) || 'yes' === $settings['preview_sample_data'] ) ) {
			$items = $this->get_sample_items();
		}

		// Swiper Config
		$swiper_config = array(
			'dynamicPagination' => ( ! isset( $settings['dynamic_pagination'] ) || 'yes' === $settings['dynamic_pagination'] ),
			'autoplay'          => ( ! empty( $settings['autoplay'] ) && 'yes' === $settings['autoplay'] ),
			'autoplaySpeed'     => ! empty( $settings['autoplay_speed'] ) ? intval( $settings['autoplay_speed'] ) : 4500,
		);

		include dirname( __DIR__ ) . '/views/testimonials.php';
	}

	/**
	 * Wireframe Sample Data matching Reference Image
	 */
	protected function get_sample_items() {
		return array(
			array(
				'id'                 => 'sample_1',
				'title'              => 'Cuit Baby Wear',
				'card_image'         => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&auto=format&fit=crop&q=80',
				'company_logo'       => '',
				'client_name'        => 'Cuit Baby Wear',
				'quote'              => 'Gak perlu buat software sendiri, Jubelio sudah sangat efisien buat Cuit Baby Wear. Sebab, kami bisa memaksimalkan potensi dari setiap store dengan memanfaatkan stok yang optimal. Selain itu, CS Jubelio juga sangat membantu sekali',
				'client_person_name' => 'Hansa Artupas',
				'client_person_role' => 'Owner Cuit BabyWear',
				'cta_link'           => esc_url( home_url( '/cerita-pebisnis/cuit-baby-wear/' ) ),
			),
			array(
				'id'                 => 'sample_2',
				'title'              => 'Clowny',
				'card_image'         => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&auto=format&fit=crop&q=80',
				'company_logo'       => '',
				'client_name'        => 'Clowny',
				'quote'              => 'Sebelum pakai Jubelio, sering banget selisih stok antar marketplace sampai orderan harus dibatalkan. Sekarang semua stok tersinkronisasi otomatis dalam hitungan detik.',
				'client_person_name' => 'Vincent',
				'client_person_role' => 'Founder Clowny',
				'cta_link'           => esc_url( home_url( '/cerita-pebisnis/clowny/' ) ),
			),
			array(
				'id'                 => 'sample_3',
				'title'              => 'Rodeo Junior',
				'card_image'         => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&auto=format&fit=crop&q=80',
				'company_logo'       => '',
				'client_name'        => 'Rodeo Junior',
				'quote'              => 'Integrasi toko fisik dan puluhan marketplace kami sekarang berjalan sangat seamless. Efisiensi operasional tim kami meningkat lebih dari 80%.',
				'client_person_name' => 'Rudy Kurniawan',
				'client_person_role' => 'Head of Operations Rodeo Junior',
				'cta_link'           => esc_url( home_url( '/cerita-pebisnis/rodeo-junior/' ) ),
			),
			array(
				'id'                 => 'sample_4',
				'title'              => 'Aerostreet',
				'card_image'         => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
				'company_logo'       => '',
				'client_name'        => 'Aerostreet',
				'quote'              => 'Jubelio mempermudah kami memproses hingga 40.000 paket per hari saat flash sale tanpa kendala sistem atau salah kirim barang.',
				'client_person_name' => 'Aditya',
				'client_person_role' => 'COO Aerostreet',
				'cta_link'           => esc_url( home_url( '/cerita-pebisnis/aerostreet/' ) ),
			),
		);
	}
}
