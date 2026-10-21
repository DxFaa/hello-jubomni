<?php
/**
 * View Partial: Cerita Pebisnis Section Grid & Touch Slider
 *
 * @package Jubelio
 * @var array  $items Array of normalized card items.
 * @var array  $industries Array of industry slug => name pairs.
 * @var array  $settings Widget settings.
 * @var array  $swiper_config Swiper config array.
 * @var string $layout_type 'slider' or 'grid'.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$layout_type      = ! empty( $settings['layout_type'] ) ? $settings['layout_type'] : ( ! empty( $layout_type ) ? $layout_type : 'slider' );
$show_navigation  = ! isset( $settings['show_navigation'] ) || 'yes' === $settings['show_navigation'];
$show_pagination  = ! isset( $settings['show_pagination'] ) || 'yes' === $settings['show_pagination'];
$show_filter_tabs = ! isset( $settings['show_filter_tabs'] ) || 'yes' === $settings['show_filter_tabs'];
$show_eyebrow     = ! isset( $settings['show_eyebrow'] ) || 'yes' === $settings['show_eyebrow'];
$cols_class       = 'jubelio-grid-cols-' . ( isset( $settings['grid_columns'] ) ? intval( $settings['grid_columns'] ) : 3 );
$filter_align     = ! empty( $settings['filter_align'] ) ? $settings['filter_align'] : 'center';
?>
<div class="jubelio-cerita-grid-section" data-layout="<?php echo esc_attr( $layout_type ); ?>">

	<?php if ( ! empty( $is_sample_preview ) ) : ?>
		<div class="jubelio-grid-editor-notice">
			<strong><?php esc_html_e( 'Mode Demo Preview:', 'hello-elementor-child' ); ?></strong>
			<?php esc_html_e( 'Menampilkan kartu sampel Cerita Pebisnis Jubelio untuk live preview & styling.', 'hello-elementor-child' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $show_eyebrow && ! empty( $settings['eyebrow_text'] ) ) : ?>
		<div class="jubelio-grid-header-row">
			<div class="jubelio-grid-eyebrow-wrap">
				<span class="jubelio-grid-eyebrow"><?php echo esc_html( $settings['eyebrow_text'] ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $show_filter_tabs && ! empty( $industries ) ) : ?>
		<div class="jubelio-filter-bar-wrap align-<?php echo esc_attr( $filter_align ); ?>">
			<div class="jubelio-filter-pill-container" role="tablist">
				<button type="button" class="jubelio-filter-pill-btn active" data-filter="all" role="tab" aria-selected="true">
					<?php echo esc_html( ! empty( $settings['filter_all_label'] ) ? $settings['filter_all_label'] : __( 'Semua', 'hello-elementor-child' ) ); ?>
				</button>
				<?php foreach ( $industries as $slug => $name ) : ?>
					<button type="button" class="jubelio-filter-pill-btn" data-filter="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="false">
						<?php echo esc_html( $name ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 'slider' === $layout_type ) : ?>
		<div class="jubelio-slider-main-wrap">

			<?php if ( $show_navigation ) : ?>
				<button type="button" class="jubelio-slider-btn jubelio-slider-prev jubelio-side-nav jubelio-side-prev" aria-label="<?php esc_attr_e( 'Previous Slide', 'hello-elementor-child' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<line x1="19" y1="12" x2="5" y2="12"></line>
						<polyline points="12 19 5 12 12 5"></polyline>
					</svg>
				</button>
			<?php endif; ?>

			<div class="swiper jubelio-cards-swiper" data-swiper-config="<?php echo esc_attr( wp_json_encode( ! empty( $swiper_config ) ? $swiper_config : array() ) ); ?>">
				<div class="swiper-wrapper">
					<?php foreach ( $items as $item ) : ?>
						<div class="swiper-slide jubelio-swiper-slide" data-industry="<?php echo esc_attr( $item['industry_slug'] ); ?>">
							<?php include __DIR__ . '/card.php'; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $show_navigation ) : ?>
				<button type="button" class="jubelio-slider-btn jubelio-slider-next jubelio-side-nav jubelio-side-next" aria-label="<?php esc_attr_e( 'Next Slide', 'hello-elementor-child' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<line x1="5" y1="12" x2="19" y2="12"></line>
						<polyline points="12 5 19 12 12 19"></polyline>
					</svg>
				</button>
			<?php endif; ?>

		</div>

		<div class="jubelio-slider-footer">
			<?php if ( $show_pagination ) : ?>
				<div class="swiper-pagination jubelio-swiper-pagination"></div>
			<?php endif; ?>

			<?php if ( $show_navigation ) : ?>
				<div class="jubelio-slider-nav-wrap jubelio-mobile-only">
					<button type="button" class="jubelio-slider-btn jubelio-slider-prev" aria-label="<?php esc_attr_e( 'Previous Slide', 'hello-elementor-child' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<line x1="19" y1="12" x2="5" y2="12"></line>
							<polyline points="12 19 5 12 12 5"></polyline>
						</svg>
					</button>
					<button type="button" class="jubelio-slider-btn jubelio-slider-next" aria-label="<?php esc_attr_e( 'Next Slide', 'hello-elementor-child' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<line x1="5" y1="12" x2="19" y2="12"></line>
							<polyline points="12 5 19 12 12 19"></polyline>
						</svg>
					</button>
				</div>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="jubelio-grid-cards-container <?php echo esc_attr( $cols_class ); ?>">
			<?php
			foreach ( $items as $item ) {
				include __DIR__ . '/card.php';
			}
			?>
		</div>
	<?php endif; ?>

</div>
