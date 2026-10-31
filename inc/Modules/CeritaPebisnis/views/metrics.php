<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$layout_type      = ! empty( $settings['layout_type'] ) ? $settings['layout_type'] : 'glass_grid';
$header_position  = ! empty( $settings['header_position'] ) ? $settings['header_position'] : 'bottom';
$show_eyebrow     = ! isset( $settings['show_eyebrow'] ) || 'yes' === $settings['show_eyebrow'];
$eyebrow_text     = ! empty( $settings['eyebrow_text'] ) ? $settings['eyebrow_text'] : __( 'What We Offer ↗', 'hello-elementor-child' );
$title_text       = ! empty( $settings['title_text'] ) ? $settings['title_text'] : __( 'Dampak Nyata untuk Pertumbuhan Bisnis Bersama Jubelio', 'hello-elementor-child' );
$allowed_tags     = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
$title_tag        = ( ! empty( $settings['title_tag'] ) && in_array( $settings['title_tag'], $allowed_tags, true ) ) ? $settings['title_tag'] : 'h2';
$desc_text        = ! empty( $settings['description_text'] ) ? $settings['description_text'] : __( 'Bersama Jubelio, berbagai bisnis telah merasakan peningkatan efisiensi operasional, penjualan, dan pertumbuhan yang signifikan.', 'hello-elementor-child' );
$enable_counter   = ! isset( $settings['enable_counter'] ) || 'yes' === $settings['enable_counter'];
$show_icons       = ! empty( $settings['show_card_icons'] ) && 'yes' === $settings['show_card_icons'];
$metrics_items    = ! empty( $settings['metrics_list'] ) && is_array( $settings['metrics_list'] ) ? $settings['metrics_list'] : array();
$total_items      = count( $metrics_items );

$render_chart = function( $item, $index ) {
	$chart_type = ! empty( $item['chart_type'] ) ? $item['chart_type'] : '';

	if ( empty( $chart_type ) ) {
		$fallbacks  = array( 'bar_chart', 'arch_wave', 'ticks_bar', 'spline_wave', 'growth_area', 'dual_sparkline' );
		$chart_type = isset( $fallbacks[ $index % count( $fallbacks ) ] ) ? $fallbacks[ $index % count( $fallbacks ) ] : 'bar_chart';
	}

	if ( 'custom' === $chart_type && ! empty( $item['custom_chart']['url'] ) ) {
		?>
		<img src="<?php echo esc_url( $item['custom_chart']['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $item['metric_label'] ) ? $item['metric_label'] : 'Metric Chart' ); ?>" class="jubelio-chart-img" loading="lazy" decoding="async" />
		<?php
		return;
	}

	switch ( $chart_type ) {
		case 'bar_chart':
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-bars" aria-hidden="true">
				<rect x="8" y="36" width="28" height="28" rx="4" fill="#FEE2E2" />
				<rect x="48" y="10" width="28" height="54" rx="4" fill="#FF4B26" />
				<rect x="88" y="26" width="28" height="38" rx="4" fill="#FEE2E2" />
				<rect x="128" y="44" width="28" height="20" rx="4" fill="#FEE2E2" />
				<rect x="168" y="32" width="28" height="32" rx="4" fill="#FEE2E2" />
			</svg>
			<?php
			break;

		case 'arch_wave':
			$grad_id = 'archBlueGrad-' . esc_attr( $index );
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-arch" aria-hidden="true">
				<defs>
					<linearGradient id="<?php echo $grad_id; ?>" x1="0" y1="0" x2="0" y2="1">
						<stop offset="0%" stop-color="#2563EB" />
						<stop offset="100%" stop-color="#60A5FA" stop-opacity="0.6" />
					</linearGradient>
				</defs>
				<?php
				$num_lines = 36;
				$start_x   = 16;
				$end_x     = 188;
				$step_x    = ( $end_x - $start_x ) / ( $num_lines - 1 );
				for ( $i = 0; $i < $num_lines; $i++ ) {
					$x   = round( $start_x + $i * $step_x, 1 );
					$rad = ( $i / ( $num_lines - 1 ) ) * M_PI;
					$h   = sin( $rad ) * 40;
					$y1  = round( 62 - $h, 1 );
					$y2  = 62;
					echo '<line x1="' . $x . '" y1="' . $y1 . '" x2="' . $x . '" y2="' . $y2 . '" stroke="url(#' . $grad_id . ')" stroke-width="1.6" stroke-linecap="round" />';
				}
				?>
			</svg>
			<?php
			break;

		case 'ticks_bar':
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-ticks" aria-hidden="true">
				<?php
				$total_ticks = 28;
				$start_x     = 10;
				$end_x       = 194;
				$step        = ( $end_x - $start_x ) / ( $total_ticks - 1 );
				for ( $i = 0; $i < $total_ticks; $i++ ) {
					$x = round( $start_x + $i * $step, 1 );
					if ( $i >= 19 ) {
						$y1     = 8;
						$stroke = '#8B5CF6';
						$w      = 2.4;
					} else {
						$y1     = 36;
						$stroke = '#CBD5E1';
						$w      = 1.8;
					}
					echo '<line x1="' . $x . '" y1="' . $y1 . '" x2="' . $x . '" y2="60" stroke="' . $stroke . '" stroke-width="' . $w . '" stroke-linecap="round" />';
				}
				?>
			</svg>
			<?php
			break;

		case 'spline_wave':
			$filter_id = 'glow-orange-' . esc_attr( $index );
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-spline" aria-hidden="true">
				<defs>
					<filter id="<?php echo $filter_id; ?>" x="-20%" y="-30%" width="140%" height="180%">
						<feGaussianBlur stdDeviation="3.5" result="blur" />
						<feMerge>
							<feMergeNode in="blur" />
							<feMergeNode in="SourceGraphic" />
						</feMerge>
					</filter>
				</defs>
				<path d="M 8 50 Q 22 24 38 46 T 70 28 T 102 50 T 134 30 T 166 12 T 198 40" stroke="#FF5722" stroke-width="3" stroke-linecap="round" fill="none" filter="url(#<?php echo $filter_id; ?>)" />
			</svg>
			<?php
			break;

		case 'growth_area':
			$grad_id = 'emeraldGrad-' . esc_attr( $index );
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-growth" aria-hidden="true">
				<defs>
					<linearGradient id="<?php echo $grad_id; ?>" x1="0" y1="0" x2="0" y2="1">
						<stop offset="0%" stop-color="#10B981" stop-opacity="0.32" />
						<stop offset="100%" stop-color="#10B981" stop-opacity="0.02" />
					</linearGradient>
				</defs>
				<path d="M 10 52 C 50 48 80 36 120 24 C 150 16 180 12 198 8 L 198 62 L 10 62 Z" fill="url(#<?php echo $grad_id; ?>)" />
				<path d="M 10 52 C 50 48 80 36 120 24 C 150 16 180 12 198 8" stroke="#10B981" stroke-width="2.6" stroke-linecap="round" fill="none" />
			</svg>
			<?php
			break;

		case 'dual_sparkline':
		default:
			?>
			<svg viewBox="0 0 204 65" fill="none" xmlns="http://www.w3.org/2000/svg" class="jubelio-chart-svg chart-sparkline" aria-hidden="true">
				<path d="M 10 44 C 40 52 80 28 120 34 C 160 40 180 18 198 12" stroke="#4F46E5" stroke-width="2.4" stroke-linecap="round" fill="none" />
				<path d="M 10 24 C 50 14 90 38 130 20 C 160 8 185 24 198 28" stroke="#06B6D4" stroke-width="2" stroke-dasharray="4 3" stroke-linecap="round" fill="none" />
			</svg>
			<?php
			break;
	}
};

$render_metric_icon = function( $item ) {
	$icon_type = ! empty( $item['metric_icon'] ) ? $item['metric_icon'] : 'storefront';

	if ( 'custom' === $icon_type && ! empty( $item['custom_icon']['value'] ) ) {
		\Elementor\Icons_Manager::render_icon( $item['custom_icon'], array( 'aria-hidden' => 'true' ) );
		return;
	}

	switch ( $icon_type ) {
		case 'storefront':
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
				<path d="M3 9l2.5-6h13L21 9"></path>
				<path d="M10 13a2 2 0 1 0 4 0"></path>
			</svg>
			<?php
			break;

		case 'package':
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
				<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
				<line x1="12" y1="22.08" x2="12" y2="12"></line>
			</svg>
			<?php
			break;

		case 'chart':
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
				<polyline points="17 6 23 6 23 12"></polyline>
			</svg>
			<?php
			break;

		case 'wallet':
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
				<path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
				<circle cx="16" cy="14" r="1.5"></circle>
			</svg>
			<?php
			break;

		case 'users':
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
				<circle cx="9" cy="7" r="4"></circle>
				<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
				<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
			</svg>
			<?php
			break;

		case 'layers':
		default:
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
				<polyline points="2 17 12 22 22 17"></polyline>
				<polyline points="2 12 12 17 22 12"></polyline>
			</svg>
			<?php
			break;
	}
};
?>
<section class="jubelio-metrics-section layout-<?php echo esc_attr( $layout_type ); ?>" data-animated="<?php echo esc_attr( $enable_counter ? 'yes' : 'no' ); ?>">

	<?php if ( 'glass_grid' === $layout_type ) : ?>

		<?php if ( ( $show_eyebrow && ! empty( $eyebrow_text ) ) || ! empty( $title_text ) ) : ?>
			<div class="jubelio-metrics-section-header">
				<?php if ( $show_eyebrow && ! empty( $eyebrow_text ) ) : ?>
					<div class="jubelio-metrics-eyebrow-wrap">
						<span class="jubelio-metrics-eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $title_text ) ) : ?>
					<<?php echo esc_attr( $title_tag ); ?> class="jubelio-metrics-title elementor-heading-title">
						<?php echo esc_html( $title_text ); ?>
					</<?php echo esc_attr( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $desc_text ) ) : ?>
					<p class="jubelio-metrics-desc">
						<?php echo esc_html( $desc_text ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="jubelio-metrics-glass-outer">
			<div class="jubelio-metrics-glass-backdrop">

				<div class="jubelio-metrics-solid-container">

					<div class="jubelio-metrics-grid-3col">
						<?php if ( ! empty( $metrics_items ) ) : ?>
							<?php foreach ( $metrics_items as $index => $item ) :
								$val     = ! empty( $item['metric_value'] ) ? trim( $item['metric_value'] ) : '';
								$label   = ! empty( $item['metric_label'] ) ? trim( $item['metric_label'] ) : '';
								$card_id = ! empty( $item['_id'] ) ? $item['_id'] : $index;
							?>
								<div class="jubelio-metric-card-box elementor-repeater-item-<?php echo esc_attr( $card_id ); ?>">
									<div class="jubelio-metric-card-top">
										<h3 class="jubelio-metric-number">
											<span class="jubelio-metric-number-val" data-raw="<?php echo esc_attr( $val ); ?>">
												<?php echo esc_html( $val ); ?>
											</span>
										</h3>

										<?php if ( ! empty( $label ) ) : ?>
											<h4 class="jubelio-metric-desc">
												<?php echo esc_html( $label ); ?>
											</h4>
										<?php endif; ?>
									</div>

									<div class="jubelio-metric-chart-wrap">
										<?php $render_chart( $item, $index ); ?>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

				</div>

			</div>
		</div>

	<?php elseif ( 'arc' === $layout_type ) : ?>
		<div class="jubelio-metrics-arc-stage">

			<?php if ( 'top' === $header_position ) : ?>
				<div class="jubelio-metrics-center-content header-top">
					<?php if ( $show_eyebrow && ! empty( $eyebrow_text ) ) : ?>
						<div class="jubelio-metrics-eyebrow-wrap">
							<span class="jubelio-metrics-eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>
						</div>
					<?php endif; ?>

					<<?php echo esc_attr( $title_tag ); ?> class="jubelio-metrics-title">
						<?php echo esc_html( $title_text ); ?>
					</<?php echo esc_attr( $title_tag ); ?>>

					<?php if ( ! empty( $desc_text ) ) : ?>
						<p class="jubelio-metrics-desc">
							<?php echo esc_html( $desc_text ); ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="jubelio-metrics-arc-wrap">
				<?php if ( ! empty( $metrics_items ) ) : ?>
					<?php
					foreach ( $metrics_items as $index => $item ) :
						$val   = ! empty( $item['metric_value'] ) ? trim( $item['metric_value'] ) : '';
						$label = ! empty( $item['metric_label'] ) ? trim( $item['metric_label'] ) : '';

						$offset     = $index - ( ( $total_items - 1 ) / 2 );
						$max_deg    = ( $total_items <= 4 ) ? 10.5 : 13.5;
						$deg_step   = ( $total_items > 1 ) ? ( $max_deg * 2 / ( $total_items - 1 ) ) : 0;
						$rot_deg    = round( $offset * $deg_step, 1 );

						$max_y      = ( $total_items <= 4 ) ? 36 : 46;
						$max_offset = max( 1, ( $total_items - 1 ) / 2 );
						$y_drop     = round( pow( abs( $offset ) / $max_offset, 1.8 ) * $max_y, 1 );

						$arc_style  = sprintf( '--arc-rot: %sdeg; --arc-y: %spx;', $rot_deg, $y_drop );
						$card_class = 'jubelio-metric-card jubelio-metric-card-arc elementor-repeater-item-' . esc_attr( ! empty( $item['_id'] ) ? $item['_id'] : $index );
						$card_class .= $show_icons ? ' has-icon' : ' no-icon';
					?>
						<div class="<?php echo esc_attr( $card_class ); ?>" style="<?php echo esc_attr( $arc_style ); ?>">
							<?php if ( $show_icons ) : ?>
								<div class="jubelio-metric-icon-wrap">
									<?php $render_metric_icon( $item ); ?>
								</div>
							<?php endif; ?>
							<div class="jubelio-metric-content">
								<div class="jubelio-metric-value" data-raw="<?php echo esc_attr( $val ); ?>">
									<?php echo esc_html( $val ); ?>
								</div>
								<?php if ( ! empty( $label ) ) : ?>
									<div class="jubelio-metric-label">
										<?php echo esc_html( $label ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<?php if ( 'bottom' === $header_position ) : ?>
				<div class="jubelio-metrics-center-content header-bottom">
					<?php if ( $show_eyebrow && ! empty( $eyebrow_text ) ) : ?>
						<div class="jubelio-metrics-eyebrow-wrap">
							<span class="jubelio-metrics-eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>
						</div>
					<?php endif; ?>

					<<?php echo esc_attr( $title_tag ); ?> class="jubelio-metrics-title">
						<?php echo esc_html( $title_text ); ?>
					</<?php echo esc_attr( $title_tag ); ?>>

					<?php if ( ! empty( $desc_text ) ) : ?>
						<p class="jubelio-metrics-desc">
							<?php echo esc_html( $desc_text ); ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

	<?php else : ?>

		<div class="jubelio-metrics-container">
			<div class="jubelio-metrics-header-col">
				<?php if ( $show_eyebrow && ! empty( $eyebrow_text ) ) : ?>
					<div class="jubelio-metrics-eyebrow-wrap">
						<span class="jubelio-metrics-eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>
					</div>
				<?php endif; ?>

				<<?php echo esc_attr( $title_tag ); ?> class="jubelio-metrics-title">
					<?php echo esc_html( $title_text ); ?>
				</<?php echo esc_attr( $title_tag ); ?>>

				<?php if ( ! empty( $desc_text ) ) : ?>
					<p class="jubelio-metrics-desc">
						<?php echo esc_html( $desc_text ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="jubelio-metrics-cards-grid">
				<?php if ( ! empty( $metrics_items ) ) : ?>
					<?php foreach ( $metrics_items as $index => $item ) :
						$val        = ! empty( $item['metric_value'] ) ? trim( $item['metric_value'] ) : '';
						$label      = ! empty( $item['metric_label'] ) ? trim( $item['metric_label'] ) : '';
						$card_class = 'jubelio-metric-card elementor-repeater-item-' . esc_attr( ! empty( $item['_id'] ) ? $item['_id'] : $index );
						$card_class .= $show_icons ? ' has-icon' : ' no-icon';
					?>
						<div class="<?php echo esc_attr( $card_class ); ?>">
							<?php if ( $show_icons ) : ?>
								<div class="jubelio-metric-icon-wrap">
									<?php $render_metric_icon( $item ); ?>
								</div>
							<?php endif; ?>
							<div class="jubelio-metric-content">
								<div class="jubelio-metric-value" data-raw="<?php echo esc_attr( $val ); ?>">
									<?php echo esc_html( $val ); ?>
								</div>
								<?php if ( ! empty( $label ) ) : ?>
									<div class="jubelio-metric-label">
										<?php echo esc_html( $label ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

	<?php endif; ?>

</section>
