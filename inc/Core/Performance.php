<?php
namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Performance {

	public static function init() {
		add_filter( 'elementor/widget/render_content', array( __CLASS__, 'optimize_elementor_lcp_images' ), 10, 2 );
		add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'optimize_featured_image_attributes' ), 10, 3 );
	}

	public static function optimize_elementor_lcp_images( $content, $widget ) {
		if ( empty( $content ) || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return $content;
		}

		$widget_name  = $widget->get_name();
		$settings     = method_exists( $widget, 'get_settings_for_display' ) ? $widget->get_settings_for_display() : array();
		$is_lcp_image = false;
		$is_wa_button = false;

		if ( 'image' === $widget_name && ! empty( $settings['_css_classes'] ) && false !== strpos( $settings['_css_classes'], 'hero-priority-img' ) ) {
			$is_lcp_image = true;
		}

		if ( 'theme-post-featured-image' === $widget_name ) {
			$is_lcp_image = true;
		}

		if ( 'image' === $widget_name && ! empty( $settings['_element_id'] ) && 'rotating-wa-button' === $settings['_element_id'] ) {
			$is_wa_button = true;
		}

		if ( $is_lcp_image ) {
			$content = preg_replace( '/\sloading=[\'"][^\'"]*[\'"]/i', '', $content );
			$content = preg_replace( '/\sfetchpriority=[\'"][^\'"]*[\'"]/i', '', $content );

			$content = preg_replace( '/<img /i', '<img fetchpriority="high" loading="eager" ', $content, 1 );

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
