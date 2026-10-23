/**
 * Jubelio - Cerita Pebisnis Interactive Script
 * Handles Unified Segmented Pill Filtering, Swiper Touch Slider & Dynamic Bullets Pagination
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

			// Cached DOM slide elements for fast, non-destructive filtering
			var $swiperWrapper = isSlider ? $swiperEl.find('.swiper-wrapper') : null;
			var allSlidesArray = [];
			if (isSlider && $swiperWrapper && $swiperWrapper.length) {
				$swiperWrapper.children('.swiper-slide').each(function() {
					allSlidesArray.push(this);
				});
			}

			// 1. Initialize Swiper Touch Slider with Dynamic Bullets Pagination
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

				var isDynamicBullets = (customConfig.dynamicPagination !== false);

				var swiperDefaultOptions = {
					slidesPerView: 1.18,
					slidesPerGroup: 1,
					slidesPerGroupSkip: 0,
					spaceBetween: 16,
					speed: 450,
					grabCursor: true,
					watchSlidesProgress: true,
					watchOverflow: true,
					observer: true,
					observeParents: true,
					navigation: false, // Managed customly below to prevent duplicate event listener firing
					pagination: {
						el: $pagination[0],
						clickable: true,
						dynamicBullets: isDynamicBullets,
						dynamicMainBullets: 1
					},
					breakpoints: {
						640: {
							slidesPerView: 2,
							slidesPerGroup: 1,
							spaceBetween: 24
						},
						1024: {
							slidesPerView: 3,
							slidesPerGroup: 1,
							spaceBetween: 28
						}
					}
				};

				var swiperOptions = $.extend(true, swiperDefaultOptions, customConfig);

				// Universally connect both desktop and mobile navigation buttons
				// Guard with !swiperInstance.animating to ensure exactly ONE card slides per click
				$nextBtn.off('click.jubelioNav').on('click.jubelioNav', function(e) {
					e.preventDefault();
					if (swiperInstance && !swiperInstance.animating && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
						swiperInstance.slideNext();
					}
				});
				$prevBtn.off('click.jubelioNav').on('click.jubelioNav', function(e) {
					e.preventDefault();
					if (swiperInstance && !swiperInstance.animating && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
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
					var visibleCount = $swiperWrapper.children('.swiper-slide').length;
					var currentPerView = swiperInstance.params.slidesPerView;
					if (typeof currentPerView === 'number' && visibleCount <= currentPerView) {
						isBeg = true;
						isEnd = true;
					}
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

				if (isSlider && swiperInstance && allSlidesArray.length) {
					// Clean non-destructive slide filtering for Swiper
					$swiperWrapper.empty();
					for (var i = 0; i < allSlidesArray.length; i++) {
						var slideEl = allSlidesArray[i];
						var ind = slideEl.getAttribute('data-industry');
						if (filter === 'all' || ind === filter) {
							$swiperWrapper.append(slideEl);
						}
					}

					// Recalculate slider track, dynamic bullets pagination, and reset to slide 1
					swiperInstance.update();
					swiperInstance.slideTo(0, 300);
					syncNavButtons();
				} else if (!isSlider) {
					// Classic Grid Filter with Load More synchronization
					var $gridContainer = $section.find('.jubelio-grid-cards-container');
					var isLoadMore = $gridContainer.attr('data-load-more') === 'yes';
					var initialLimit = parseInt($gridContainer.attr('data-initial'), 10) || 6;
					var $loadMoreWrap = $section.find('.jubelio-grid-load-more-wrap');
					var matchCount = 0;

					$slides.each(function() {
						var $card = $(this);
						var ind = $card.attr('data-industry');
						var matches = (filter === 'all' || ind === filter);

						if (matches) {
							matchCount++;
							if (isLoadMore && matchCount > initialLimit) {
								$card.addClass('jubelio-card-load-more-hidden').removeClass('hidden');
							} else {
								$card.removeClass('hidden jubelio-card-load-more-hidden')
									.css('opacity', '0').animate({ opacity: 1 }, 220);
							}
						} else {
							$card.addClass('hidden');
						}
					});

					// Update Load More button visibility for classic grid
					if (isLoadMore) {
						if (matchCount > initialLimit) {
							$loadMoreWrap.fadeIn(200);
						} else {
							$loadMoreWrap.fadeOut(200);
						}
					}
				}
			});

			// 3. Load More Click Event (Classic Grid Only)
			var $loadMoreBtn = $section.find('.jubelio-grid-load-more-btn');

			$loadMoreBtn.off('click').on('click', function(e) {
				e.preventDefault();
				var activeFilter = $pillBtns.filter('.active').attr('data-filter') || 'all';
				var step = parseInt($(this).attr('data-step'), 10) || 6;
				var $gridContainer = $section.find('.jubelio-grid-cards-container');

				var $hiddenCards = $gridContainer.find('.jubelio-grid-card.jubelio-card-load-more-hidden').filter(function() {
					if (activeFilter === 'all') return true;
					return $(this).attr('data-industry') === activeFilter;
				});

				var $toRevealCards = $hiddenCards.slice(0, step);

				$toRevealCards.removeClass('jubelio-card-load-more-hidden')
					.css({ opacity: 0, transform: 'translateY(14px)' })
					.animate({ opacity: 1 }, {
						duration: 320,
						step: function(now, fx) {
							if (fx.prop === 'opacity') {
								var trans = 14 * (1 - now);
								$(this).css('transform', 'translateY(' + trans + 'px)');
							}
						},
						complete: function() {
							$(this).css('transform', '');
						}
					});

				var remainingCards = $gridContainer.find('.jubelio-grid-card.jubelio-card-load-more-hidden').filter(function() {
					if (activeFilter === 'all') return true;
					return $(this).attr('data-industry') === activeFilter;
				}).length;

				if (remainingCards === 0) {
					$section.find('.jubelio-grid-load-more-wrap').fadeOut(220);
				}
			});
		});
	}

	/**
	 * Initialize Metrics Cards Interactive Count-Up Animation
	 */
	function initMetricsSection($scope) {
		var $sections = $scope ? $scope.find('.jubelio-metrics-section') : $('.jubelio-metrics-section');

		$sections.each(function() {
			var sectionEl = this;
			var $section = $(this);
			var isAnimated = $section.attr('data-animated') !== 'no';
			if (!isAnimated) return;

			var $values = $section.find('.jubelio-metric-value, .jubelio-metric-number-val');

			function parseMetric(str) {
				if (!str) return null;
				str = str.trim();
				var match = str.match(/^([^\d]*)([\d.,]+)(.*)$/);
				if (!match) return null;

				var prefix = match[1] || '';
				var numStr = match[2] || '';
				var suffix = match[3] || '';

				var isThousands = (numStr.indexOf('.') !== -1 && numStr.indexOf(',') === -1 && numStr.split('.')[1].length === 3);
				var isDecimalComma = (numStr.indexOf(',') !== -1);
				var isDecimalDot = (!isThousands && numStr.indexOf('.') !== -1);

				var decimals = 0;
				var cleanNum = 0;

				if (isThousands) {
					cleanNum = parseFloat(numStr.replace(/\./g, ''));
				} else if (isDecimalComma) {
					var commaParts = numStr.split(',');
					decimals = commaParts[1] ? commaParts[1].length : 0;
					cleanNum = parseFloat(numStr.replace(',', '.'));
				} else if (isDecimalDot) {
					var dotParts = numStr.split('.');
					decimals = dotParts[1] ? dotParts[1].length : 0;
					cleanNum = parseFloat(numStr);
				} else {
					cleanNum = parseFloat(numStr);
				}

				if (isNaN(cleanNum)) return null;

				return {
					prefix: prefix,
					suffix: suffix,
					target: cleanNum,
					decimals: decimals,
					isThousands: isThousands,
					isDecimalComma: isDecimalComma
				};
			}

			function animateElement(el, parsed, duration) {
				var startTime = null;

				function step(timestamp) {
					if (!startTime) startTime = timestamp;
					var progress = Math.min((timestamp - startTime) / duration, 1);
					var easeProgress = 1 - Math.pow(1 - progress, 3);
					var currentVal = easeProgress * parsed.target;

					var formattedNum = '';
					if (parsed.isThousands) {
						formattedNum = Math.floor(currentVal).toLocaleString('id-ID');
					} else if (parsed.decimals > 0) {
						var fixed = currentVal.toFixed(parsed.decimals);
						formattedNum = parsed.isDecimalComma ? fixed.replace('.', ',') : fixed;
					} else {
						formattedNum = Math.floor(currentVal).toString();
					}

					el.textContent = parsed.prefix + formattedNum + parsed.suffix;

					if (progress < 1) {
						window.requestAnimationFrame(step);
					} else {
						el.textContent = el.getAttribute('data-raw') || (parsed.prefix + formattedNum + parsed.suffix);
					}
				}

				window.requestAnimationFrame(step);
			}

			function triggerAnimation() {
				$values.each(function() {
					var valEl = this;
					var raw = valEl.getAttribute('data-raw') || valEl.textContent;
					var parsed = parseMetric(raw);
					if (parsed) {
						animateElement(valEl, parsed, 1600);
					}
				});
			}

			// Trigger on Viewport Entry with IntersectionObserver
			if ('IntersectionObserver' in window) {
				var observer = new IntersectionObserver(function(entries) {
					entries.forEach(function(entry) {
						if (entry.isIntersecting) {
							triggerAnimation();
							observer.unobserve(entry.target);
						}
					});
				}, {
					threshold: 0.15
				});

				observer.observe(sectionEl);
			} else {
				triggerAnimation();
			}
		});
	}

	/**
	 * Initialize Cerita Pebisnis Testimonials Centered Slider & Dynamic Bullets
	 */
	function initTestimonialsSection($scope) {
		var $sections = $scope ? $scope.find('.jubelio-testimonials-section') : $('.jubelio-testimonials-section');

		$sections.each(function() {
			var $section = $(this);
			var $swiperEl = $section.find('.jubelio-testimonials-swiper');
			if (!$swiperEl.length || typeof Swiper === 'undefined') return;

			var configAttr = $swiperEl.attr('data-swiper-config');
			var customConfig = {};
			try {
				if (configAttr) {
					customConfig = JSON.parse(configAttr);
				}
			} catch (err) {
				customConfig = {};
			}

			var $nextBtn = $section.find('.jubelio-slider-next');
			var $prevBtn = $section.find('.jubelio-slider-prev');
			var $pagination = $section.find('.jubelio-swiper-pagination');
			var isDynamicBullets = (customConfig.dynamicPagination !== false);
			var slideCount = $swiperEl.find('.swiper-slide').length;

			// Loop is supported when there are at least 3 slides
			var canLoop = (slideCount >= 3);

			var swiperOptions = {
				centeredSlides: true,
				loop: canLoop,
				slidesPerView: 1.15,
				spaceBetween: 16,
				speed: 500,
				grabCursor: true,
				watchSlidesProgress: true,
				watchOverflow: true,
				observer: true,
				observeParents: true,
				navigation: false, // Managed customly below with !swiperInstance.animating guard
				pagination: {
					el: $pagination[0],
					clickable: true,
					dynamicBullets: isDynamicBullets,
					dynamicMainBullets: 1
				},
				breakpoints: {
					640: {
						slidesPerView: 1.35,
						spaceBetween: 24,
						centeredSlides: true
					},
					1024: {
						slidesPerView: 1.62,
						spaceBetween: 28,
						centeredSlides: true
					},
					1440: {
						slidesPerView: 1.75,
						spaceBetween: 32,
						centeredSlides: true
					}
				}
			};

			if (customConfig.autoplay) {
				swiperOptions.autoplay = {
					delay: customConfig.autoplaySpeed || 4500,
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				};
			}

			// Destroy previous instance in Elementor editor
			if ($swiperEl[0].swiper) {
				$swiperEl[0].swiper.destroy(true, true);
			}

			var swiperInstance = new Swiper($swiperEl[0], swiperOptions);

			// Synchronize button disabled states when loop is disabled
			function syncNavButtons() {
				if (!swiperInstance || canLoop) return;
				var isBeg = swiperInstance.isBeginning;
				var isEnd = swiperInstance.isEnd;
				$prevBtn.prop('disabled', isBeg).toggleClass('swiper-button-disabled', isBeg);
				$nextBtn.prop('disabled', isEnd).toggleClass('swiper-button-disabled', isEnd);
			}

			// Exactly ONE card slides per click
			$nextBtn.off('click.jubelioTestimonialNav').on('click.jubelioTestimonialNav', function(e) {
				e.preventDefault();
				if (swiperInstance && !swiperInstance.animating && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
					swiperInstance.slideNext();
				}
			});
			$prevBtn.off('click.jubelioTestimonialNav').on('click.jubelioTestimonialNav', function(e) {
				e.preventDefault();
				if (swiperInstance && !swiperInstance.animating && !$(this).prop('disabled') && !$(this).hasClass('swiper-button-disabled')) {
					swiperInstance.slidePrev();
				}
			});

			if (swiperInstance) {
				swiperInstance.on('slideChange', syncNavButtons);
				swiperInstance.on('update', syncNavButtons);
				syncNavButtons();
			}
		});
	}

	// Document Ready
	$(document).ready(function() {
		initCeritaPebisnis();
		initMetricsSection();
		initTestimonialsSection();
	});

	// Elementor Live Editor Support
	$(window).on('elementor/frontend/init', function() {
		if (window.elementorFrontend && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis_grid.default', function($scope) {
				initCeritaPebisnis($scope);
			});
			elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis_metrics.default', function($scope) {
				initMetricsSection($scope);
			});
			elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis_testimonials.default', function($scope) {
				initTestimonialsSection($scope);
			});
		}
	});

})(jQuery);
