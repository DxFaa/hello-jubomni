<?php
/**
 * View Partial: Cerita Pebisnis Section Grid
 *
 * @package Jubelio
 * @var array $items Array of normalized card items.
 * @var array $industries Array of industry slug => name pairs.
 * @var array $settings Widget settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cols_class = 'jubelio-grid-cols-' . intval( $settings['grid_columns'] );
?>
<div class="jubelio-cerita-grid-section">

	<!-- Section Eyebrow -->
	<?php if ( 'yes' === $settings['show_eyebrow'] && ! empty( $settings['eyebrow_text'] ) ) : ?>
		<div class="jubelio-grid-eyebrow-wrap">
			<span class="jubelio-grid-eyebrow"><?php echo esc_html( $settings['eyebrow_text'] ); ?></span>
		</div>
	<?php endif; ?>

	<!-- Industry Filter Tabs -->
	<?php if ( 'yes' === $settings['show_filter_tabs'] && ! empty( $industries ) ) : ?>
		<div class="jubelio-grid-filter-tabs">
			<button type="button" class="jubelio-tab-btn active" data-filter="all">
				<?php echo esc_html( $settings['filter_all_label'] ); ?>
			</button>
			<?php foreach ( $industries as $slug => $name ) : ?>
				<button type="button" class="jubelio-tab-btn" data-filter="<?php echo esc_attr( $slug ); ?>">
					<?php echo esc_html( $name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- Cards Grid Container -->
	<div class="jubelio-grid-cards-container <?php echo esc_attr( $cols_class ); ?>">
		<?php
		foreach ( $items as $item ) {
			include __DIR__ . '/card.php';
		}
		?>
	</div>

</div>
