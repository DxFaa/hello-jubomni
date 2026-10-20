<?php
/**
 * Shortcode & Rendering Engine for Cerita Pebisnis
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Render Function for Cerita Pebisnis Section
 *
 * @param array $settings Configuration options (from Elementor widget or shortcode attributes).
 * @return string HTML output.
 */
function jubelio_render_cerita_pebisnis( $settings = array() ) {
	// Normalize settings
	$defaults = array(
		'layout'           => 'grid', // 'grid', 'slider', 'spotlight'
		'columns'          => 3,
		'posts_per_page'   => 6,
		'category'         => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'show_filter'      => 'no',
		'filter_all_label' => __( 'Semua Cerita', 'hello-elementor-child' ),
		'show_logo'        => 'yes',
		'show_category'    => 'yes',
		'show_photo'       => 'yes',
		'show_rating'      => 'yes',
		'show_metric'      => 'yes',
		'show_quote'       => 'yes',
		'show_name'        => 'yes',
		'show_role'        => 'yes',
		'show_video_btn'   => 'yes',
		'btn_text'         => __( 'Tonton Video', 'hello-elementor-child' ),
		'readmore_text'    => __( 'Baca Cerita', 'hello-elementor-child' ),
		// Slider options
		'autoplay'         => 'yes',
		'autoplay_speed'   => 5000,
		'arrows'           => 'yes',
		'dots'             => 'yes',
		'infinite'         => 'yes',
		// Custom ACF field mapping (if user has custom field names)
		'field_client_name'   => '',
		'field_client_role'   => '',
		'field_company_name'  => '',
		'field_company_logo'  => '',
		'field_client_photo'  => '',
		'field_quote'         => '',
		'field_metric_badge'  => '',
		'field_rating'        => '',
		'field_video_url'     => '',
		'field_category'      => '',
	);

	$settings = wp_parse_args( $settings, $defaults );

	// Build WP_Query args
	$query_args = array(
		'post_type'      => 'cerita-pebisnis',
		'post_status'    => 'publish',
		'posts_per_page' => intval( $settings['posts_per_page'] ),
		'orderby'        => sanitize_key( $settings['orderby'] ),
		'order'          => sanitize_text_field( $settings['order'] ),
	);

	// Category taxonomy query if specified
	if ( ! empty( $settings['category'] ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'kategori_cerita_pebisnis',
				'field'    => is_numeric( $settings['category'] ) ? 'term_id' : 'slug',
				'terms'    => $settings['category'],
			),
		);
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '<div class="jubelio-cp-empty"><p>' . esc_html__( 'Belum ada Cerita Pebisnis yang dipublikasikan.', 'hello-elementor-child' ) . '</p></div>';
	}

	// Prepare ACF mapping array
	$field_keys = array(
		'client_name'  => $settings['field_client_name'],
		'client_role'  => $settings['field_client_role'],
		'company_name' => $settings['field_company_name'],
		'company_logo' => $settings['field_company_logo'],
		'client_photo' => $settings['field_client_photo'],
		'quote'        => $settings['field_quote'],
		'metric_badge' => $settings['field_metric_badge'],
		'rating'       => $settings['field_rating'],
		'video_url'    => $settings['field_video_url'],
		'category'     => $settings['field_category'],
	);

	// Collect items & categories for filter tabs
	$items        = array();
	$filter_terms = array();

	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id   = get_the_ID();
		$item_data = jubelio_get_cerita_pebisnis_data( $post_id, $field_keys );
		$items[]   = $item_data;

		if ( ! empty( $item_data['category_slug'] ) ) {
			$filter_terms[ $item_data['category_slug'] ] = $item_data['category_name'];
		}
	}
	wp_reset_postdata();

	$unique_id = 'jubelio-cp-' . wp_rand( 1000, 9999 );
	$columns_class = 'jubelio-cp-cols-' . intval( $settings['columns'] );
	$layout_class  = 'jubelio-cp-layout-' . sanitize_html_class( $settings['layout'] );

	ob_start();
	?>
	<div class="jubelio-cp-wrapper <?php echo esc_attr( $layout_class . ' ' . $columns_class ); ?>" id="<?php echo esc_attr( $unique_id ); ?>">

		<?php
		// Optional Filter Tabs (for Grid Layout)
		if ( 'yes' === $settings['show_filter'] && ! empty( $filter_terms ) && 'grid' === $settings['layout'] ) :
			?>
			<div class="jubelio-cp-filter-tabs">
				<button type="button" class="jubelio-cp-tab-btn active" data-filter="all">
					<?php echo esc_html( $settings['filter_all_label'] ); ?>
				</button>
				<?php foreach ( $filter_terms as $slug => $name ) : ?>
					<button type="button" class="jubelio-cp-tab-btn" data-filter="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( 'spotlight' === $settings['layout'] && ! empty( $items ) ) :
			// Spotlight Layout: 1 Featured Card on left/top + Grid of companion cards
			$featured = array_shift( $items );
			?>
			<div class="jubelio-cp-spotlight-container">
				<div class="jubelio-cp-spotlight-hero">
					<?php jubelio_render_single_card( $featured, $settings, true ); ?>
				</div>
				<?php if ( ! empty( $items ) ) : ?>
					<div class="jubelio-cp-spotlight-grid">
						<?php foreach ( $items as $item ) : ?>
							<?php jubelio_render_single_card( $item, $settings, false ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

		<?php elseif ( 'slider' === $settings['layout'] ) :
			// Slider / Carousel Layout
			$slider_data = array(
				'slidesToShow'   => intval( $settings['columns'] ),
				'autoplay'       => 'yes' === $settings['autoplay'],
				'autoplaySpeed'  => intval( $settings['autoplay_speed'] ),
				'arrows'         => 'yes' === $settings['arrows'],
				'dots'           => 'yes' === $settings['dots'],
				'infinite'       => 'yes' === $settings['infinite'],
			);
			?>
			<div class="jubelio-cp-slider" data-slick-config="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<div class="jubelio-cp-slide-item">
						<?php jubelio_render_single_card( $item, $settings, false ); ?>
					</div>
				<?php endforeach; ?>
			</div>

		<?php else :
			// Standard Grid Layout
			?>
			<div class="jubelio-cp-grid">
				<?php foreach ( $items as $item ) : ?>
					<?php jubelio_render_single_card( $item, $settings, false ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<!-- Video Modal Lightbox (Global for this instance) -->
		<div class="jubelio-cp-modal" id="<?php echo esc_attr( $unique_id ); ?>-modal" aria-hidden="true" role="dialog">
			<div class="jubelio-cp-modal-backdrop"></div>
			<div class="jubelio-cp-modal-dialog">
				<button type="button" class="jubelio-cp-modal-close" aria-label="<?php esc_attr_e( 'Tutup Video', 'hello-elementor-child' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
				<div class="jubelio-cp-modal-body">
					<div class="jubelio-cp-video-frame-container">
						<iframe src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render a Single Cerita Pebisnis Card
 *
 * @param array $item Normalized card data.
 * @param array $settings Widget / shortcode settings.
 * @param bool  $is_featured Is featured spotlight card.
 */
function jubelio_render_single_card( $item, $settings, $is_featured = false ) {
	$card_classes = array( 'jubelio-cp-card' );
	if ( $is_featured ) {
		$card_classes[] = 'jubelio-cp-card-featured';
	}
	if ( ! empty( $item['category_slug'] ) ) {
		$card_classes[] = 'category-' . sanitize_html_class( $item['category_slug'] );
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" data-category="<?php echo esc_attr( $item['category_slug'] ); ?>">
		<div class="jubelio-cp-card-inner">

			<!-- Card Top Header: Logo, Category Badge, Rating -->
			<div class="jubelio-cp-card-header">
				<div class="jubelio-cp-card-header-left">
					<?php if ( 'yes' === $settings['show_logo'] && ! empty( $item['company_logo'] ) ) : ?>
						<div class="jubelio-cp-company-logo">
							<img src="<?php echo esc_url( $item['company_logo'] ); ?>" alt="<?php echo esc_attr( ! empty( $item['company_name'] ) ? $item['company_name'] : $item['client_name'] ); ?>" loading="lazy" />
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_category'] && ! empty( $item['category_name'] ) ) : ?>
						<span class="jubelio-cp-badge-category">
							<?php echo esc_html( $item['category_name'] ); ?>
						</span>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === $settings['show_rating'] ) : ?>
					<div class="jubelio-cp-stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rating %s dari 5', 'hello-elementor-child' ), $item['rating'] ) ); ?>">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<svg class="jubelio-cp-star <?php echo $i <= $item['rating'] ? 'filled' : 'empty'; ?>" width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
							</svg>
						<?php endfor; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Card Metric Badge Highlight (e.g. "+350% Order / Hari") -->
			<?php if ( 'yes' === $settings['show_metric'] && ! empty( $item['metric_badge'] ) ) : ?>
				<div class="jubelio-cp-metric-box">
					<span class="jubelio-cp-metric-icon">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
						</svg>
					</span>
					<span class="jubelio-cp-metric-text"><?php echo esc_html( $item['metric_badge'] ); ?></span>
				</div>
			<?php endif; ?>

			<!-- Card Testimonial Quote -->
			<?php if ( 'yes' === $settings['show_quote'] && ! empty( $item['quote'] ) ) : ?>
				<div class="jubelio-cp-quote-box">
					<svg class="jubelio-cp-quote-mark" width="28" height="24" viewBox="0 0 28 24" fill="currentColor">
						<path d="M12.18 0C6.67 2.45 2.8 7.35 1.05 13.93c-.7 2.63-.7 5.07-.35 7.17.35 2.1 1.75 2.9 3.85 2.9 2.45 0 4.2-.88 5.43-2.45 1.05-1.58 1.58-3.33 1.58-5.25 0-3.85-2.28-5.95-6.65-5.95.18-2.63 1.4-5.25 3.68-7.7L12.18 0zm15.4 0c-5.51 2.45-9.38 7.35-11.13 13.93-.7 2.63-.7 5.07-.35 7.17.35 2.1 1.75 2.9 3.85 2.9 2.45 0 4.2-.88 5.43-2.45 1.05-1.58 1.58-3.33 1.58-5.25 0-3.85-2.28-5.95-6.65-5.95.18-2.63 1.4-5.25 3.68-7.7L27.58 0z"/>
					</svg>
					<blockquote class="jubelio-cp-quote-text">
						"<?php echo wp_kses_post( $item['quote'] ); ?>"
					</blockquote>
				</div>
			<?php endif; ?>

			<!-- Card Footer: Author Profile & Video / Link Button -->
			<div class="jubelio-cp-card-footer">
				<div class="jubelio-cp-author">
					<?php if ( 'yes' === $settings['show_photo'] ) : ?>
						<div class="jubelio-cp-author-avatar">
							<?php if ( ! empty( $item['client_photo'] ) ) : ?>
								<img src="<?php echo esc_url( $item['client_photo'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
							<?php else : ?>
								<div class="jubelio-cp-avatar-placeholder">
									<?php echo esc_html( strtoupper( mb_substr( $item['client_name'], 0, 1 ) ) ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="jubelio-cp-author-info">
						<?php if ( 'yes' === $settings['show_name'] ) : ?>
							<h4 class="jubelio-cp-author-name"><?php echo esc_html( $item['client_name'] ); ?></h4>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_role'] ) : ?>
							<p class="jubelio-cp-author-role">
								<?php
								$role_str = $item['client_role'];
								if ( ! empty( $item['company_name'] ) ) {
									$role_str = ! empty( $role_str ) ? $role_str . ', ' . $item['company_name'] : $item['company_name'];
								}
								echo esc_html( $role_str );
								?>
							</p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Video Play Button or Link -->
				<?php if ( 'yes' === $settings['show_video_btn'] && ! empty( $item['video_url'] ) ) : ?>
					<button type="button" class="jubelio-cp-btn-video" data-video-url="<?php echo esc_url( $item['video_url'] ); ?>" aria-label="<?php echo esc_attr( $settings['btn_text'] ); ?>">
						<span class="jubelio-cp-btn-icon">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
								<polygon points="5 3 19 12 5 21 5 3"/>
							</svg>
						</span>
						<span class="jubelio-cp-btn-text"><?php echo esc_html( $settings['btn_text'] ); ?></span>
					</button>
				<?php elseif ( ! empty( $item['permalink'] ) && ! $is_featured ) : ?>
					<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="jubelio-cp-link-story">
						<span><?php echo esc_html( $settings['readmore_text'] ); ?></span>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
							<line x1="5" y1="12" x2="19" y2="12"></line>
							<polyline points="12 5 19 12 12 19"></polyline>
						</svg>
					</a>
				<?php endif; ?>
			</div>

		</div>
	</div>
	<?php
}

/**
 * Register Shortcode [cerita_pebisnis]
 */
add_shortcode( 'cerita_pebisnis', 'jubelio_cerita_pebisnis_shortcode' );
function jubelio_cerita_pebisnis_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'layout'         => 'grid',
		'columns'        => 3,
		'posts_per_page' => 6,
		'category'       => '',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'show_filter'    => 'no',
		'show_logo'      => 'yes',
		'show_category'  => 'yes',
		'show_photo'     => 'yes',
		'show_rating'    => 'yes',
		'show_metric'    => 'yes',
		'show_quote'     => 'yes',
		'show_name'      => 'yes',
		'show_role'      => 'yes',
		'show_video_btn' => 'yes',
		'btn_text'       => __( 'Tonton Video', 'hello-elementor-child' ),
		'autoplay'       => 'yes',
		'arrows'         => 'yes',
		'dots'           => 'yes',
	), $atts, 'cerita_pebisnis' );

	return jubelio_render_cerita_pebisnis( $atts );
}
