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
				'default'   => 'Konsultasikan Kebutuhan Bisnis',
				'condition' => array(
					'show_cta_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_cta_url',
			array(
				'label'       => __( 'URL Konsultasi Global (Opsional)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://jubelio.com/demo atau link WhatsApp',
				'description' => __( 'Jika diisi, semua kartu akan mengarah ke link ini. Jika kosong, akan menggunakan link khusus kartu atau single post link.', 'hello-elementor-child' ),
				'condition'   => array(
					'show_cta_link' => 'yes',
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
				'label'       => __( 'Field Key: Headline / Solusi', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'headline / subjudul / solusi',
			)
		);

		$this->add_control(
			'field_company_logo',
			array(
				'label'       => __( 'Field Key: Logo Brand', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'logo_bisnis / logo / brand_logo',
			)
		);

		$this->add_control(
			'field_card_image',
			array(
				'label'       => __( 'Field Key: Foto Toko (jika bukan Featured Image)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'foto_toko / gambar_utama',
			)
		);

		$this->add_control(
			'field_description',
			array(
				'label'       => __( 'Field Key: Deskripsi Cerita', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'deskripsi / cerita_singkat',
			)
		);

		$this->add_control(
			'field_cta_link',
			array(
				'label'       => __( 'Field Key: Link Khusus Kartu', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'link_konsultasi / cta_link',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: EYEBROW & TABS ====================
		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => __( 'Eyebrow & Filter Tabs', 'hello-elementor-child' ),
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
			'tab_active_bg',
			array(
				'label'     => __( 'Background Tab Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-tab-btn.active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'tab_active_color',
			array(
				'label'     => __( 'Warna Teks Tab Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-tab-btn.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_inactive_bg',
			array(
				'label'     => __( 'Background Tab Tidak Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F4F7FA',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-tab-btn:not(.active)' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_inactive_color',
			array(
				'label'     => __( 'Warna Teks Tab Tidak Aktif', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-tab-btn:not(.active)' => 'color: {{VALUE}};',
				),
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

		if ( ! $query->have_posts() ) {
			echo '<div class="jubelio-grid-empty"><p>' . esc_html__( 'Belum ada Cerita Pebisnis yang dipublikasikan.', 'hello-elementor-child' ) . '</p></div>';
			return;
		}

		// Field mapping config
		$field_keys = array(
			'headline'     => $settings['field_headline'],
			'company_logo' => $settings['field_company_logo'],
			'card_image'   => $settings['field_card_image'],
			'description'  => $settings['field_description'],
			'cta_link'     => $settings['field_cta_link'],
		);

		// Extract items and collect distinct industry terms
		$items      = array();
		$industries = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id   = get_the_ID();
			$item_data = Model::get( $post_id, $field_keys );

			// Check global CTA URL override
			if ( ! empty( $settings['custom_cta_url'] ) ) {
				$item_data['cta_link'] = esc_url( $settings['custom_cta_url'] );
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

		// Delegate HTML rendering to the view template partial
		include dirname( __DIR__ ) . '/views/grid.php';
	}
}
