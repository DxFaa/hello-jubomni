<?php

namespace Jubelio\Modules\CeritaPebisnis\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

class SingleHeroWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_single_hero';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis: Hero Video', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-video-playlist';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'hero', 'video', 'cerita pebisnis', 'single', 'youtube', 'lcp', 'single post', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_header_content',
			array(
				'label' => __( 'Headline & Teks Pengantar', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'headline_source',
			array(
				'label'   => __( 'Sumber Headline', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => array(
					'dynamic' => __( 'Dinamis (Judul Post / ACF cp_headline)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom (Ketik Manual di Elementor)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'custom_headline',
			array(
				'label'       => __( 'Teks Headline Kustom', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Saat Ribuan Order Membutuhkan Gudang yang Lebih Siap', 'hello-elementor-child' ),
				'placeholder' => __( 'Masukkan judul headline utama...', 'hello-elementor-child' ),
				'label_block' => true,
				'condition'   => array(
					'headline_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'headline_tag',
			array(
				'label'   => __( 'HTML Tag Headline', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1 (Rekomendasi SEO)',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'subheadline_source',
			array(
				'label'   => __( 'Sumber Subheadline', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => array(
					'dynamic' => __( 'Dinamis (Excerpt Post / ACF cp_deskripsi)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom (Ketik Manual di Elementor)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'custom_subheadline',
			array(
				'label'       => __( 'Teks Subheadline Kustom', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => __( 'Kisah inspiratif bagaimana pemilik bisnis mengoptimalkan operasional pergudangan dan ribuan pesanan harian multi-channel bersama Jubelio.', 'hello-elementor-child' ),
				'placeholder' => __( 'Masukkan ringkasan atau pengantar cerita...', 'hello-elementor-child' ),
				'condition'   => array(
					'subheadline_source' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_content',
			array(
				'label' => __( 'Video & Thumbnail Placeholder (LCP)', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'video_source',
			array(
				'label'   => __( 'Sumber Link Video', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => array(
					'dynamic' => __( 'Dinamis (ACF cp_video_url)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom URL', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'custom_video_url',
			array(
				'label'       => __( 'URL Video (YouTube / Vimeo / MP4)', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'placeholder' => 'https://www.youtube.com/watch?v=...',
				'label_block' => true,
				'condition'   => array(
					'video_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'thumbnail_source',
			array(
				'label'       => __( 'Sumber Thumbnail Placeholder', 'hello-elementor-child' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'dynamic',
				'description' => __( 'Placeholder gambar dimuat dengan prioritas LCP tinggi untuk menjamin nilai Core Web Vitals optimal sebelum video dimainkan.', 'hello-elementor-child' ),
				'options'     => array(
					'dynamic' => __( 'Otomatis (Featured Image / ACF Foto / YouTube HQ)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom Unggah Gambar', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'custom_thumbnail',
			array(
				'label'     => __( 'Unggah Gambar Thumbnail', 'hello-elementor-child' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'thumbnail_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'   => __( 'Aspek Rasio Video', 'hello-elementor-child' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => array(
					'16-9' => '16 : 9 (Standar YouTube)',
					'21-9' => '21 : 9 (Ultrawide)',
					'4-3'  => '4 : 3 (Klasik)',
				),
			)
		);

		$this->add_control(
			'play_button_text',
			array(
				'label'       => __( 'Teks Tombol Play', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Putar', 'hello-elementor-child' ),
				'placeholder' => __( 'Putar', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'play_button_pulse',
			array(
				'label'        => __( 'Animasi Efek Denyut Tombol Play', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Aktif', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_nav_button',
			array(
				'label' => __( 'Tombol Navigasi Cerita Pebisnis', 'hello-elementor-child' ),
			)
		);

		$this->add_control(
			'show_nav_button',
			array(
				'label'        => __( 'Tampilkan Tombol', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'nav_button_text',
			array(
				'label'       => __( 'Teks Tombol', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Lihat Semua Cerita Pebisnis', 'hello-elementor-child' ),
				'placeholder' => __( 'Lihat Semua Cerita Pebisnis', 'hello-elementor-child' ),
				'condition'   => array(
					'show_nav_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'nav_button_link',
			array(
				'label'       => __( 'Link URL', 'hello-elementor-child' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '/cerita-pebisnis/',
				'default'     => array(
					'url'         => home_url( '/cerita-pebisnis/' ),
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'   => array(
					'show_nav_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'nav_button_icon',
			array(
				'label'       => __( 'Ikon Kustom Tombol', 'hello-elementor-child' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => '',
					'library' => '',
				),
				'condition'   => array(
					'show_nav_button' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => __( 'Container & Tata Letak', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => __( 'Padding Container', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_headline',
			array(
				'label' => __( 'Gaya Headline', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => __( 'Warna Teks Headline', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .jubelio-single-hero-title',
			)
		);

		$this->add_responsive_control(
			'headline_margin_bottom',
			array(
				'label'      => __( 'Jarak ke Subheadline (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_subheadline',
			array(
				'label' => __( 'Gaya Subheadline', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subheadline_color',
			array(
				'label'     => __( 'Warna Teks Subheadline', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subheadline_typography',
				'selector' => '{{WRAPPER}} .jubelio-single-hero-subtitle',
			)
		);

		$this->add_responsive_control(
			'subheadline_margin_bottom',
			array(
				'label'      => __( 'Jarak ke Video (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 36,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_video_box',
			array(
				'label' => __( 'Kartu Video & Tombol Play', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'video_border_radius',
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
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-video-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'video_box_shadow',
				'selector' => '{{WRAPPER}} .jubelio-single-hero-video-box',
			)
		);

		$this->add_responsive_control(
			'play_btn_padding',
			array(
				'label'      => __( 'Padding Tombol Play', 'hello-elementor-child' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '22',
					'bottom'   => '10',
					'left'     => '20',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-play-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'play_btn_border_radius',
			array(
				'label'      => __( 'Lengkungan Sudut Tombol (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-play-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'play_btn_typography',
				'selector' => '{{WRAPPER}} .jubelio-single-hero-play-btn, {{WRAPPER}} .jubelio-play-btn-text',
			)
		);

		$this->add_control(
			'play_btn_bg',
			array(
				'label'     => __( 'Warna Background Tombol Play', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-play-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_btn_text_color',
			array(
				'label'     => __( 'Warna Teks & Ikon Play', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-play-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jubelio-single-hero-play-btn svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav_btn',
			array(
				'label'     => __( 'Gaya Tombol Navigasi Bawah', 'hello-elementor-child' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_nav_button' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_btn_typography',
				'selector' => '{{WRAPPER}} .jubelio-single-hero-nav-btn',
			)
		);

		$this->add_control(
			'nav_btn_color',
			array(
				'label'     => __( 'Warna Teks Tombol', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4B68EF',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-nav-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_btn_hover_color',
			array(
				'label'     => __( 'Warna Teks Tombol (Hover)', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2B48D9',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-hero-nav-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_btn_margin_top',
			array(
				'label'      => __( 'Jarak dari Video ke Tombol (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-hero-nav-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function extract_youtube_id( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i';
		if ( preg_match( $pattern, $url, $matches ) ) {
			return $matches[1];
		}

		return false;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = get_the_ID();

		$headline = '';
		if ( 'custom' === $settings['headline_source'] && ! empty( $settings['custom_headline'] ) ) {
			$headline = $settings['custom_headline'];
		} else {
			if ( function_exists( 'get_field' ) ) {
				$headline = get_field( 'cp_headline', $post_id );
			}
			if ( empty( $headline ) ) {
				$headline = get_the_title( $post_id );
			}
		}

		if ( empty( $headline ) ) {
			$headline = __( 'Saat Ribuan Order Membutuhkan Gudang yang Lebih Siap', 'hello-elementor-child' );
		}

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		$tag          = ( ! empty( $settings['headline_tag'] ) && in_array( $settings['headline_tag'], $allowed_tags, true ) ) ? $settings['headline_tag'] : 'h1';

		$subheadline = '';
		if ( 'custom' === $settings['subheadline_source'] && ! empty( $settings['custom_subheadline'] ) ) {
			$subheadline = $settings['custom_subheadline'];
		} else {
			if ( function_exists( 'get_field' ) ) {
				$subheadline = get_field( 'cp_deskripsi', $post_id );
			}
			if ( empty( $subheadline ) ) {
				$subheadline = get_the_excerpt( $post_id );
			}
		}

		$video_url = '';
		if ( 'custom' === $settings['video_source'] && ! empty( $settings['custom_video_url'] ) ) {
			$video_url = $settings['custom_video_url'];
		} else {
			if ( function_exists( 'get_field' ) ) {
				$video_url = get_field( 'cp_video_url', $post_id );
			}
			if ( empty( $video_url ) ) {
				$video_url = get_post_meta( $post_id, 'cp_video_url', true );
			}
			if ( empty( $video_url ) && ! empty( $settings['custom_video_url'] ) ) {
				$video_url = $settings['custom_video_url'];
			}
		}

		$yt_id = $this->extract_youtube_id( $video_url );

		$thumbnail_url = '';
		if ( 'custom' === $settings['thumbnail_source'] && ! empty( $settings['custom_thumbnail']['url'] ) ) {
			$thumbnail_url = $settings['custom_thumbnail']['url'];
		} else {
			if ( has_post_thumbnail( $post_id ) ) {
				$thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full' );
			}
			if ( empty( $thumbnail_url ) && function_exists( 'get_field' ) ) {
				$foto = get_field( 'cp_foto_toko', $post_id );
				if ( is_array( $foto ) && isset( $foto['url'] ) ) {
					$thumbnail_url = $foto['url'];
				} elseif ( is_string( $foto ) ) {
					$thumbnail_url = $foto;
				}
			}
			if ( empty( $thumbnail_url ) && $yt_id ) {
				$thumbnail_url = 'https://i.ytimg.com/vi/' . $yt_id . '/maxresdefault.jpg';
			}
		}

		if ( empty( $thumbnail_url ) ) {
			$thumbnail_url = get_stylesheet_directory_uri() . '/assets/img/hero-video-placeholder.jpg';
		}

		$aspect_class = 'aspect-' . ( ! empty( $settings['aspect_ratio'] ) ? esc_attr( $settings['aspect_ratio'] ) : '16-9' );
		$pulse_class  = ( 'yes' === $settings['play_button_pulse'] ) ? 'has-pulse' : '';
		?>
		<div class="jubelio-single-hero-section">
			<div class="jubelio-single-hero-inner">
				<<?php echo $tag; ?> class="jubelio-single-hero-title">
					<?php echo esc_html( $headline ); ?>
				</<?php echo $tag; ?>>

				<?php if ( ! empty( $subheadline ) ) : ?>
					<p class="jubelio-single-hero-subtitle">
						<?php echo esc_html( $subheadline ); ?>
					</p>
				<?php endif; ?>

				<div class="jubelio-single-hero-video-box <?php echo esc_attr( $aspect_class ); ?>" data-video-url="<?php echo esc_url( $video_url ); ?>">
					<img 
						src="<?php echo esc_url( $thumbnail_url ); ?>" 
						alt="<?php echo esc_attr( $headline ); ?>" 
						fetchpriority="high" 
						loading="eager" 
						decoding="async" 
						width="1280" 
						height="720" 
						class="jubelio-single-hero-poster no-lazyload skip-lazy" 
					/>

					<div class="jubelio-single-hero-overlay"></div>

					<?php $play_btn_text = ! empty( $settings['play_button_text'] ) ? $settings['play_button_text'] : __( 'Putar', 'hello-elementor-child' ); ?>
					<button type="button" class="jubelio-single-hero-play-btn <?php echo esc_attr( $pulse_class ); ?>" aria-label="<?php echo esc_attr( $play_btn_text . ' Video Cerita Pebisnis' ); ?>">
						<svg class="jubelio-play-icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
							<path d="M8 5v14l11-7z"></path>
						</svg>
						<?php if ( ! empty( $play_btn_text ) ) : ?>
							<span class="jubelio-play-btn-text"><?php echo esc_html( $play_btn_text ); ?></span>
						<?php endif; ?>
					</button>

					<div class="jubelio-single-hero-player-target"></div>
				</div>

				<?php if ( 'yes' === $settings['show_nav_button'] ) : 
					$btn_url = ! empty( $settings['nav_button_link']['url'] ) ? $settings['nav_button_link']['url'] : home_url( '/cerita-pebisnis/' );
					$is_ext  = ! empty( $settings['nav_button_link']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
					$btn_txt = ! empty( $settings['nav_button_text'] ) ? $settings['nav_button_text'] : __( 'Lihat Semua Cerita Pebisnis', 'hello-elementor-child' );
				?>
					<div class="jubelio-single-hero-nav-wrap">
						<a href="<?php echo esc_url( $btn_url ); ?>" class="jubelio-single-hero-nav-btn"<?php echo $is_ext; ?>>
							<span><?php echo esc_html( $btn_txt ); ?></span>
							<?php if ( ! empty( $settings['nav_button_icon']['value'] ) ) : ?>
								<span class="jubelio-btn-icon">
									<?php Icons_Manager::render_icon( $settings['nav_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php else : ?>
								<span class="jubelio-btn-arrow" aria-hidden="true">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
										<line x1="5" y1="12" x2="19" y2="12"></line>
										<polyline points="12 5 19 12 12 19"></polyline>
									</svg>
								</span>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
