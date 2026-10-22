<?php
/**
 * Elementor Widget: Cerita Pebisnis Metrics Section (Angka yang Bicara)
 * Supports Curved Fan Arc Layout (matching Reference Image) & Classic 2-Column Split
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

class MetricsWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_metrics';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis Metrics (Angka yang Bicara)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'metrics', 'angka', 'counter', 'arc', 'fan', 'statistik', 'pertumbuhan', 'jubelio', 'cerita pebisnis' );
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

		// ==================== SECTION: LAYOUT & STRUKTUR ====================
		$this->start_controls_section(
			'section_layout_settings',
			array(
				'label' => __( 'Tipe Layout & Tampilan', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'layout_type',
			array(
				'label'   => __( 'Tipe Layout Tampilan', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'glass_grid',
				'options' => array(
					'glass_grid' => __( 'Glass Gradient Grid (3-Kolom Sesuai Referensi)', 'hello-elementor-child' ),
					'arc'        => __( 'Curved Fan Arc Layout', 'hello-elementor-child' ),
					'split'      => __( '2-Kolom Bersebelahan (Classic Split)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'header_position',
			array(
				'label'     => __( 'Posisi Teks Header', 'hello-elementor-child' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'bottom' => __( 'Di Bawah Kartu Melengkung (Sesuai Referensi)', 'hello-elementor-child' ),
					'top'    => __( 'Di Atas Kartu Melengkung', 'hello-elementor-child' ),
				),
				'condition' => array(
					'layout_type' => 'arc',
				),
			)
		);

		$this->add_control(
			'show_card_icons',
			array(
				'label'        => __( 'Tampilkan Ikon di Dalam Kartu', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Aktifkan jika ingin menampilkan ikon di dalam kartu (default: nonaktif sesuai screenshot referensi).', 'hello-elementor-child' ),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: HEADER COPY ====================
		$this->start_controls_section(
			'section_header_content',
			array(
				'label' => __( 'Teks Header & Ajakan (CTA)', 'hello-elementor-child' ),
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
				'default'     => 'What We Offer ↗',
				'placeholder' => 'What We Offer ↗',
				'condition'   => array(
					'show_eyebrow' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label'       => __( 'Judul Utama Section', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Dampak Nyata untuk Pertumbuhan Bisnis Bersama Jubelio',
				'placeholder' => 'Dampak Nyata untuk Pertumbuhan Bisnis...',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'HTML Tag Judul', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'DIV',
					'span' => 'SPAN',
				),
			)
		);

		$this->add_control(
			'description_text',
			array(
				'label'       => __( 'Deskripsi Singkat', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Bersama Jubelio, berbagai bisnis telah merasakan peningkatan efisiensi operasional, penjualan, dan pertumbuhan yang signifikan.',
				'placeholder' => 'Deskripsi dampak bisnis bersama Jubelio...',
			)
		);

		$this->add_control(
			'show_cta',
			array(
				'label'        => __( 'Tampilkan Tombol Konsultasi (CTA)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'       => __( 'Teks Tombol CTA', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Konsultasikan Kebutuhan Bisnis',
				'placeholder' => 'Konsultasikan Kebutuhan Bisnis',
				'condition'   => array(
					'show_cta' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_url',
			array(
				'label'       => __( 'Link URL Tombol CTA', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://jubelio.com/demo',
				'default'     => array(
					'url'         => 'https://jubelio.com/demo',
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'   => array(
					'show_cta' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_cta_arrow',
			array(
				'label'        => __( 'Tampilkan Panah di Tombol CTA', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'show_cta' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// ==================== SECTION: METRICS CARDS REPEATER ====================
		$this->start_controls_section(
			'section_metrics_list',
			array(
				'label' => __( 'Daftar Kartu Metrik', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'enable_counter',
			array(
				'label'        => __( 'Aktifkan Animasi Hitung (Count-up)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Angka bertambah halus saat section muncul di viewport browser.', 'hello-elementor-child' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'metric_value',
			array(
				'label'       => __( 'Angka / Nilai Metrik (Tag <h3>)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '127',
				'placeholder' => '127 / 35.000+ / 4.2m',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'metric_label',
			array(
				'label'       => __( 'Teks Deskripsi (Tag <h4>)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Active Conversations',
				'placeholder' => 'Active Conversations / Deskripsi...',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'chart_type',
			array(
				'label'   => __( 'Pilihan Grafik / Chart di Bagian Bawah', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bar_chart',
				'options' => array(
					'bar_chart'      => __( 'Diagram Batang Coral / Oranye (Kartu 1 Referensi)', 'hello-elementor-child' ),
					'arch_wave'      => __( 'Arch Waveform Biru (Kartu 2 Referensi)', 'hello-elementor-child' ),
					'ticks_bar'      => __( 'Garis Vertikal Ungu / Barcode (Kartu 3 Referensi)', 'hello-elementor-child' ),
					'spline_wave'    => __( 'Gelombang Halus Oranye Glow (Kartu 4 Referensi)', 'hello-elementor-child' ),
					'growth_area'    => __( 'Kurva Pertumbuhan Area Hijau Emerald (Kartu 5)', 'hello-elementor-child' ),
					'dual_sparkline' => __( 'Dual Sparkline Indigo & Cyan (Kartu 6)', 'hello-elementor-child' ),
					'custom'         => __( 'Upload Gambar / SVG Custom Sendiri...', 'hello-elementor-child' ),
				),
			)
		);

		$repeater->add_control(
			'custom_chart',
			array(
				'label'     => __( 'Pilih File Gambar / Grafik', 'hello-elementor-child' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'chart_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'metric_icon',
			array(
				'label'     => __( 'Ikon Vektor (Khusus Layout Arc / Split)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'storefront',
				'options'   => array(
					'storefront' => __( 'Toko / Storefront', 'hello-elementor-child' ),
					'package'    => __( 'Paket / 3D Cube', 'hello-elementor-child' ),
					'chart'      => __( 'Grafik Naik / Trending Up', 'hello-elementor-child' ),
					'wallet'     => __( 'Dompet / GMV Wallet', 'hello-elementor-child' ),
					'users'      => __( 'Komunitas / Multi-Users', 'hello-elementor-child' ),
					'layers'     => __( 'Platform / Stack Layers', 'hello-elementor-child' ),
					'custom'     => __( 'Custom Elementor Icon...', 'hello-elementor-child' ),
				),
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label'     => __( 'Pilih Custom Icon', 'hello-elementor-child' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array(
					'metric_icon' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'item_hover_bg',
			array(
				'label'       => __( 'Warna Hover Khusus (Opsional)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'description' => __( 'Kosongkan untuk menggunakan warna hover global.', 'hello-elementor-child' ),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'metrics_list',
			array(
				'label'       => __( 'Kartu-Kartu Metrik (3 Kolom x 2 Baris)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ metric_value }}} - {{{ metric_label }}}',
				'default'     => array(
					array(
						'metric_value' => '127',
						'metric_label' => 'Active Conversations',
						'chart_type'   => 'bar_chart',
					),
					array(
						'metric_value' => '4.2m',
						'metric_label' => 'Avg Response Time',
						'chart_type'   => 'arch_wave',
					),
					array(
						'metric_value' => '98',
						'metric_label' => 'Resolved Today',
						'chart_type'   => 'ticks_bar',
					),
					array(
						'metric_value' => '54%',
						'metric_label' => 'AI Auto-Resolved',
						'chart_type'   => 'spline_wave',
					),
					array(
						'metric_value' => '35.000+',
						'metric_label' => 'Bisnis Aktif Menggunakan Jubelio',
						'chart_type'   => 'growth_area',
					),
					array(
						'metric_value' => '32juta+',
						'metric_label' => 'Pesanan Diproses Setiap Bulan',
						'chart_type'   => 'dual_sparkline',
					),
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: HEADER ====================
		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => __( 'Header & Teks', 'hello-elementor-child' ),
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
					'{{WRAPPER}} .jubelio-metrics-eyebrow' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .jubelio-metrics-eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Warna Judul', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-title' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .jubelio-metrics-title',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Warna Deskripsi', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748B',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-desc' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .jubelio-metrics-desc',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: TOMBOL CTA ====================
		$this->start_controls_section(
			'section_style_cta',
			array(
				'label' => __( 'Tombol Konsultasi (CTA)', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .jubelio-metrics-cta-btn',
			)
		);

		$this->start_controls_tabs( 'tabs_cta_style' );

		$this->start_controls_tab(
			'tab_cta_normal',
			array(
				'label' => __( 'Normal', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'cta_text_color',
			array(
				'label'     => __( 'Warna Teks', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'cta_bg_color',
			array(
				'label'     => __( 'Warna Background', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'cta_border_color',
			array(
				'label'     => __( 'Warna Border', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cta_hover',
			array(
				'label' => __( 'Hover', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'cta_text_color_hover',
			array(
				'label'     => __( 'Warna Teks Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn:hover' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'cta_bg_color_hover',
			array(
				'label'     => __( 'Warna Background Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn:hover' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'cta_border_color_hover',
			array(
				'label'     => __( 'Warna Border Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-cta-btn:hover' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ==================== STYLE TAB: OUTER GLASS CONTAINER ====================
		$this->start_controls_section(
			'section_style_glass_container',
			array(
				'label'     => __( 'Container Luar (Glass & Gradient Border)', 'hello-elementor-child' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => 'glass_grid',
				),
			)
		);

		$this->add_control(
			'glass_border_grad_start',
			array(
				'label'     => __( 'Warna Gradient Border (Awal)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(251, 146, 60, 0.75)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-glass-outer' => '--glass-border-start: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'glass_border_grad_end',
			array(
				'label'     => __( 'Warna Gradient Border (Akhir)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(234, 88, 12, 0.85)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-glass-outer' => '--glass-border-end: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'glass_backdrop_bg',
			array(
				'label'     => __( 'Warna Background Backdrop', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8FAFD',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metrics-glass-backdrop' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'glass_border_radius',
			array(
				'label'      => __( 'Border Radius Container Luar', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 38,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-metrics-glass-outer' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .jubelio-metrics-glass-backdrop' => 'border-radius: calc({{SIZE}}{{UNIT}} - 3px) !important;',
				),
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: KARTU METRIK & HOVER COLOR ====================
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Kartu Metrik & Efek Hover Warna', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_cards_style' );

		// Tab Normal
		$this->start_controls_tab(
			'tab_cards_normal',
			array(
				'label' => __( 'Normal', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Warna Background Kartu', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-card, {{WRAPPER}} .jubelio-metric-card-arc, {{WRAPPER}} .jubelio-metric-card-box' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .jubelio-metric-card, {{WRAPPER}} .jubelio-metric-card-arc, {{WRAPPER}} .jubelio-metric-card-box',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Border Radius Kartu', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-metric-card, {{WRAPPER}} .jubelio-metric-card-arc, {{WRAPPER}} .jubelio-metric-card-box' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .jubelio-metric-card, {{WRAPPER}} .jubelio-metric-card-arc, {{WRAPPER}} .jubelio-metric-card-box',
			)
		);

		$this->end_controls_tab();

		// Tab Hover (Change Color on Hover!)
		$this->start_controls_tab(
			'tab_cards_hover',
			array(
				'label' => __( 'Hover (Ubah Warna)', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'card_hover_bg',
			array(
				'label'       => __( 'Warna Background Kartu Saat Hover', 'hello-elementor-child' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#4B68EF',
				'description' => __( 'Kartu akan berubah ke warna ini saat mouse mengarah kepadanya.', 'hello-elementor-child' ),
				'selectors'   => array(
					'{{WRAPPER}} .jubelio-metric-card:hover, {{WRAPPER}} .jubelio-metric-card-arc:hover, {{WRAPPER}} .jubelio-metric-card-box:hover' => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_hover_value_color',
			array(
				'label'     => __( 'Warna Angka Saat Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-card:hover .jubelio-metric-value, {{WRAPPER}} .jubelio-metric-card-arc:hover .jubelio-metric-value, {{WRAPPER}} .jubelio-metric-card-box:hover .jubelio-metric-number' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_hover_label_color',
			array(
				'label'     => __( 'Warna Keterangan Saat Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.92)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-card:hover .jubelio-metric-label, {{WRAPPER}} .jubelio-metric-card-arc:hover .jubelio-metric-label, {{WRAPPER}} .jubelio-metric-card-box:hover .jubelio-metric-desc' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_hover_icon_color',
			array(
				'label'     => __( 'Warna Ikon Saat Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-card:hover .jubelio-metric-icon-wrap, {{WRAPPER}} .jubelio-metric-card-arc:hover .jubelio-metric-icon-wrap' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .jubelio-metric-card:hover .jubelio-metric-icon-wrap svg, {{WRAPPER}} .jubelio-metric-card-arc:hover .jubelio-metric-icon-wrap svg' => 'stroke: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'card_hover_icon_bg',
			array(
				'label'     => __( 'Background Ikon Saat Hover', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.2)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-card:hover .jubelio-metric-icon-wrap, {{WRAPPER}} .jubelio-metric-card-arc:hover .jubelio-metric-icon-wrap' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_hover_shadow',
				'selector' => '{{WRAPPER}} .jubelio-metric-card:hover, {{WRAPPER}} .jubelio-metric-card-arc:hover, {{WRAPPER}} .jubelio-metric-card-box:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ==================== STYLE TAB: ANGKA & LABEL ====================
		$this->start_controls_section(
			'section_style_numbers',
			array(
				'label' => __( 'Angka & Label Metrik', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Warna Angka (Normal)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-value' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .jubelio-metric-value',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Warna Label / Keterangan (Normal)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748B',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-label' => 'color: {{VALUE}} !important;',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .jubelio-metric-label',
			)
		);

		$this->end_controls_section();

		// ==================== STYLE TAB: IKON ====================
		$this->start_controls_section(
			'section_style_icons',
			array(
				'label'     => __( 'Kotak Ikon Metrik', 'hello-elementor-child' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_card_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Warna Ikon', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-icon-wrap' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .jubelio-metric-icon-wrap svg' => 'stroke: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Background Kotak Ikon', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(75, 104, 239, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-metric-icon-wrap' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Widget HTML
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		include dirname( __DIR__ ) . '/views/metrics.php';
	}
}
