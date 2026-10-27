<?php
namespace Jubelio\Modules\CeritaPebisnis\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Jubelio\Modules\CeritaPebisnis\Model;

class SingleResultMetricsWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_single_result_metrics';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis: Hasil Transformasi (Metrics)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'metrics', 'hasil', 'transformasi', 'counter', 'cerita pebisnis', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_header',
			array(
				'label' => __( 'Judul Bagian', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => __( 'Judul', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Hasil Nyata Transformasi',
				'placeholder' => 'Hasil Nyata Transformasi',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Tag HTML Judul', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => __( 'Perataan Judul', 'hello-elementor-child' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Kiri', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Tengah', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Kanan', 'hello-elementor-child' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metrics-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_metrics',
			array(
				'label' => __( 'Daftar Metrik', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'data_source',
			array(
				'label'   => __( 'Sumber Data', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => array(
					'dynamic' => __( 'Otomatis dari Post / ACF (cp_metrik_hasil_list)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom Manual (Input Sendiri di Elementor)', 'hello-elementor-child' ),
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'metric_label',
			array(
				'label'       => __( 'Label Metrik', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Stock Accuracy',
				'placeholder' => 'Stock Accuracy',
			)
		);

		$repeater->add_control(
			'metric_value',
			array(
				'label'       => __( 'Nilai / Hasil Metrik', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '96% → 97%',
				'placeholder' => '96% → 97%',
			)
		);

		$repeater->add_control(
			'metric_icon',
			array(
				'label'       => __( 'Ikon Kustom (SVG / Gambar)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => '',
				),
				'description' => __( 'Upload file gambar (PNG, JPG, WebP) atau SVG. Biarkan kosong untuk menggunakan ikon default.', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'metrics_items',
			array(
				'label'       => __( 'Item Metrik Kustom', 'hello-elementor-child' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'metric_label' => 'Stock Accuracy',
						'metric_value' => '96% → 97%',
					),
					array(
						'metric_label' => 'Picking Performance',
						'metric_value' => '>60%',
					),
					array(
						'metric_label' => 'Putaway Output',
						'metric_value' => '+68,95% /hari',
					),
					array(
						'metric_label' => 'Lebih Efisien',
						'metric_value' => '4x',
					),
				),
				'title_field' => '{{{ metric_label }}}: {{{ metric_value }}}',
				'condition'   => array(
					'data_source' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'max_columns',
			array(
				'label'   => __( 'Maksimal Kolom (Desktop)', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'2' => '2 Kolom',
					'3' => '3 Kolom',
					'4' => '4 Kolom',
				),
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metrics-grid' => '--jubelio-metric-max-cols: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_heading',
			array(
				'label' => __( 'Gaya Judul', 'hello-elementor-child' ),
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
					'{{WRAPPER}} .jubelio-result-metrics-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin_bottom',
			array(
				'label'      => __( 'Jarak ke Bawah (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metrics-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Gaya Kartu Metrik', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => __( 'Warna Background Kartu', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F5FAFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metric-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_border_color',
			array(
				'label'     => __( 'Warna Border Kartu', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#CDE8FF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metric-card' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_border_radius',
			array(
				'label'      => __( 'Lengkungan Sudut (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metric-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding Kartu', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'      => '32',
					'bottom'   => '32',
					'left'     => '32',
					'right'    => '32',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metric-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => __( 'Jarak Antar Kartu (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metrics-grid' => '--jubelio-metric-gap: {{SIZE}}{{UNIT}}; gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_internal_gap',
			array(
				'label'      => __( 'Jarak Konten Internal (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 36,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metric-card' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_elements',
			array(
				'label' => __( 'Gaya Elemen Teks & Ikon', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Warna Ikon', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metric-icon-wrap' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Ukuran Ikon (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 28,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-result-metric-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jubelio-result-metric-icon-wrap img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Warna Label Metrik', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metric-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label'     => __( 'Warna Nilai Metrik', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-result-metric-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = get_the_ID();
		$post_data = Model::get( $post_id );

		$metrics = array();
		if ( 'custom' === $settings['data_source'] ) {
			if ( ! empty( $settings['metrics_items'] ) && is_array( $settings['metrics_items'] ) ) {
				foreach ( $settings['metrics_items'] as $item ) {
					$lbl = ! empty( $item['metric_label'] ) ? trim( $item['metric_label'] ) : '';
					$val = ! empty( $item['metric_value'] ) ? trim( $item['metric_value'] ) : '';
					$icon_url = '';
					if ( ! empty( $item['metric_icon'] ) && is_array( $item['metric_icon'] ) && ! empty( $item['metric_icon']['url'] ) ) {
						$icon_url = esc_url( $item['metric_icon']['url'] );
					}
					if ( ! empty( $lbl ) || ! empty( $val ) ) {
						$metrics[] = array(
							'label'    => $lbl,
							'value'    => $val,
							'icon_url' => $icon_url,
						);
					}
				}
			}
		} else {
			if ( ! empty( $post_data['result_metrics'] ) && is_array( $post_data['result_metrics'] ) ) {
				$metrics = $post_data['result_metrics'];
			}
		}

		if ( empty( $metrics ) ) {
			$metrics = array(
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

		$title_tag = ! empty( $settings['title_tag'] ) ? esc_attr( $settings['title_tag'] ) : 'h2';
		$section_title = ! empty( $settings['section_title'] ) ? $settings['section_title'] : '';
		$max_cols = ! empty( $settings['max_columns'] ) ? intval( $settings['max_columns'] ) : 4;
		?>
		<div class="jubelio-result-metrics-section">
			<?php if ( ! empty( $section_title ) ) : ?>
				<<?php echo $title_tag; ?> class="jubelio-result-metrics-title">
					<?php echo esc_html( $section_title ); ?>
				</<?php echo $title_tag; ?>>
			<?php endif; ?>

			<div class="jubelio-result-metrics-grid" style="--jubelio-metric-max-cols: <?php echo esc_attr( $max_cols ); ?>;">
				<?php foreach ( $metrics as $metric ) : ?>
					<div class="jubelio-result-metric-card">
						<div class="jubelio-result-metric-icon-wrap" aria-hidden="true">
							<?php if ( ! empty( $metric['icon_url'] ) ) : ?>
								<img src="<?php echo esc_url( $metric['icon_url'] ); ?>" alt="" class="jubelio-result-metric-icon-img" loading="lazy" />
							<?php else : ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="jubelio-result-metric-icon">
									<path d="M12 2a10 10 0 1 0 10 10" />
									<path d="m21.5 2.5-9 9" />
									<path d="M17 2.5h4.5v4.5" />
									<circle cx="12" cy="12" r="5" />
								</svg>
							<?php endif; ?>
						</div>
						<div class="jubelio-result-metric-content">
							<?php if ( ! empty( $metric['label'] ) ) : ?>
								<div class="jubelio-result-metric-label">
									<?php echo esc_html( $metric['label'] ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $metric['value'] ) ) : ?>
								<div class="jubelio-result-metric-value">
									<?php echo esc_html( $metric['value'] ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
