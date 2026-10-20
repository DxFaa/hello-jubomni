<?php
/**
 * View Partial: Single Cerita Pebisnis Card
 *
 * @package Jubelio
 * @var array $item Normalized card data from Model.
 * @var array $settings Widget settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="jubelio-grid-card" data-industry="<?php echo esc_attr( $item['industry_slug'] ); ?>">

	<!-- Card Image with Industry Pill Overlay -->
	<?php if ( 'yes' === $settings['show_image'] ) : ?>
		<div class="jubelio-grid-card-media">
			<?php if ( ! empty( $item['card_image'] ) ) : ?>
				<img src="<?php echo esc_url( $item['card_image'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
			<?php else : ?>
				<div class="jubelio-grid-card-placeholder">
					<span><?php echo esc_html( $item['client_name'] ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_industry_badge'] && ! empty( $item['industry_name'] ) ) : ?>
				<span class="jubelio-grid-card-industry-badge">
					<?php echo esc_html( $item['industry_name'] ); ?>
				</span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<!-- Card Body -->
	<div class="jubelio-grid-card-body">

		<!-- Brand Logo -->
		<?php if ( 'yes' === $settings['show_logo'] && ! empty( $item['company_logo'] ) ) : ?>
			<div class="jubelio-grid-card-logo">
				<img src="<?php echo esc_url( $item['company_logo'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
			</div>
		<?php endif; ?>

		<!-- Brand Name / Client Name -->
		<?php if ( 'yes' === $settings['show_brand_title'] && ! empty( $item['client_name'] ) ) : ?>
			<h3 class="jubelio-grid-card-brand"><?php echo esc_html( $item['client_name'] ); ?></h3>
		<?php endif; ?>

		<!-- Headline / Key Benefit -->
		<?php if ( 'yes' === $settings['show_headline'] && ! empty( $item['headline'] ) ) : ?>
			<h4 class="jubelio-grid-card-headline"><?php echo esc_html( $item['headline'] ); ?></h4>
		<?php endif; ?>

		<!-- Description -->
		<?php if ( 'yes' === $settings['show_description'] && ! empty( $item['description'] ) ) : ?>
			<p class="jubelio-grid-card-desc"><?php echo wp_kses_post( $item['description'] ); ?></p>
		<?php endif; ?>

		<!-- Action Link (CTA) -->
		<?php if ( 'yes' === $settings['show_cta_link'] && ! empty( $settings['cta_text'] ) ) : ?>
			<div class="jubelio-grid-card-footer">
				<a href="<?php echo esc_url( $item['cta_link'] ); ?>" class="jubelio-grid-card-link">
					<span><?php echo esc_html( $settings['cta_text'] ); ?></span>
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<line x1="5" y1="12" x2="19" y2="12"></line>
						<polyline points="12 5 19 12 12 19"></polyline>
					</svg>
				</a>
			</div>
		<?php endif; ?>

	</div>

</div>
