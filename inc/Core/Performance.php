<?php
/**
 * Core Web Vitals & Image Performance Optimizations
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Performance {

	/**
	 * Register performance hooks
	 */
	public static function init() {
		// Elementor LCP and Priority Image Optimizer
		add_filter( 'elementor/widget/render_content', array( __CLASS__, 'optimize_elementor_lcp_images' ), 10, 2 );

		// WordPress Standard Featured Image Optimizer (Single Posts / Pages)
		add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'optimize_featured_image_attributes' ), 10, 3 );
	}

	/**
	 * Optimize Elementor LCP images (hero images, post featured images, and rotating CTA buttons)
	 *
	 * @param string $content Rendered widget HTML content.
	 * @param object $widget Elementor widget instance.
	 * @return string Modified content with high fetchpriority and eager loading.
	 */
	public static function optimize_elementor_lcp_images( $content, $widget ) {
		if ( empty( $content ) || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return $content;
		}

		$widget_name  = $widget->get_name();
		$settings     = method_exists( $widget, 'get_settings_for_display' ) ? $widget->get_settings_for_display() : array();
		$is_lcp_image = false;
		$is_wa_button = false;

		// Check for hero priority image class
		if ( 'image' === $widget_name && ! empty( $settings['_css_classes'] ) && false !== strpos( $settings['_css_classes'], 'hero-priority-img' ) ) {
			$is_lcp_image = true;
		}

		// Check for theme post featured image widget
		if ( 'theme-post-featured-image' === $widget_name ) {
			$is_lcp_image = true;
		}

		// Check for rotating WhatsApp floating CTA button
		if ( 'image' === $widget_name && ! empty( $settings['_element_id'] ) && 'rotating-wa-button' === $settings['_element_id'] ) {
			$is_wa_button = true;
		}

		if ( $is_lcp_image ) {
			// Strip lazy loading attributes
			$content = preg_replace( '/\sloading=[\'"][^\'"]*[\'"]/i', '', $content );
			$content = preg_replace( '/\sfetchpriority=[\'"][^\'"]*[\'"]/i', '', $content );

			// Inject high priority and eager loading
			$content = preg_replace( '/<img /i', '<img fetchpriority="high" loading="eager" ', $content, 1 );

			// Append skip-lazy classes if class attribute exists, otherwise add class attribute
			if ( preg_match( '/<img\s[^>]*class=[\'"]([^\'"]*)[\'"]/i', $content, $matches ) ) {
				$existing_classes = $matches[1];
				if ( false === strpos( $existing_classes, 'skip-lazy' ) ) {
					$new_classes = trim( $existing_classes . ' no-lazyload skip-lazy' );
					$content     = preg_replace( '/class=[\'"][^\'"]*[\'"]/i', 'class="' . esc_attr( $new_classes ) . '"', $content, 1 );
				}
			} else {
				$content = preg_replace( '/<img /i', '<img class="no-lazyload skip-lazy" ', $content, 1 );
			}
		}

		if ( $is_wa_button ) {
			$content = preg_replace( '/\sloading=[\'"][^\'"]*[\'"]/i', '', $content );
			$content = preg_replace( '/<img /i', '<img loading="eager" ', $content, 1 );

			if ( preg_match( '/<img\s[^>]*class=[\'"]([^\'"]*)[\'"]/i', $content, $matches ) ) {
				$existing_classes = $matches[1];
				if ( false === strpos( $existing_classes, 'skip-lazy' ) ) {
					$new_classes = trim( $existing_classes . ' no-lazyload skip-lazy' );
					$content     = preg_replace( '/class=[\'"][^\'"]*[\'"]/i', 'class="' . esc_attr( $new_classes ) . '"', $content, 1 );
				}
			} else {
				$content = preg_replace( '/<img /i', '<img class="no-lazyload skip-lazy" ', $content, 1 );
			}
		}

		return $content;
	}

	/**
	 * Optimize standard WordPress featured images on singular pages
	 *
	 * @param array        $attr Attributes for the image markup.
	 * @param \WP_Post     $attachment Image attachment post.
	 * @param string|array $size Requested image size.
	 * @return array Modified attributes.
	 */
	public static function optimize_featured_image_attributes( $attr, $attachment, $size ) {
		if ( is_singular() && is_object( $attachment ) && get_post_thumbnail_id() == $attachment->ID ) {
			$attr['fetchpriority'] = 'high';
			unset( $attr['loading'] );

			$existing_class = isset( $attr['class'] ) ? $attr['class'] : '';
			if ( false === strpos( $existing_class, 'skip-lazy' ) ) {
				$attr['class'] = trim( $existing_class . ' no-lazyload skip-lazy' );
			}
		}

		return $attr;
	}
}
