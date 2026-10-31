<?php

namespace Jubelio\Modules\CeritaPebisnis\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jubelio\Modules\CeritaPebisnis\Model;

class SingleQuoteWidget extends Widget_Base {

	public function get_name() {
		return 'jubelio_cerita_pebisnis_single_quote';
	}

	public function get_title() {
		return __( 'Cerita Pebisnis: Kutipan Klien (Quote)', 'hello-elementor-child' );
	}

	public function get_icon() {
		return 'eicon-blockquote';
	}

	public function get_categories() {
		return array( 'jubelio-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'quote', 'kutipan', 'testimoni', 'narasumber', 'cerita pebisnis', 'jubelio' );
	}

	public function get_style_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	public function get_script_depends() {
		return array( 'jubelio-cerita-pebisnis' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_quote',
			array(
				'label' => __( 'Konten Kutipan (Quote)', 'hello-elementor-child' ),
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
					'dynamic' => __( 'Otomatis dari Post / ACF (cp_quote)', 'hello-elementor-child' ),
					'custom'  => __( 'Kustom Manual (Input Sendiri)', 'hello-elementor-child' ),
				),
			)
		);

		$this->add_control(
			'custom_quote_text',
			array(
				'label'       => __( 'Teks Kutipan', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => 'Dengan sistem Jubelio yang terintegrasi otomatis, pengelolaan operasional ribuan pesanan per hari menjadi jauh lebih cepat dan akurat tanpa perlu khawatir stok selisih lagi.',
				'description' => __( 'Kutipan langsung dari klien atau pemilik bisnis.', 'hello-elementor-child' ),
				'condition'   => array(
					'data_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_author_name',
			array(
				'label'       => __( 'Nama Narasumber / Klien', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Hansa Artupas',
				'placeholder' => 'Nama Pemilik / Narasumber',
				'condition'   => array(
					'data_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_author_role',
			array(
				'label'       => __( 'Jabatan & Nama Bisnis', 'hello-elementor-child' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'CEO, BES',
				'placeholder' => 'e.g. CEO, Nama Bisnis',
				'condition'   => array(
					'data_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'show_author_avatar',
			array(
				'label'        => __( 'Tampilkan Foto Profil / Avatar', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'custom_author_avatar',
			array(
				'label'     => __( 'Foto Profil Tokoh', 'hello-elementor-child' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => '',
				),
				'condition' => array(
					'data_source'        => 'custom',
					'show_author_avatar' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_quotation_marks',
			array(
				'label'        => __( 'Tampilkan Tanda Petik (&ldquo; &rdquo;)', 'hello-elementor-child' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ya', 'hello-elementor-child' ),
				'label_off'    => __( 'Tidak', 'hello-elementor-child' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			'quote_max_width',
			array(
				'label'      => __( 'Lebar Maksimal Teks Kutipan (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 400,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 50,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 860,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-quote-blockquote' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'quote_align',
			array(
				'label'     => __( 'Perataan Teks', 'hello-elementor-child' ),
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
					'{{WRAPPER}} .jubelio-single-quote-card'        => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .jubelio-single-quote-blockquote'  => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
					'{{WRAPPER}} .jubelio-single-quote-text'        => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .jubelio-single-quote-author'      => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => __( 'Tata Letak & Jarak', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label'      => __( 'Jarak Antara Kutipan & Penulis (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-quote-blockquote' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_colors',
			array(
				'label' => __( 'Warna Teks', 'hello-elementor-child' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'quote_text_color',
			array(
				'label'     => __( 'Warna Teks Kutipan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-quote-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'author_name_color',
			array(
				'label'     => __( 'Warna Nama Penulis', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#163A50',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-quote-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'author_role_color',
			array(
				'label'     => __( 'Warna Jabatan / Perusahaan', 'hello-elementor-child' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#606C75',
				'selectors' => array(
					'{{WRAPPER}} .jubelio-single-quote-role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label'     => __( 'Foto Profil / Avatar', 'hello-elementor-child' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_author_avatar' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Ukuran Foto (px)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 140,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 56,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-quote-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_border_radius',
			array(
				'label'      => __( 'Radius Sudut (Bulat)', 'hello-elementor-child' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 70,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-quote-avatar' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_bottom_spacing',
			array(
				'label'      => __( 'Jarak ke Nama (px)', 'hello-elementor-child' ),
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
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jubelio-single-quote-avatar-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = get_the_ID();
		$post_data = Model::get( $post_id );

		$quote_text = '';
		if ( 'custom' === $settings['data_source'] && ! empty( $settings['custom_quote_text'] ) ) {
			$quote_text = $settings['custom_quote_text'];
		} else {
			$quote_text = ! empty( $post_data['quote'] ) ? $post_data['quote'] : '';
		}

		if ( empty( $quote_text ) ) {
			$quote_text = __( 'Dengan sistem Jubelio yang terintegrasi otomatis, pengelolaan operasional ribuan pesanan per hari menjadi jauh lebih cepat dan akurat tanpa perlu khawatir stok selisih lagi.', 'hello-elementor-child' );
		}

		$author_name = '';
		if ( 'custom' === $settings['data_source'] && ! empty( $settings['custom_author_name'] ) ) {
			$author_name = $settings['custom_author_name'];
		} else {
			$author_name = ! empty( $post_data['client_person_name'] ) ? $post_data['client_person_name'] : $post_data['client_name'];
		}

		$author_role = '';
		if ( 'custom' === $settings['data_source'] && ! empty( $settings['custom_author_role'] ) ) {
			$author_role = $settings['custom_author_role'];
		} else {
			$author_role = ! empty( $post_data['client_person_role'] ) ? $post_data['client_person_role'] : '';
		}

		$author_avatar = '';
		$show_avatar   = ( 'yes' === $settings['show_author_avatar'] );
		if ( $show_avatar ) {
			if ( 'custom' === $settings['data_source'] && ! empty( $settings['custom_author_avatar']['url'] ) ) {
				$author_avatar = $settings['custom_author_avatar']['url'];
			} else {
				$author_avatar = ! empty( $post_data['client_photo'] ) ? $post_data['client_photo'] : '';
			}
		}

		$show_quotes = ( 'yes' === $settings['show_quotation_marks'] );
		?>
		<div class="jubelio-single-quote-section">
			<div class="jubelio-single-quote-card">
				<blockquote class="jubelio-single-quote-blockquote">
					<p class="jubelio-single-quote-text">
						<?php if ( $show_quotes ) : ?>&ldquo;<?php endif; ?><?php echo esc_html( $quote_text ); ?><?php if ( $show_quotes ) : ?>&rdquo;<?php endif; ?>
					</p>
					<?php if ( ! empty( $author_name ) || ! empty( $author_role ) || ! empty( $author_avatar ) ) : ?>
						<footer class="jubelio-single-quote-author">
							<?php if ( ! empty( $author_avatar ) ) : ?>
								<div class="jubelio-single-quote-avatar-wrap">
									<img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" class="jubelio-single-quote-avatar" loading="lazy" />
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $author_name ) ) : ?>
								<cite class="jubelio-single-quote-name"><?php echo esc_html( $author_name ); ?></cite>
							<?php endif; ?>
							<?php if ( ! empty( $author_role ) ) : ?>
								<span class="jubelio-single-quote-role"><?php echo esc_html( $author_role ); ?></span>
							<?php endif; ?>
						</footer>
					<?php endif; ?>
				</blockquote>
			</div>
		</div>
		<?php
	}
}
