/**
 * Jubelio - Cerita Pebisnis Interactive Script
 * Handles Unified Segmented Pill Filtering, Swiper Touch Slider & Responsive Navbar
 */

(function($) {
	'use strict';

	/**
	 * Initialize Cerita Pebisnis Slider & Unified Pill Filters
	 */
	function initCeritaPebisnis($scope) {
		var $sections = $scope ? $scope.find('.jubelio-cerita-grid-section') : $('.jubelio-cerita-grid-section');

		$sections.each(function() {
			var $section = $(this);
			var $swiperEl = $section.find('.jubelio-cards-swiper');
			var layoutType = $section.attr('data-layout') || 'slider';
			var isSlider = (layoutType === 'slider') && $swiperEl.length;
			var swiperInstance = null;

			// 1. Initialize Swiper Touch Slider
			if (isSlider && typeof Swiper !== 'undefined') {
				var configAttr = $swiperEl.attr('data-swiper-config');
				var customConfig = {};
				try {
					if (configAttr) {
						customConfig = JSON.parse(configAttr);
					}
				} catch (err) {
					customConfig = {};
				}

				// Find navigation buttons scoped to this widget instance
				var $nextBtn = $section.find('.jubelio-slider-next');
				var $prevBtn = $section.find('.jubelio-slider-prev');
				var $pagination = $section.find('.jubelio-swiper-pagination');

				var swiperDefaultOptions = {
					slidesPerView: 1.18,
					spaceBetween: 16,
					speed: 450,
					grabCursor: true,
					watchSlidesProgress: true,
					watchOverflow: false,
					observer: true,
					observeParents: true,
					navigation: {
						nextEl: $nextBtn[0],
						prevEl: $prevBtn[0],
						lockClass: 'swiper-button-disabled'
					},
					pagination: {
						el: $pagination[0],
						clickable: true
					},
					breakpoints: {
						640: {
							slidesPerView: 2,
							spaceBetween: 24
						},
						1024: {
							slidesPerView: 3,
							spaceBetween: 28
						}
					}
				};

				var swiperOptions = $.extend(true, swiperDefaultOptions, customConfig);

				// Universally connect both desktop and mobile navigation buttons
				$nextBtn.off('click.jubelioNav').on('click.jubelioNav', function(e) {
					e.preventDefault();
					if (swiperInstance && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
						swiperInstance.slideNext();
					}
				});
				$prevBtn.off('click.jubelioNav').on('click.jubelioNav', function(e) {
					e.preventDefault();
					if (swiperInstance && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
						swiperInstance.slidePrev();
					}
				});

				// Destroy previous instance if re-initializing in Elementor editor
				if ($swiperEl[0].swiper) {
					$swiperEl[0].swiper.destroy(true, true);
				}

				swiperInstance = new Swiper($swiperEl[0], swiperOptions);

				// Sync custom button disabled states
				function syncNavButtons() {
					if (!swiperInstance) return;
					var isBeg = swiperInstance.isBeginning;
					var isEnd = swiperInstance.isEnd;
					$prevBtn.prop('disabled', isBeg);
					$nextBtn.prop('disabled', isEnd);
					if (isBeg) {
						$prevBtn.addClass('swiper-button-disabled');
					} else {
						$prevBtn.removeClass('swiper-button-disabled');
					}
					if (isEnd) {
						$nextBtn.addClass('swiper-button-disabled');
					} else {
						$nextBtn.removeClass('swiper-button-disabled');
					}
				}

				if (swiperInstance) {
					swiperInstance.on('slideChange', syncNavButtons);
					swiperInstance.on('reachBeginning', syncNavButtons);
					swiperInstance.on('reachEnd', syncNavButtons);
					swiperInstance.on('update', syncNavButtons);
					syncNavButtons();
				}
			}

			// 2. Unified Segmented Pill Filter Interaction (Single Active Pill State)
			var $pillBtns = $section.find('.jubelio-filter-pill-btn');
			var $slides = isSlider ? $section.find('.swiper-slide') : $section.find('.jubelio-grid-card');

			$pillBtns.off('click').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var filter = $btn.attr('data-filter');

				// Strictly only ONE active button; all others return to default state
				$pillBtns.removeClass('active').attr('aria-selected', 'false');
				$btn.addClass('active').attr('aria-selected', 'true');

				if (isSlider && swiperInstance) {
					$slides.each(function() {
						var $slide = $(this);
						var ind = $slide.attr('data-industry');

						if (filter === 'all' || ind === filter) {
							$slide.show().removeClass('swiper-slide-hidden');
						} else {
							$slide.hide().addClass('swiper-slide-hidden');
						}
					});

					// Recalculate slider track, dots, and reset to slide 1
					swiperInstance.update();
					swiperInstance.slideTo(0, 300);

					// Refresh navigation buttons disabled state
					$prevBtn.prop('disabled', true).addClass('swiper-button-disabled');
					var visibleCount = $slides.not('.swiper-slide-hidden').length;
					var currentPerView = swiperInstance.params.slidesPerView;
					if (visibleCount <= currentPerView) {
						$nextBtn.prop('disabled', true).addClass('swiper-button-disabled');
					} else {
						$nextBtn.prop('disabled', false).removeClass('swiper-button-disabled');
					}
				} else {
					// Classic Grid Filter
					if (filter === 'all') {
						$slides.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 220);
					} else {
						$slides.each(function() {
							var $card = $(this);
							var ind = $card.attr('data-industry');
							if (ind === filter) {
								$card.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 220);
							} else {
								$card.addClass('hidden');
							}
						});
					}
				}
			});
		});
	}

	// Document Ready
	$(document).ready(function() {
		initCeritaPebisnis();
	});

	// Elementor Live Editor Support
	$(window).on('elementor/frontend/init', function() {
		if (window.elementorFrontend && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis_grid.default', function($scope) {
				initCeritaPebisnis($scope);
			});
		}
	});

})(jQuery);
