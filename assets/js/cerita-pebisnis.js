/**
 * Jubelio - Cerita Pebisnis Interactive Script
 * Handles Industry Filter Tabs for Section 2 and Elementor live preview.
 */

(function($) {
	'use strict';

	/**
	 * Initialize Industry Filter Tabs for Section 2 Grid
	 */
	function initGridFilterTabs($scope) {
		var $section = $scope ? $scope.find('.jubelio-cerita-grid-section') : $('.jubelio-cerita-grid-section');

		$section.each(function() {
			var $this = $(this);
			var $tabs = $this.find('.jubelio-tab-btn');
			var $cards = $this.find('.jubelio-grid-card');

			$tabs.off('click').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var filter = $btn.attr('data-filter');

				$tabs.removeClass('active');
				$btn.addClass('active');

				if (filter === 'all') {
					$cards.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 220);
				} else {
					$cards.each(function() {
						var $card = $(this);
						var ind = $card.attr('data-industry');
						if (ind === filter) {
							$card.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 220);
						} else {
							$card.addClass('hidden');
						}
					});
				}
			});
		});
	}

	$(document).ready(function() {
		initGridFilterTabs();
	});

	// Hook into Elementor frontend for live preview updates
	$(window).on('elementor/frontend/init', function() {
		if (window.elementorFrontend && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis_grid.default', function($scope) {
				initGridFilterTabs($scope);
			});
		}
	});

})(jQuery);
