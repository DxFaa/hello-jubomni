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

	<?php if ( 'yes' === $settings['show_image'] ) : ?>
		<div class="jubelio-grid-card-media">
			<?php if ( ! empty( $item['cta_link'] ) ) : ?>
				<a href="<?php echo esc_url( $item['cta_link'] ); ?>" class="jubelio-grid-card-media-link" aria-label="<?php echo esc_attr( $item['client_name'] ); ?>">
			<?php endif; ?>
			<?php if ( ! empty( $item['card_image'] ) ) : ?>
				<img src="<?php echo esc_url( $item['card_image'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
			<?php else : ?>
				<div class="jubelio-grid-card-placeholder">
					<span><?php echo esc_html( $item['client_name'] ); ?></span>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $item['cta_link'] ) ) : ?>
				</a>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_industry_badge'] && ! empty( $item['industry_name'] ) ) : ?>
				<span class="jubelio-grid-card-industry-badge">
					<?php echo esc_html( $item['industry_name'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_logo'] && ( ! empty( $item['company_logo'] ) || ! empty( $item['client_name'] ) ) ) : ?>
				<div class="jubelio-grid-card-logo-tab">
					<?php if ( ! empty( $item['cta_link'] ) ) : ?>
						<a href="<?php echo esc_url( $item['cta_link'] ); ?>" class="jubelio-grid-card-logo-link" aria-label="<?php echo esc_attr( $item['client_name'] ); ?>">
					<?php endif; ?>
					<?php if ( ! empty( $item['company_logo'] ) ) : ?>
						<img src="<?php echo esc_url( $item['company_logo'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
					<?php elseif ( ! empty( $item['client_name'] ) ) : ?>
						<div class="jubelio-grid-card-logo-badge">
							<span><?php echo esc_html( $item['client_name'] ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $item['cta_link'] ) ) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="jubelio-grid-card-body">

		<?php if ( 'yes' === $settings['show_logo'] && 'yes' !== $settings['show_image'] && ( ! empty( $item['company_logo'] ) || ! empty( $item['client_name'] ) ) ) : ?>
			<div class="jubelio-grid-card-logo">
				<?php if ( ! empty( $item['company_logo'] ) ) : ?>
					<img src="<?php echo esc_url( $item['company_logo'] ); ?>" alt="<?php echo esc_attr( $item['client_name'] ); ?>" loading="lazy" />
				<?php elseif ( ! empty( $item['client_name'] ) ) : ?>
					<div class="jubelio-grid-card-logo-badge">
						<span><?php echo esc_html( $item['client_name'] ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( 'yes' === $settings['show_brand_title'] && ! empty( $item['client_name'] ) ) : ?>
			<h3 class="jubelio-grid-card-brand">
				<?php if ( ! empty( $item['cta_link'] ) ) : ?>
					<a href="<?php echo esc_url( $item['cta_link'] ); ?>" class="jubelio-grid-card-title-link"><?php echo esc_html( $item['client_name'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $item['client_name'] ); ?>
				<?php endif; ?>
			</h3>
		<?php endif; ?>

		<?php if ( 'yes' === $settings['show_headline'] && ! empty( $item['headline'] ) ) : ?>
			<h4 class="jubelio-grid-card-headline"><?php echo esc_html( $item['headline'] ); ?></h4>
		<?php endif; ?>

		<?php if ( 'yes' === $settings['show_description'] && ! empty( $item['description'] ) ) : ?>
			<p class="jubelio-grid-card-desc"><?php echo wp_kses_post( $item['description'] ); ?></p>
		<?php endif; ?>

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
