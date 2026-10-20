<?php
/**
 * Elementor Widget: Cerita Pebisnis (Client Testimonials & Success Stories)
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

class Jubelio_Widget_Cerita_Pebisnis extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis (Testimonial)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'cerita pebisnis', 'testimoni', 'testimonial', 'client', 'review', 'case study', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis', 'jubelio-slider' );
	}

	/**
	 * Get categories for dropdown control
	 */
	protected function get_categories_options() {
		$options = array( '' => __( 'Semua Kategori', 'hello-elementor-child' ) );
		$terms   = get_terms( array(
			'taxonomy'   => 'kategori_cerita_pebisnis',
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

		// ==================== SECTION: LAYOUT ====================
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout & Tampilan', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Tipe Layout', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'      => __( 'Responsive Grid', 'hello-elementor-child' ),
					'slider'    => __( 'Carousel / Slider', 'hello-elementor-child' ),
					'spotlight' => __( 'Featured Spotlight (Hero Story + Cards)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Jumlah Kolom', 'hello-elementor-child' ),
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
				'condition'      => array(
					'layout!' => 'spotlight',
				),
			)
		);

		$this->add_control(
			'show_filter',
			array(
				'label'        => __( 'Tampilkan Filter Tab Kategori', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'layout' => 'grid',
				),
			)
		);

		$this->add_control(
			'filter_all_label',
			array(
				'label'     => __( 'Label Tab Semua', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Semua Cerita', 'hello-elementor-child' ),
				'condition' => array(
					'layout'      => 'grid',
					'show_filter' => 'yes',
				),
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
				'label'   => __( 'Jumlah Post', 'hello-elementor-child' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
			)
		);

		$this->add_control(
			'category',
			array(
				'label'   => __( 'Filter Kategori Spesifik', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_categories_options(),
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
					'title'      => __( 'Nama / Judul (A-Z)', 'hello-elementor-child' ),
					'rand'       => __( 'Acak (Random)', 'hello-elementor-child' ),
					'menu_order' => __( 'Urutan Menu (Menu Order)', 'hello-elementor-child' ),
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

		// ==================== SECTION: ELEMENTS VISIBILITY ====================
		$this->start_controls_section(
			'section_elements',
			array(
				'label' => __( 'Visibilitas Elemen Kartu', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_logo',
			array(
				'label'        => __( 'Tampilkan Logo Toko / Bisnis', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_category',
			array(
				'label'        => __( 'Tampilkan Badge Kategori / Industri', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => __( 'Tampilkan Bintang Rating (5 Star)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_metric',
			array(
				'label'        => __( 'Tampilkan Highlight Metrik (Pencapaian)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_quote',
			array(
				'label'        => __( 'Tampilkan Kutipan Testimoni', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_photo',
			array(
				'label'        => __( 'Tampilkan Foto / Avatar Klien', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_name',
			array(
				'label'        => __( 'Tampilkan Nama Klien', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_role',
			array(
				'label'        => __( 'Tampilkan Jabatan & Perusahaan', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_video_btn',
			array(
				'label'        => __( 'Tampilkan Tombol Video Lightbox', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'     => __( 'Teks Tombol Video', 'hello-elementor-child' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Tonton Video', 'hello-elementor-child' ),
				'condition' => array(
					'show_video_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label'   => __( 'Teks Link Baca Cerita', 'hello-elementor-child' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Baca Cerita', 'hello-elementor-child' ),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: SLIDER SETTINGS ====================
		$this->start_controls_section(
			'section_slider',
			array(
				'label'     => __( 'Pengaturan Slider / Carousel', 'hello-elementor-child' ),
				'condition' => array(
					'layout' => 'slider',
				),
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
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Kecepatan Autoplay (ms)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => __( 'Tampilkan Panah Navigasi', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => __( 'Tampilkan Titik Indikator (Dots)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => __( 'Loop Tanpa Batas (Infinite)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: ACF FIELD MAPPING ====================
		$this->start_controls_section(
			'section_acf_mapping',
			array(
				'label' => __( 'Pemetaan Field ACF (Opsional)', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'acf_mapping_desc',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>Otomatis:</strong> Widget ini sudah mendeteksi nama-nama field standar ACF secara otomatis (client_name, quote, foto_klien, dsb). Anda hanya perlu mengisi kolom di bawah jika memiliki field khusus dengan nama berbeda.', 'hello-elementor-child' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'field_client_name',
			array(
				'label'       => __( 'Custom Field: Nama Klien', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'client_name / nama_klien',
			)
		);

		$this->add_control(
			'field_client_role',
			array(
				'label'       => __( 'Custom Field: Jabatan', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'client_role / jabatan',
			)
		);

		$this->add_control(
			'field_company_name',
			array(
				'label'       => __( 'Custom Field: Nama Bisnis/Toko', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'company_name / nama_toko',
			)
		);

		$this->add_control(
			'field_company_logo',
			array(
				'label'       => __( 'Custom Field: Logo Bisnis', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'company_logo / logo_bisnis',
			)
		);

		$this->add_control(
			'field_client_photo',
			array(
				'label'       => __( 'Custom Field: Foto Klien', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'client_photo / foto_klien',
			)
		);

		$this->add_control(
			'field_quote',
			array(
				'label'       => __( 'Custom Field: Kutipan Testimoni', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'quote / testimoni',
			)
		);

		$this->add_control(
			'field_metric_badge',
			array(
				'label'       => __( 'Custom Field: Metrik / Pencapaian', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'metric_badge / stat_highlight',
			)
		);

		$this->add_control(
			'field_video_url',
			array(
				'label'       => __( 'Custom Field: Link Video (YouTube)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'video_url / link_video',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: KARTU (CARD) ====================
		$this->start_controls_section(
			'section_style_card',
			array(
				'label' => __( 'Kartu Testimoni (Card)', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_background',
				'label'    => __( 'Background Kartu', 'hello-elementor-child' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .jubelio-cp-card-inner',
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding Kartu', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-cp-card-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_border_radius',
			array(
				'label'      => __( 'Sudut Melengkung (Border Radius)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-cp-card-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .jubelio-cp-card-inner',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .jubelio-cp-card-inner',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: HIGHLIGHT METRIK & BADGE ====================
		$this->start_controls_section(
			'section_style_badge',
			array(
				'label' => __( 'Highlight Metrik & Kategori', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'metric_heading',
			array(
				'label'     => __( 'Box Metrik Pencapaian', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'metric_bg_color',
			array(
				'label'     => __( 'Background Metrik', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFF7EE',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-metric-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'metric_text_color',
			array(
				'label'     => __( 'Warna Teks Metrik', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-metric-box' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jubelio-cp-metric-icon svg' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'metric_typography',
				'selector' => '{{WRAPPER}} .jubelio-cp-metric-text',
			)
		);

		$this->add_control(
			'category_badge_heading',
			array(
				'label'     => __( 'Badge Kategori Bisnis', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'category_badge_bg',
			array(
				'label'     => __( 'Background Badge Kategori', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E1F0FF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-badge-category' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'category_badge_color',
			array(
				'label'     => __( 'Warna Teks Badge Kategori', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-badge-category' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TYPOGRAPHY (QUOTE & NAMA) ====================
		$this->start_controls_section(
			'section_style_typography',
			array(
				'label' => __( 'Tipografi & Warna Teks', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'quote_heading',
			array(
				'label' => __( 'Kutipan Testimoni', 'hello-elementor-child' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => __( 'Warna Kutipan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-quote-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'quote_typography',
				'selector' => '{{WRAPPER}} .jubelio-cp-quote-text',
			)
		);

		$this->add_control(
			'name_heading',
			array(
				'label'     => __( 'Nama Klien', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Warna Nama', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-author-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .jubelio-cp-author-name',
			)
		);

		$this->add_control(
			'role_heading',
			array(
				'label'     => __( 'Jabatan / Perusahaan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => __( 'Warna Jabatan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#606C75',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-author-role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'selector' => '{{WRAPPER}} .jubelio-cp-author-role',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TOMBOL & BINTANG ====================
		$this->start_controls_section(
			'section_style_interactive',
			array(
				'label' => __( 'Tombol Video & Bintang Rating', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => __( 'Warna Bintang (Filled)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFB800',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-star.filled' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_video_bg',
			array(
				'label'     => __( 'Warna Background Tombol Video', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-btn-video' => 'background-color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'btn_video_color',
			array(
				'label'     => __( 'Warna Teks Tombol Video', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-cp-btn-video' => 'color: {{VALUE}};',
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
		echo jubelio_render_cerita_pebisnis( $settings );
	}
}
