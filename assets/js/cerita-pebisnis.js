/**
 * Jubelio - Cerita Pebisnis Interactive Script
 * Handles Slick carousel, category filter tabs, and video lightbox modal.
 */

(function($) {
	'use strict';

	/**
	 * Convert standard video URL to embeddable URL
	 */
	function getEmbedUrl(url) {
		if (!url) return '';

		// YouTube Watch URL
		var ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
		if (ytMatch && ytMatch[1]) {
			return 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&rel=0';
		}

		// Vimeo URL
		var vimeoMatch = url.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)/);
		if (vimeoMatch && vimeoMatch[1]) {
			return 'https://player.vimeo.com/video/' + vimeoMatch[1] + '?autoplay=1';
		}

		return url;
	}

	/**
	 * Initialize Cerita Pebisnis Slider
	 */
	function initSlider($scope) {
		var $slider = $scope ? $scope.find('.jubelio-cp-slider') : $('.jubelio-cp-slider');

		$slider.each(function() {
			var $this = $(this);
			if ($this.hasClass('slick-initialized')) {
				return;
			}

			var rawConfig = $this.attr('data-slick-config');
			var config = {};
			try {
				config = JSON.parse(rawConfig || '{}');
			} catch (e) {
				config = {};
			}

			var slidesToShow = parseInt(config.slidesToShow, 10) || 3;

			$this.slick({
				slidesToShow: slidesToShow,
				slidesToScroll: 1,
				autoplay: config.autoplay !== false,
				autoplaySpeed: parseInt(config.autoplaySpeed, 10) || 5000,
				arrows: config.arrows !== false,
				dots: config.dots !== false,
				infinite: config.infinite !== false,
				pauseOnHover: true,
				adaptiveHeight: true,
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: Math.min(slidesToShow, 2),
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 680,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							arrows: false
						}
					}
				]
			});
		});
	}

	/**
	 * Initialize Filter Tabs
	 */
	function initFilterTabs($scope) {
		var $wrapper = $scope ? $scope.find('.jubelio-cp-wrapper') : $('.jubelio-cp-wrapper');

		$wrapper.each(function() {
			var $this = $(this);
			var $tabs = $this.find('.jubelio-cp-tab-btn');
			var $cards = $this.find('.jubelio-cp-card');

			$tabs.off('click').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var filter = $btn.attr('data-filter');

				$tabs.removeClass('active');
				$btn.addClass('active');

				if (filter === 'all') {
					$cards.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 200);
				} else {
					$cards.each(function() {
						var $card = $(this);
						var cat = $card.attr('data-category');
						if (cat === filter) {
							$card.removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 200);
						} else {
							$card.addClass('hidden');
						}
					});
				}
			});
		});
	}

	/**
	 * Initialize Video Modal Lightbox
	 */
	function initVideoModal($scope) {
		var $wrapper = $scope ? $scope.find('.jubelio-cp-wrapper') : $('.jubelio-cp-wrapper');

		$wrapper.each(function() {
			var $this = $(this);
			var $modal = $this.find('.jubelio-cp-modal');
			var $iframe = $modal.find('iframe');
			var $close = $modal.find('.jubelio-cp-modal-close, .jubelio-cp-modal-backdrop');

			$this.find('.jubelio-cp-btn-video').off('click').on('click', function(e) {
				e.preventDefault();
				var videoUrl = $(this).attr('data-video-url');
				var embedUrl = getEmbedUrl(videoUrl);

				if (embedUrl) {
					$iframe.attr('src', embedUrl);
					$modal.addClass('is-active').attr('aria-hidden', 'false');
					$('body').css('overflow', 'hidden');
				}
			});

			function closeModal() {
				$modal.removeClass('is-active').attr('aria-hidden', 'true');
				$iframe.attr('src', '');
				$('body').css('overflow', '');
			}

			$close.off('click').on('click', function(e) {
				e.preventDefault();
				closeModal();
			});

			$(document).off('keydown.jubelioCpModal').on('keydown.jubelioCpModal', function(e) {
				if (e.key === 'Escape' && $modal.hasClass('is-active')) {
					closeModal();
				}
			});
		});
	}

	/**
	 * Bootstrap everything
	 */
	function initAll($scope) {
		initSlider($scope);
		initFilterTabs($scope);
		initVideoModal($scope);
	}

	$(document).ready(function() {
		initAll();
	});

	// Hook into Elementor frontend for live preview updates
	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/jubelio_cerita_pebisnis.default', function($scope) {
			initAll($scope);
		});
	});

})(jQuery);
