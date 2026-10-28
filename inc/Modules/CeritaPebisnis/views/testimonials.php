<?php
/**
 * View Partial: Cerita Pebisnis Testimonials (Kata Pelanggan Tentang Jubelio)
 * Interactive Centered Slider with Dynamic Bullets & Side Peeking Cards
 *
 * @package Jubelio
 * @var array $items List of normalized testimonial card items.
 * @var array $settings Widget settings.
 * @var array $swiper_config Swiper JSON configuration array.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_header      = ! isset( $settings['show_header'] ) || 'yes' === $settings['show_header'];
$title_text       = ! empty( $settings['title_text'] ) ? $settings['title_text'] : __( 'Kata Pelanggan Tentang Jubelio', 'hello-elementor-child' );
$allowed_tags     = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
$title_tag        = ( ! empty( $settings['title_tag'] ) && in_array( $settings['title_tag'], $allowed_tags, true ) ) ? $settings['title_tag'] : 'h2';
$desc_text        = ! empty( $settings['description_text'] ) ? $settings['description_text'] : __( 'Simak langsung pengalaman mereka dalam menggunakan Jubelio untuk mengembangkan bisnis.', 'hello-elementor-child' );
$show_action_link = ! empty( $settings['show_action_link'] ) && 'yes' === $settings['show_action_link'];
$action_text      = ! empty( $settings['action_link_text'] ) ? $settings['action_link_text'] : __( 'Lihat Semua Cerita Pebisnis →', 'hello-elementor-child' );
$action_url       = ! empty( $settings['action_link_url']['url'] ) ? esc_url( $settings['action_link_url']['url'] ) : home_url( '/cerita-pebisnis/' );
$action_external  = ! empty( $settings['action_link_url']['is_external'] ) ? ' target="_blank"' : '';

$rel_parts = array();
if ( ! empty( $settings['action_link_url']['is_external'] ) ) {
	$rel_parts[] = 'noopener';
	$rel_parts[] = 'noreferrer';
}
if ( ! empty( $settings['action_link_url']['nofollow'] ) ) {
	$rel_parts[] = 'nofollow';
}
$action_rel = ! empty( $rel_parts ) ? ' rel="' . esc_attr( implode( ' ', array_unique( $rel_parts ) ) ) . '"' : '';

$show_navigation  = ! isset( $settings['show_navigation'] ) || 'yes' === $settings['show_navigation'];
$show_pagination  = ! isset( $settings['show_pagination'] ) || 'yes' === $settings['show_pagination'];
?>
<section class="jubelio-testimonials-section" aria-label="<?php echo esc_attr( $title_text ); ?>">

	<?php if ( $show_header ) : ?>
		<div class="jubelio-testimonials-header">
			<?php if ( ! empty( $title_text ) ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="jubelio-testimonials-title elementor-heading-title">
					<?php echo esc_html( $title_text ); ?>
				</<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( ! empty( $desc_text ) ) : ?>
				<p class="jubelio-testimonials-desc">
					<?php echo esc_html( $desc_text ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $show_action_link && ! empty( $action_text ) ) : ?>
				<div class="jubelio-testimonials-action-wrap">
					<a href="<?php echo $action_url; ?>" class="jubelio-testimonials-action-link"<?php echo $action_external . $action_rel; ?>>
						<span><?php echo esc_html( $action_text ); ?></span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="jubelio-testimonials-slider-stage">

		<?php if ( $show_navigation ) : ?>
			<button type="button" class="jubelio-slider-btn jubelio-slider-prev jubelio-side-nav jubelio-side-prev" aria-label="<?php esc_attr_e( 'Previous Testimonial', 'hello-elementor-child' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="19" y1="12" x2="5" y2="12"></line>
					<polyline points="12 19 5 12 12 5"></polyline>
				</svg>
			</button>
		<?php endif; ?>

		<div class="swiper jubelio-testimonials-swiper" data-swiper-config="<?php echo esc_attr( wp_json_encode( ! empty( $swiper_config ) ? $swiper_config : array() ) ); ?>">
			<div class="swiper-wrapper">
				<?php if ( ! empty( $items ) ) : ?>
					<?php foreach ( $items as $index => $item ) :
						$target_link = ! empty( $item['cta_link'] ) ? $item['cta_link'] : '#';
						$img_url     = ! empty( $item['card_image'] ) ? $item['card_image'] : '';
						$logo_url    = ! empty( $item['company_logo'] ) ? $item['company_logo'] : '';
						$client_name = ! empty( $item['client_name'] ) ? $item['client_name'] : ( ! empty( $item['title'] ) ? $item['title'] : 'Client' );
						$quote_text  = ! empty( $item['quote'] ) ? $item['quote'] : ( ! empty( $item['description'] ) ? $item['description'] : '' );
						$author_name = ! empty( $item['client_person_name'] ) ? $item['client_person_name'] : $client_name;
						$author_role = ! empty( $item['client_person_role'] ) ? $item['client_person_role'] : 'Owner ' . $client_name;
					?>
						<div class="swiper-slide jubelio-testimonial-slide">
							<a href="<?php echo esc_url( $target_link ); ?>" class="jubelio-testimonial-card" aria-label="<?php echo esc_attr( $author_name . ' - ' . $client_name ); ?>">

								<?php /* Left Media Column: Client Photo with Overlapping Logo Badge */ ?>
								<div class="jubelio-testimonial-media">
									<?php if ( ! empty( $img_url ) ) : ?>
										<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" class="jubelio-testimonial-photo" loading="lazy" decoding="async" />
									<?php else : ?>
										<div class="jubelio-testimonial-photo-placeholder" aria-hidden="true">
											<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
												<rect x="3" y="3" width="18" height="18" rx="4" ry="4"></rect>
												<circle cx="8.5" cy="8.5" r="1.5"></circle>
												<polyline points="21 15 16 10 5 21"></polyline>
											</svg>
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $logo_url ) || ! empty( $client_name ) ) : ?>
										<div class="jubelio-testimonial-logo-tab">
											<?php if ( ! empty( $logo_url ) ) : ?>
												<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $client_name ); ?>" class="jubelio-testimonial-logo-img" loading="lazy" decoding="async" />
											<?php else : ?>
												<span class="jubelio-testimonial-logo-text"><?php echo esc_html( $client_name ); ?></span>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>

								<?php /* Right Content Column: Quote and Author Info */ ?>
								<div class="jubelio-testimonial-content">
									<?php if ( ! empty( $quote_text ) ) : ?>
										<blockquote class="jubelio-testimonial-quote">
											“<?php echo esc_html( trim( $quote_text, "\"' \t\n\r\0\x0B" ) ); ?>”
										</blockquote>
									<?php endif; ?>

									<div class="jubelio-testimonial-author-box">
										<?php if ( ! empty( $author_name ) ) : ?>
											<div class="jubelio-testimonial-author-name">
												<?php echo esc_html( $author_name ); ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $author_role ) ) : ?>
											<div class="jubelio-testimonial-author-role">
												<?php echo esc_html( $author_role ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>

							</a>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $show_navigation ) : ?>
			<button type="button" class="jubelio-slider-btn jubelio-slider-next jubelio-side-nav jubelio-side-next" aria-label="<?php esc_attr_e( 'Next Testimonial', 'hello-elementor-child' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>
			</button>
		<?php endif; ?>

	</div>

	<?php if ( $show_pagination || $show_navigation ) : ?>
		<div class="jubelio-slider-footer jubelio-testimonials-footer">
			<?php if ( $show_pagination ) : ?>
				<div class="swiper-pagination jubelio-swiper-pagination"></div>
			<?php endif; ?>

			<?php if ( $show_navigation ) : ?>
				<div class="jubelio-slider-nav-wrap jubelio-mobile-only">
					<button type="button" class="jubelio-slider-btn jubelio-slider-prev" aria-label="<?php esc_attr_e( 'Previous Slide', 'hello-elementor-child' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<line x1="19" y1="12" x2="5" y2="12"></line>
							<polyline points="12 19 5 12 12 5"></polyline>
						</svg>
					</button>
					<button type="button" class="jubelio-slider-btn jubelio-slider-next" aria-label="<?php esc_attr_e( 'Next Slide', 'hello-elementor-child' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<line x1="5" y1="12" x2="19" y2="12"></line>
							<polyline points="12 5 19 12 12 19"></polyline>
						</svg>
					</button>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</section>
