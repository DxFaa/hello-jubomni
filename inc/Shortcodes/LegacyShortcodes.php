<?php
namespace Jubelio\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LegacyShortcodes {

	public static function init() {
		add_shortcode( 'dynamic_list', array( __CLASS__, 'dynamic_list' ) );
		add_shortcode( 'fitur_unggulan_list', array( __CLASS__, 'fitur_unggulan_list' ) );
		add_shortcode( 'other_feature', array( __CLASS__, 'other_feature' ) );
		add_shortcode( 'compatible_tools', array( __CLASS__, 'compatible_tools' ) );
		add_shortcode( 'partner_list', array( __CLASS__, 'partner_list' ) );
		add_shortcode( 'partner_list_affiliate', array( __CLASS__, 'partner_list_affiliate' ) );
		add_shortcode( 'about_jubelio_milestone', array( __CLASS__, 'about_jubelio_milestone' ) );
		add_shortcode( 'after_sales_list', array( __CLASS__, 'after_sales_list' ) );
		add_shortcode( 'jubelio_brand_slider', array( __CLASS__, 'jubelio_brand_slider' ) );
		add_shortcode( 'chat_step', array( __CLASS__, 'chat_step' ) );
		add_shortcode( 'jubelio_step_program', array( __CLASS__, 'jubelio_step_program' ) );
		add_shortcode( 'why_box', array( __CLASS__, 'why_box' ) );
		add_shortcode( 'wp_redirect_link', array( __CLASS__, 'wp_redirect_link' ) );
		add_shortcode( 'faq_list', array( __CLASS__, 'faq_list' ) );
		add_shortcode( 'group_partner_list', array( __CLASS__, 'group_partner_list' ) );
		add_shortcode( 'get_pdf', array( __CLASS__, 'get_pdf' ) );
		add_shortcode( 'current_post_id', array( __CLASS__, 'current_post_id' ) );
	}

	public static function dynamic_list( $atts ) {
		global $post;
		if ( ! $post ) {
			return '';
		}

		$fitur = isset( $atts['fitur_no'] ) ? $atts['fitur_no'] : 1;
		$show_jubelio_fitur = function_exists( 'get_field' ) ? get_field( 'show_jubelio_fitur', $post->ID ) : '';

		if ( 'yes' === $show_jubelio_fitur ) {
			ob_start();
			if ( $fitur == 1 ) {
				$fitur_list_jubelio = get_field( 'title_group', $post->ID );
				echo "<ul class='list_item_wjubelio'>";
				if ( is_array( $fitur_list_jubelio ) ) {
					foreach ( $fitur_list_jubelio as $list ) {
						if ( is_array( $list ) || is_object( $list ) ) {
							foreach ( $list as $item ) {
								echo '<li class="item_wjubelio">' . ( isset( $item['list_title'] ) ? esc_html( $item['list_title'] ) : '' ) . '</li>';
							}
						}
					}
				}
				echo '</ul>';
			} else {
				$fitur_list_jubelio = get_field( 'title_group_notjubelio', $post->ID );
				echo "<ul class='list_item_notjubelio'>";
				if ( is_array( $fitur_list_jubelio ) ) {
					foreach ( $fitur_list_jubelio as $list ) {
						if ( is_array( $list ) || is_object( $list ) ) {
							foreach ( $list as $item ) {
								echo '<li class="item_notjubelio">' . ( isset( $item['list_title_notjubelio'] ) ? esc_html( $item['list_title_notjubelio'] ) : '' ) . '</li>';
							}
						}
					}
				}
				echo '</ul>';
			}
			return ob_get_clean();
		}
		return '';
	}

	public static function fitur_unggulan_list() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_fitur_unggulan = get_field( 'show_fitur_unggulan', $post->ID );
		if ( 'yes' === $show_fitur_unggulan ) {
			$fitur_list_unggulan = get_field( 'fitur_unggulan', $post->ID );
			if ( $fitur_list_unggulan ) {
				ob_start();
				echo '<div id="responsiveTabsDemo"><ul>';
				foreach ( $fitur_list_unggulan as $key => $list_fitur ) {
					$ai_badge = '';
					if ( isset( $list_fitur['fitur_ai'] ) && $list_fitur['fitur_ai'] === 'yes' ) {
						$ai_badge = ' <img class="ai-badge" src="https://jubelio.com/wp-content/uploads/2026/07/Stars_RoyalBlue.svg" alt="AI Feature" />';
					}

					echo '<li>
							<a href="#tab-' . esc_attr( $key ) . '">' . esc_html( $list_fitur['title_list_item_fitur_unggulan'] ) . $ai_badge . ' <span>' . esc_html( $list_fitur['description_list_item_fitur_unggulan'] ) . '</span></a>
						  </li>';
				}
				echo '</ul>';

				echo '<div class="tabs-container">';
				foreach ( $fitur_list_unggulan as $key => $list_fitur ) {
					echo '<div id="tab-' . esc_attr( $key ) . '">
							<div class="wrapper-tabs">
								 <div class="content-page-tab">
									 <img src="' . esc_url( $list_fitur['image_list_item_fitur_unggulan'] ) . '" alt="' . esc_attr( $list_fitur['title_list_item_fitur_unggulan'] ) . '">
								 </div>
							</div>
						</div>';
				}
				echo '</div>';
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function other_feature() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_other_features_list = get_field( 'show_other_features_list', $post->ID );
		if ( 'yes' === $show_other_features_list ) {
			$fitur_list_other = get_field( 'feature_list', $post->ID );
			$box_style        = get_field( 'box_style', $post->ID );
			$class            = ( 'normal' === $box_style ) ? 'normal_box' : '';

			if ( $fitur_list_other ) {
				ob_start();
				echo '<div class="other_feature_list ' . esc_attr( $class ) . '">';
				foreach ( $fitur_list_other as $other ) {
					echo '<div class="other_item"><a href="' . esc_url( $other['feature_link'] ) . '">
						<div class="other_image">
							<img src="' . esc_url( $other['icon_feature'] ) . '" alt="Jubelio">
						</div>
						<h4>' . esc_html( $other['title_feature'] ) . '</h4>
						</a>
					</div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function compatible_tools() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_perangkat_pendukung = get_field( 'show_perangkat_pendukung', $post->ID );
		if ( 'yes' === $show_perangkat_pendukung ) {
			$list_perangkat = get_field( 'list_perangkat', $post->ID );
			if ( $list_perangkat ) {
				ob_start();
				echo '<div class="list_perangkat">';
				foreach ( $list_perangkat as $dt ) {
					echo '<div class="list_item">
						<div class="list_item_image">
							<img src="' . esc_url( $dt['image_perangkat_pendukung'] ) . '" alt="Jubelio">
						</div>
						<h4>' . esc_html( $dt['title_list_perangkat'] ) . '</h4>
					</div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function partner_list() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_jubelio_partner_list_ = get_field( 'show_jubelio_partner_list_', $post->ID );
		if ( 'yes' === $show_jubelio_partner_list_ ) {
			$list_partner = get_field( 'partner_list', $post->ID );
			if ( $list_partner ) {
				$layout_style = get_field( 'layout_setting', $post->ID );
				$class        = ( 'One Row' === $layout_style ) ? 'one_row' : 'dynamic';

				ob_start();
				echo '<div class="partner_list ' . esc_attr( $class ) . '">';
				foreach ( $list_partner as $partner ) {
					echo '<div class="partner_item">
							<img src="' . esc_url( $partner['partner_logo'] ) . '" alt="Jubelio">
						 </div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function partner_list_affiliate() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_partner_list_affiliate = get_field( 'show_partner_list_affiliate', $post->ID );
		if ( 'yes' === $show_partner_list_affiliate ) {
			$affiliate_partner_list = get_field( 'affiliate_partner_list', $post->ID );
			if ( $affiliate_partner_list ) {
				ob_start();
				echo '<div class="partner_list dynamic affiliate_list">';
				foreach ( $affiliate_partner_list as $partner ) {
					echo '<div class="partner_item">
							<img src="' . esc_url( $partner['image_affiliate'] ) . '" alt="Jubelio">
						 </div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function about_jubelio_milestone() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$about_jubelio_milestone = get_field( 'milestones_jubelio', $post->ID );
		if ( $about_jubelio_milestone ) {
			ob_start();
			echo '<div class="box_wrap_list flex_box">';
			foreach ( $about_jubelio_milestone as $milestone ) {
				echo '<div class="box_list">
						<div class="box_heading" style="background-color: ' . esc_attr( $milestone['box_color'] ) . ' ">
							<h3>' . esc_html( $milestone['heading_milestone'] ) . '</h3>
						</div>
						<div class="box_text">' . wp_kses_post( $milestone['milestone_text'] ) . '</div>
				</div>';
			}
			echo '</div>';
			return ob_get_clean();
		}
		return '';
	}

	public static function after_sales_list() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$after_sales_list = get_field( 'after_sales_list', $post->ID );
		if ( $after_sales_list ) {
			ob_start();
			echo '<div class="sales_step">';
			$j = 0;
			foreach ( $after_sales_list as $sales_step ) {
				$reverse = ( $j % 2 != 0 ) ? ' reverse_col' : '';
				echo '<div class="sales_step_list' . esc_attr( $reverse ) . '">
						<div class="sales_col_left">
							<h4>' . esc_html( $sales_step['title_after_sales'] ) . '</h4>
							<div class="sales_desc">
								' . wp_kses_post( $sales_step['description_after_sales'] ) . '
							</div>
						</div>
						<div class="sales_col_right" style="background-color:' . esc_attr( $sales_step['background_color_for_image'] ) . '">
							<img src="' . esc_url( $sales_step['image_after_sales'] ) . '" alt="Jubelio">
						</div>
					 </div>';
				$j++;
			}
			echo '</div>';
			return ob_get_clean();
		}
		return '';
	}

	public static function jubelio_brand_slider() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$jubelio_brand_slider = get_field( 'jubelio_brand', $post->ID );
		if ( $jubelio_brand_slider ) {
			ob_start();
			echo '<div class="box_wrap_brand">';
			foreach ( $jubelio_brand_slider as $brand_slider ) {
				echo '<div class="box_brand_slider">
						<img src="' . esc_url( $brand_slider['brand_logo_'] ) . '">
				</div>';
			}
			echo '</div>';
			return ob_get_clean();
		}
		return '';
	}

	public static function chat_step() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_jubelio_chat_step_      = get_field( 'show_jubelio_chat_step_', $post->ID );
		$show_jubelio_other_products_ = get_field( 'show_jubelio_other_products_', $post->ID );

		if ( 'yes' === $show_jubelio_chat_step_ || 'yes' === $show_jubelio_other_products_ ) {
			$jubelio_chat_step   = get_field( 'jubelio_chat_step', $post->ID );
			$layout_info_box_col = get_field( 'layout_info_box_col', $post->ID );
			$use_link            = get_field( 'use_link', $post->ID );
			$class_box           = ( 'columns' === $layout_info_box_col ) ? 'three_column' : '';

			if ( $jubelio_chat_step ) {
				ob_start();
				echo '<div class="other_feature_list chat_step ' . esc_attr( $class_box ) . '">';
				foreach ( $jubelio_chat_step as $step ) {
					echo '<div class="other_item">';
					if ( 'yes' === $use_link ) {
						echo '<a href="' . esc_url( $step['link_info_box'] ) . '">';
					}
					echo '<div class="other_image">
							<img src="' . esc_url( $step['image_step_list'] ) . '" alt="Jubelio">
						</div>
						<div class="content_wrapper">
						<h4>' . esc_html( $step['title_step_list'] ) . '</h4>'
						. wp_kses_post( $step['description_step_list'] ) .
						'</div>';
					if ( 'yes' === $use_link ) {
						echo '</a>';
					}
					echo '</div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function jubelio_step_program() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_jubelio_step_program = get_field( 'show_jubelio_step_program_', $post->ID );
		if ( 'yes' === $show_jubelio_step_program ) {
			$list_info_program = get_field( 'list_info_program', $post->ID );
			if ( $list_info_program ) {
				ob_start();
				echo '<div class="info_box_program">';
				foreach ( $list_info_program as $program ) {
					echo '<div class="info_program_list">
						<div class="info_program_image">
							<img src="' . esc_url( $program['image_info_program'] ) . '" alt="' . esc_attr( $program['title_info_program'] ) . '">
						</div>
						<div class="info_program_content">
							<h4>' . esc_html( $program['title_info_program'] ) . '</h4>
							' . wp_kses_post( $program['description_info_program_list'] ) . '
						</div>
					</div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function why_box() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_why_box_ = get_field( 'show_why_box_', $post->ID );
		if ( 'yes' === $show_why_box_ ) {
			$item_list_box_ = get_field( 'item_list_box_', $post->ID );
			if ( $item_list_box_ ) {
				ob_start();
				echo '<div class="why_box_list"><ul>';
				foreach ( $item_list_box_ as $list_box ) {
					echo '<li class="box_item">
					<h4>' . esc_html( $list_box['item_title_why_box'] ) . '</h4>
					<p>' . esc_html( $list_box['item_description_why_box'] ) . '</p>
					</li>';
				}
				echo '</ul></div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function wp_redirect_link() {
		if ( is_user_logged_in() ) {
			ob_start();
			echo "<p class='notifications'>
			        Anda sudah login. Silahkan kembali ke halaman Affiliate Area
			      </p>
			      <div class='button_wrapper'>
			        <a href='https://jubelio.com/affiliate-area/'>Affiliate Dashboard</a>
			        <a href='" . esc_url( wp_logout_url( get_permalink() ) ) . "'>Logout</a>
			      </div>";
			return ob_get_clean();
		}
		return '';
	}

	public static function faq_list() {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$show_faq_ = get_field( 'show_faq_', $post->ID );
		if ( 'yes' === $show_faq_ ) {
			$faq_list = get_field( 'faq_list', $post->ID );
			if ( $faq_list ) {
				ob_start();
				echo '<div id="accordion">';
				foreach ( $faq_list as $faq ) {
					echo '<h3><span class="img_acc"><img src="https://jubelio.com/wp-content/uploads/2023/12/question-icon-1.svg"></span><span class="title_acc">' . esc_html( $faq['title_faq'] ) . '</span></h3>
						<div>
							<p>' . wp_kses_post( $faq['description_faq'] ) . '</p>
						</div>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		}
		return '';
	}

	public static function group_partner_list( $atts ) {
		global $post;
		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$partner           = isset( $atts['partner'] ) ? $atts['partner'] : '';
		$partner_link_page = get_field( 'activated_partner_link', $post->ID );

		ob_start();

		if ( 'fulfillment' === $partner ) {
			if ( 'yes' === get_field( 'show_fulfillment_partner_', $post->ID ) ) {
				$list = get_field( 'fulfillment_partner_list', $post->ID );
				if ( $list ) {
					echo '<div class="partner_list dynamic">';
					foreach ( $list as $item ) {
						echo '<div class="partner_item">';
						if ( 'yes' === $partner_link_page ) {
							echo '<a href="' . esc_url( $item['fulfillment_partner_link'] ) . '" target="_blank" rel="noopener noreferrer">';
						}
						echo '<img src="' . esc_url( $item['fulfillment_partner_logo'] ) . '" alt="Jubelio">';
						if ( 'yes' === $partner_link_page ) {
							echo '</a>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
			}
		}

		if ( 'jubelio_shipment' === $partner ) {
			if ( 'yes' === get_field( 'show_jubelio_shipment_partner_', $post->ID ) ) {
				$list = get_field( 'jubelio_shipment_partner_list', $post->ID );
				if ( $list ) {
					echo '<div class="partner_list dynamic">';
					foreach ( $list as $item ) {
						echo '<div class="partner_item">';
						if ( 'yes' === $partner_link_page ) {
							echo '<a href="' . esc_url( $item['jubelio_shipment_partner_link'] ) . '" target="_blank" rel="noopener noreferrer">';
						}
						echo '<img src="' . esc_url( $item['jubelio_shipment_partner_logo'] ) . '" alt="Jubelio">';
						if ( 'yes' === $partner_link_page ) {
							echo '</a>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
			}
		}

		if ( 'financing' === $partner ) {
			if ( 'yes' === get_field( 'show_financing_partner_', $post->ID ) ) {
				$list = get_field( 'financing_partner_list', $post->ID );
				if ( $list ) {
					echo '<div class="partner_list dynamic">';
					foreach ( $list as $item ) {
						echo '<div class="partner_item">';
						if ( 'yes' === $partner_link_page ) {
							echo '<a href="' . esc_url( $item['financing_partner_logo_link'] ) . '" target="_blank" rel="noopener noreferrer">';
						}
						echo '<img src="' . esc_url( $item['financing_partner_logo'] ) . '" alt="Jubelio">';
						if ( 'yes' === $partner_link_page ) {
							echo '</a>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
			}
		}

		if ( 'enabler' === $partner ) {
			if ( 'yes' === get_field( 'show_enabler_partner__', $post->ID ) ) {
				$list = get_field( 'enabler_partner_list', $post->ID );
				if ( $list ) {
					echo '<div class="partner_list dynamic">';
					foreach ( $list as $item ) {
						echo '<div class="partner_item">';
						if ( 'yes' === $partner_link_page ) {
							echo '<a href="' . esc_url( $item['enabler_partner_logo_link'] ) . '" target="_blank" rel="noopener noreferrer">';
						}
						echo '<img src="' . esc_url( $item['enabler_partner_logo'] ) . '" alt="Jubelio">';
						if ( 'yes' === $partner_link_page ) {
							echo '</a>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
			}
		}

		if ( 'erp' === $partner ) {
			if ( 'yes' === get_field( 'show_sistem_erp_partner_', $post->ID ) ) {
				$list = get_field( 'sistem_erp_partner_list', $post->ID );
				if ( $list ) {
					echo '<div class="partner_list dynamic">';
					foreach ( $list as $item ) {
						echo '<div class="partner_item">';
						if ( 'yes' === $partner_link_page ) {
							echo '<a href="' . esc_url( $item['sistem_erp_partner_logo_link'] ) . '" target="_blank" rel="noopener noreferrer">';
						}
						echo '<img src="' . esc_url( $item['sistem_erp_partner_logo'] ) . '" alt="Jubelio">';
						if ( 'yes' === $partner_link_page ) {
							echo '</a>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
			}
		}

		return ob_get_clean();
	}

	public static function get_pdf() {
		if ( isset( $_GET['pdf_id'] ) ) {
			$pdfid         = absint( $_GET['pdf_id'] );
			$pdf_file      = function_exists( 'get_field' ) ? get_field( 'e-book_file', $pdfid ) : '';
			$post_title    = get_the_title( $pdfid );
			$thumbnail_url = get_the_post_thumbnail_url( $pdfid, 'full' );

			ob_start();
			echo '<div class="wrap_pdf">';
			if ( $thumbnail_url ) {
				echo '<div class="pdf_image"><img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr( $post_title ) . '" style="max-width: 100%; height: auto;"></div>';
			}
			if ( $post_title ) {
				echo '<div class="pdf_title"><h3>' . esc_html( $post_title ) . '</h3></div>';
			}
			if ( $pdf_file ) {
				echo '<div class="pdf_button"><a href="' . esc_url( $pdf_file ) . '" target="_blank" rel="noopener noreferrer">Download</a></div>';
			} else {
				echo '<div class="pdf_button"><p>PDF Tidak ditemukan.</p></div>';
			}
			echo '</div>';
			echo '<script type="text/javascript">
					document.addEventListener("DOMContentLoaded", function() {
						var links = document.querySelectorAll("a[href*=\"&amp;\"]");
						links.forEach(function(link) {
							link.href = link.href.replace(/&amp;/g, "&");
						});
					});
				  </script>';
			return ob_get_clean();
		}
		return 'PDF Tidak ditemukan';
	}

	public static function current_post_id() {
		$post_ids = get_the_ID();
		$link     = 'https://jubelio.com/terima-kasih-e-book/?pdf_id=' . $post_ids;
		return urldecode( $link );
	}

	public static function strip_unused_shortcodes( $content ) {
		$pattern = self::get_unused_shortcode_regex();
		return preg_replace_callback( '/' . $pattern . '/s', 'strip_shortcode_tag', $content );
	}

	protected static function get_unused_shortcode_regex() {
		global $shortcode_tags;
		$tagnames  = array_keys( $shortcode_tags );
		$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );
		$regex     = '\[(\[?)';
		$regex    .= "(?!$tagregexp)";
		$regex    .= '\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		return $regex;
	}
}
