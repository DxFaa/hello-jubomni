<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
 
if (! defined('_S_VERSION') ) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'null'
	);
	
	 wp_enqueue_style(
        'jubelio-slider', 
        '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
        array(), _S_VERSION, 'all'
    );
	
	 wp_enqueue_style(
        'jubelio-responsive-tabs', 
        '//cdnjs.cloudflare.com/ajax/libs/responsive-tabs/1.6.3/css/responsive-tabs.css',
        array(), _S_VERSION, 'all'
    );
	
	wp_enqueue_style(
        'jubelio-accordion', 
        '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css',
        array(), _S_VERSION, 'all'
    );
	
	 wp_enqueue_script(
        'jubelio-slider', 
        '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', 
        array(), _S_VERSION, true
   	 );
	
	wp_enqueue_script(
        'jubelio-responsive-tabs', 
        '//cdn.jsdelivr.net/jquery.responsive-tabs/latest/jquery.responsiveTabs.min.js', 
        array(), _S_VERSION, true
    );
	
	wp_enqueue_script(
        'jubelio-accordion', 
        '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', 
        array(), _S_VERSION, true
    );
	
	 wp_enqueue_script(
        'jubelio-custom-script', 
        get_stylesheet_directory_uri() . '/assets/js/custom.js', 
        array(), _S_VERSION, true
    );
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

add_shortcode('dynamic_list', 'dynamic_list');
function dynamic_list( $atts )
{
	global $post;
	$fitur = $atts['fitur_no'];
	
	$show_jubelio_fitur = get_field( 'show_jubelio_fitur', $post->ID );
	if( 'yes' === $show_jubelio_fitur ){
		if( $fitur == 1  ){
			$fitur_list_jubelio   = get_field( 'title_group', $post->ID );
			echo "<ul class='list_item_wjubelio'>";
			foreach( $fitur_list_jubelio as $list ) {
				if (is_array($list) || is_object($list))
				{
					foreach ( $list as $item ) {
						echo '<li class="item_wjubelio">' . $item['list_title'] . '</li>';
					}
				}
			}
			echo "</ul>";
		}else{
			$fitur_list_jubelio = get_field( 'title_group_notjubelio', $post->ID );
			echo "<ul class='list_item_notjubelio'>";
			foreach( $fitur_list_jubelio as $list ) {
				if (is_array($list) || is_object($list))
				{
					foreach ( $list as $item ) {
						echo '<li class="item_notjubelio">' . $item['list_title_notjubelio'] . '</li>';
					}
				}
			}
			echo "</ul>";
		}
		
	}
}

add_shortcode('fitur_unggulan_list', 'fitur_unggulan_list');
function fitur_unggulan_list() {
    global $post;
    
    $show_fitur_unggulan = get_field('show_fitur_unggulan', $post->ID);
    
    if ('yes' === $show_fitur_unggulan) {
        $fitur_list_unggulan = get_field('fitur_unggulan', $post->ID);
        
        if ($fitur_list_unggulan) {
            ob_start(); 
            
            echo '<div id="responsiveTabsDemo"><ul>';
            foreach ($fitur_list_unggulan as $key => $list_fitur) {
                
                $ai_badge = '';
                if (isset($list_fitur['fitur_ai']) && $list_fitur['fitur_ai'] === 'yes') {
                    $ai_badge = ' <img class="ai-badge" src="https://jubelio.com/wp-content/uploads/2026/07/Stars_RoyalBlue.svg" alt="AI Feature" />';
                }

                echo '<li>
                        <a href="#tab-'. $key .'">'. $list_fitur['title_list_item_fitur_unggulan'] . $ai_badge .' <span>'. $list_fitur['description_list_item_fitur_unggulan'] .'</span></a>
                      </li>';
            }
            echo '</ul>';
            
            echo '<div class="tabs-container">';
            foreach ($fitur_list_unggulan as $key => $list_fitur) {
                echo '<div id="tab-'. $key .'">
                        <div class="wrapper-tabs">
                             <div class="content-page-tab">
                                 <img src="'. $list_fitur['image_list_item_fitur_unggulan'] .'" alt="'. $list_fitur['title_list_item_fitur_unggulan'] .'">
                             </div>
                        </div>
                    </div>';
            }
            echo '</div>';
            echo '</div>';
            
            return ob_get_clean();
        }
    }
}

add_shortcode('other_feature', 'other_feature');
function other_feature()
{
	global $post;
	$show_other_features_list = get_field( 'show_other_features_list', $post->ID );
	if( 'yes' === $show_other_features_list ){
		$fitur_list_other   = get_field( 'feature_list', $post->ID );
		$box_style          = get_field( 'box_style', $post->ID );
		
		if( 'normal' === $box_style)
		{
			$class = "normal_box";
		}
		else{
			$class = "";
		}
		
		if( $fitur_list_other ){
			echo '<div class="other_feature_list '. $class .'">';
			foreach( $fitur_list_other as $other ){
				echo '<div class="other_item"><a href="'.$other['feature_link'].'">
					<div class="other_image">
						<img src="'. $other['icon_feature'] .'" alt="Jubelio">
					</div>
					<h4>'. $other['title_feature'] .'</h4>
					</a>
				</div>';
			}
			echo '</div>';
		}
	}
}

add_shortcode('compatible_tools', 'compatible_tools');
function compatible_tools()
{
	global $post;
	$show_perangkat_pendukung = get_field( 'show_perangkat_pendukung', $post->ID );
	if( 'yes' === $show_perangkat_pendukung ){
		$list_perangkat   = get_field( 'list_perangkat', $post->ID );
		if( $list_perangkat ){
			echo '<div class="list_perangkat">';
			foreach( $list_perangkat as $dt ){
				echo '<div class="list_item">
					<div class="list_item_image">
						<img src="'. $dt['image_perangkat_pendukung'] .'" alt="Jubelio">
					</div>
					<h4>'. $dt['title_list_perangkat'] .'</h4>
				</div>';
			}	
			echo '</div>';
		}
	}
} 

add_shortcode('partner_list', 'partner_list');
function partner_list()
{
	global $post;
	$show_jubelio_partner_list_ = get_field( 'show_jubelio_partner_list_', $post->ID );
	if( 'yes' === $show_jubelio_partner_list_ ){
		$list_partner   = get_field( 'partner_list', $post->ID );
		if( $list_partner ){
			$layout_style = get_field( 'layout_setting', $post->ID );
		
			if( 'One Row' === $layout_style)
			{
				$class = "one_row";
			}
			else{
				$class = "dynamic";
			}
			
			echo '<div class="partner_list '. $class .'">';
				foreach( $list_partner as $partner ){
					echo '<div class="partner_item">
							<img src="'. $partner['partner_logo'] .'" alt="Jubelio">
						 </div>';
				}
			echo '</div>';
		}
	}
}

add_shortcode('partner_list_affiliate', 'partner_list_affiliate');
function partner_list_affiliate()
{
	global $post;
	$show_partner_list_affiliate = get_field( 'show_partner_list_affiliate', $post->ID );
	
	if( 'yes' === $show_partner_list_affiliate ){
		$affiliate_partner_list   = get_field( 'affiliate_partner_list', $post->ID );
		if( $affiliate_partner_list ){
			
			echo '<div class="partner_list dynamic affiliate_list">';
				foreach( $affiliate_partner_list as $partner ){
					echo '<div class="partner_item">
							<img src="'. $partner['image_affiliate'] .'" alt="Jubelio">
						 </div>';
				}
			echo '</div>';
		}
	}
}

add_shortcode('about_jubelio_milestone', 'about_jubelio_milestone');
function about_jubelio_milestone()
{
	global $post;
	$about_jubelio_milestone = get_field( 'milestones_jubelio', $post->ID );
	if( $about_jubelio_milestone ){
		echo '<div class="box_wrap_list flex_box">';
		foreach( $about_jubelio_milestone as $milestone ){
			echo '<div class="box_list">
					<div class="box_heading" style="background-color: '. $milestone['box_color'] .' ">
						<h3>'. $milestone['heading_milestone'] .'</h3>
					</div>
					<div class="box_text">'. $milestone['milestone_text'] .'</div>
			</div>';
		}
		echo '</div>';
	}
}

add_shortcode('after_sales_list', 'after_sales_list');
function after_sales_list()
{
	global $post;
	$after_sales_list = get_field( 'after_sales_list', $post->ID );
	
	if( $after_sales_list ){
		echo '<div class="sales_step">';
		$j = 0;
		foreach( $after_sales_list as $sales_step ){
			if( $j % 2 == 0 ) {
				echo '<div class="sales_step_list">
						<div class="sales_col_left">
							<h4>'. $sales_step['title_after_sales'] .'</h4>
							<div class="sales_desc">
								'. $sales_step['description_after_sales'] .'
							</div>
						</div>
						<div class="sales_col_right" style="background-color:'. $sales_step['background_color_for_image'] .'">
							<img src="'. $sales_step['image_after_sales'] .'" alt="Jubelio">
						</div>
					 </div>';
			}else{
				echo '<div class="sales_step_list reverse_col">
						<div class="sales_col_left">
							<h4>'. $sales_step['title_after_sales'] .'</h4>
							<div class="sales_desc">
								'. $sales_step['description_after_sales'] .'
							</div>
						</div>
						<div class="sales_col_right" style="background-color:'. $sales_step['background_color_for_image'] .'">
							<img src="'. $sales_step['image_after_sales'] .'" alt="Jubelio">
						</div>
					 </div>';
			}
		$j++; }
		echo '</div>';
	}
}

add_shortcode('jubelio_brand_slider', 'jubelio_brand_slider');
function jubelio_brand_slider()
{
	global $post;
	$jubelio_brand_slider = get_field( 'jubelio_brand', $post->ID );
	if( $jubelio_brand_slider ){
		echo '<div class="box_wrap_brand">';
		foreach( $jubelio_brand_slider as $brand_slider ){
			echo '<div class="box_brand_slider">
					<img src="'. $brand_slider['brand_logo_'] .'">
			</div>';
		}
		echo '</div>';
	}
}

add_shortcode('chat_step', 'chat_step');
function chat_step()
{
	global $post;
	$show_jubelio_chat_step_ = get_field( 'show_jubelio_chat_step_', $post->ID );
	$show_jubelio_other_products_ = get_field( 'show_jubelio_other_products_', $post->ID );
	
	if( 'yes' === $show_jubelio_chat_step_ ||  'yes' === $show_jubelio_other_products_ ){
		$jubelio_chat_step   = get_field( 'jubelio_chat_step', $post->ID );
		$layout_info_box_col = get_field( 'layout_info_box_col', $post->ID );
		$use_link = get_field( 'use_link', $post->ID );
		
		if( 'columns' ===  $layout_info_box_col )
		{
			$class_box = "three_column";
		}
		else
		{
			$class_box = "";
		}
		
		if( $jubelio_chat_step ){
			echo '<div class="other_feature_list chat_step '. $class_box .'">';
			foreach( $jubelio_chat_step as $step ){
				echo '<div class="other_item">';
				if( 'yes' === $use_link )
				{
					echo '<a href="'. $step['link_info_box'] .'">';
				}
				echo  '<div class="other_image">
						<img src="'. $step['image_step_list'] .'" alt="Jubelio">
					</div>
					<div class="content_wrapper">
					<h4>'. $step['title_step_list'] .'</h4>'
					. $step['description_step_list'] .
					'</div>';
				
				if( 'yes' === $use_link )
				{
					echo '</a>';
				}
				
				echo '</div>';
			}
			echo '</div>';
		}
	}
}

add_shortcode('jubelio_step_program', 'jubelio_step_program');
function jubelio_step_program()
{
	global $post;
	
	$show_jubelio_step_program = get_field( 'show_jubelio_step_program_', $post->ID );
	
	if( 'yes' === $show_jubelio_step_program )
	{
		$list_info_program  = get_field( 'list_info_program', $post->ID );
		
		if( $list_info_program )
		{
			echo '<div class="info_box_program">';
			foreach( $list_info_program as $program )
			{
				echo '<div class="info_program_list">
					<div class="info_program_image">
						<img src="'. $program['image_info_program'] .'" alt="'. $program['title_info_program'] .'">
					</div>
					<div class="info_program_content">
						<h4>'. $program['title_info_program'] .'</h4>
						'. $program['description_info_program_list'] .'
					</div>
				</div>';
			}
			echo '</div>';
		}
	}
}

add_shortcode('why_box', 'why_box');
function why_box()
{
	global $post;
	
	$show_why_box_ = get_field( 'show_why_box_', $post->ID );
	if( 'yes' === $show_why_box_ )
	{
		$item_list_box_   = get_field( 'item_list_box_', $post->ID );
		
		if( $item_list_box_ ){
			echo '<div class="why_box_list"><ul>';
			foreach( $item_list_box_ as $list_box ){
				echo '<li class="box_item">
				<h4>'. $list_box['item_title_why_box'] .'</h4>
				<p>'. $list_box['item_description_why_box'] .'</p>
				</li>';
			}
			echo '</ul></div>';
		}
	}
}

add_shortcode('wp_redirect_link', 'wp_redirect_link');
function wp_redirect_link() {
	if( is_user_logged_in() ) {
		echo "<p class='notifications'>
		        Anda sudah login. Silahkan kembali ke halaman Affiliate Area
		      </p>
		      <div class='button_wrapper'>
		        <a href='https://jubelio.com/affiliate-area/'>Affiliate Dashboard</a>
		        <a href='" . wp_logout_url( get_permalink() ) . "'>Logout</a>
		      </div>
		       
		";
	}
}

add_shortcode('faq_list', 'faq_list');
function faq_list()
{
	global $post;
	
	$show_faq_ = get_field( 'show_faq_', $post->ID );
	if( 'yes' === $show_faq_ )
	{
		$faq_list   = get_field( 'faq_list', $post->ID );
		
		if( $faq_list )
		{
			echo '<div id="accordion">';
			foreach( $faq_list as $faq )
			{
				echo '<h3><span class="img_acc"><img src="https://jubelio.com/wp-content/uploads/2023/12/question-icon-1.svg"></span><span class="title_acc">'. $faq['title_faq'] .'</span></h3>
					<div>
						<p>'. $faq['description_faq'] .'</p>
					</div>
				';
			}
			echo '</div>';
		}
		
	}
}

add_shortcode('group_partner_list', 'group_partner_list');
function group_partner_list( $atts )
{
	global $post;
	$partner = $atts['partner'];
	$partner_link_page = get_field( 'activated_partner_link', $post->ID );
	
	if( 'fulfillment' === $partner )
	{
		$show_fulfillment_partner_ = get_field( 'show_fulfillment_partner_', $post->ID );
		
		if( 'yes' === $show_fulfillment_partner_ )
		{
			$fulfillment_partner_list   = get_field( 'fulfillment_partner_list', $post->ID );
			
			if( $fulfillment_partner_list )
			{
				echo '<div class="partner_list dynamic">';
				foreach( $fulfillment_partner_list as $partner ){
					echo '<div class="partner_item">';
					if( 'yes' === $partner_link_page ) {
						echo '<a href="'. $partner['fulfillment_partner_link'] .'" target="_blank">';
					}
						echo '<img src="'. $partner['fulfillment_partner_logo'] .'" alt="Jubelio">';
					if( 'yes' === $partner_link_page ) {
						echo '</a>';
					}
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
	
	if( 'jubelio_shipment' === $partner )
	{
		$show_jubelio_shipment_partner_ = get_field( 'show_jubelio_shipment_partner_', $post->ID );
		
		if( 'yes' === $show_jubelio_shipment_partner_ )
		{
			$jubelio_shipment_partner_list   = get_field( 'jubelio_shipment_partner_list', $post->ID );
			
			if( $jubelio_shipment_partner_list )
			{
				echo '<div class="partner_list dynamic">';
				foreach( $jubelio_shipment_partner_list as $partner_j ){
					echo '<div class="partner_item">';
					if( 'yes' === $partner_link_page ) {
						echo '<a href="'. $partner_j['jubelio_shipment_partner_link'] .'" target="_blank">';
					}
					echo '<img src="'. $partner_j['jubelio_shipment_partner_logo'] .'" alt="Jubelio">';
					if( 'yes' === $partner_link_page ) {
						echo '</a>';
					}
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
	
	if( 'financing' === $partner )
	{
		$show_financing_partner_ = get_field( 'show_financing_partner_', $post->ID );
		
		if( 'yes' === $show_financing_partner_ )
		{
			$financing_partner_list   = get_field( 'financing_partner_list', $post->ID );
			
			if( $financing_partner_list )
			{
				echo '<div class="partner_list dynamic">';
				foreach( $financing_partner_list as $partner_f ){
					echo '<div class="partner_item">';
					if( 'yes' === $partner_link_page ) {
						echo '<a href="'. $partner_f['financing_partner_logo_link'] .'" target="_blank">';
					}
					echo '<img src="'. $partner_f['financing_partner_logo'] .'" alt="Jubelio">';
					if( 'yes' === $partner_link_page ) {
						echo '</a>';
					}
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
	
	if( 'enabler' === $partner )
	{
		$show_enabler_partner__ = get_field( 'show_enabler_partner__', $post->ID );
		
		if( 'yes' === $show_enabler_partner__ )
		{
			$enabler_partner_list   = get_field( 'enabler_partner_list', $post->ID );
			
			if( $enabler_partner_list )
			{
				echo '<div class="partner_list dynamic">';
				foreach( $enabler_partner_list as $partner_en ){
					echo '<div class="partner_item">';
					if( 'yes' === $partner_link_page ) {
						echo '<a href="'. $partner_en['enabler_partner_logo_link'] .'" target="_blank">';
					}
					echo '<img src="'. $partner_en['enabler_partner_logo'] .'" alt="Jubelio">';
					if( 'yes' === $partner_link_page ) {
						echo '</a>';
					}
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
	
	if( 'erp' === $partner )
	{
		$show_sistem_erp_partner_ = get_field( 'show_sistem_erp_partner_', $post->ID );
		
		if( 'yes' === $show_sistem_erp_partner_ )
		{
			$sistem_erp_partner_list   = get_field( 'sistem_erp_partner_list', $post->ID );
			
			if( $sistem_erp_partner_list )
			{
				echo '<div class="partner_list dynamic">';
				foreach( $sistem_erp_partner_list as $partner_s ){
					echo '<div class="partner_item">';
					if( 'yes' === $partner_link_page ) {
						echo '<a href="'. $partner_s['sistem_erp_partner_logo_link'] .'" target="_blank">';
					}
					echo '<img src="'. $partner_s['sistem_erp_partner_logo'] .'" alt="Jubelio">';
					if( 'yes' === $partner_link_page ) {
						echo '</a>';
					}
						
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
}

add_filter('the_content', 'mte_remove_unused_shortcode');
function mte_remove_unused_shortcode($content) { 
	$pattern = mte_get_unused_shortcode_regex();
	$content = preg_replace_callback( '/'. $pattern .'/s', 'strip_shortcode_tag', $content );
	
	return $content; 
}
 
function mte_get_unused_shortcode_regex() {
	global $shortcode_tags;
	$tagnames = array_keys($shortcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	$regex = '\[(\[?)';
	$regex .= "(?!$tagregexp)";
	$regex .= '\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
	
	return $regex; 
}

/**
 * Add Superadministration Role
 */
add_role(
    'developer', //  System name of the role.
    __( 'Developer'  ), // Display name of the role.
    array(
        'read'  => true,
        'delete_posts'  => true,
        'delete_published_posts' => true,
        'edit_posts'   => true,
        'publish_posts' => true,
        'edit_published_posts'   => true,
        'upload_files'  => true,
        'moderate_comments'=> true, // This user will be able to moderate the comments.
    )
);

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
     
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
 
    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
 
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
 
// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
 
// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);
 
// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
	remove_menu_page('edit.php?post_type=ae_global_templates');
	
	$current_user = wp_get_current_user();
	$role = ( array ) $current_user->roles;
	
	// If the user is an administrator, remove some menu
	if ( in_array( 'administrator', $role ) )
	{
		//remove_menu_page('edit.php?post_type=acf-field-group');
		remove_menu_page('wp-mail-smtp');
		remove_menu_page('menu-image-options');
		remove_menu_page('smush');
		remove_menu_page('mlang');
	}
});
 
// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

function my_mce_buttons_2($buttons) {
    array_unshift($buttons, 'fontsizeselect');
    return $buttons;
}
add_filter('mce_buttons_2', 'my_mce_buttons_2');

function my_mce_before_init_insert_formats($initArray) {
    $initArray['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
    return $initArray;
}
add_filter('tiny_mce_before_init', 'my_mce_before_init_insert_formats');

add_filter( 'googlesitekit_analytics_tracking_disabled', function( $disabled ) {
  
    if ( is_page( 'update-page-new' ) ) {
        return true; // disable Analytics tracking on this page
    }

    return $disabled;
});

add_filter( 'googlesitekit_gtm_output_tag', function( $output, $container_id ) {
    
    if ( is_page( 'update-page-new' ) ) {
        return ''; // prevent GTM script from being printed
    }

    return $output;
}, 10, 2 );

add_action('wp_head', 'your_function_name');
function your_function_name(){
	
	// Replace with your custom page ID or slug
    if ( is_page('update-page-new') ) {
        return; // stop here, no pixel on this page
    }
?>

<?php
};

function populate_select_with_hierarchy($tag) {
    if ($tag['name'] !== 'select-category') {
        return $tag;
    }

    $csv_path = WP_CONTENT_DIR . '/uploads/category.csv'; // Adjust the path to your CSV file
    if (!file_exists($csv_path)) {
        return $tag;
    }

    $categories = [];
    if (($handle = fopen($csv_path, 'r')) !== false) {
        $row = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;
            if ($row === 1) {
                continue; // Skip header row
            }

            $parent = trim($data[0]);
            $child = trim($data[1]);

            if ($parent && $child) {
                $categories[$parent][] = $child;
            }
        }
        fclose($handle);
    }

    $options = [];
    foreach ($categories as $parent => $children) {
        foreach ($children as $child) {
            $options[] = "$parent - $child"; // Add subcategories
        }
    }

    $tag['raw_values'] = $options;
    $tag['values'] = $options;
    $tag['labels'] = $options;

    return $tag;
}
add_filter('wpcf7_form_tag', 'populate_select_with_hierarchy', 10, 2);

function populate_provinceselect_with_hierarchy($tag) {
    if ($tag['name'] !== 'select-location') {
        return $tag;
    }

    $csv_path = WP_CONTENT_DIR . '/uploads/cities_in_Indonesia.csv'; // Adjust the path to your CSV file
    if (!file_exists($csv_path)) {
        return $tag;
    }

    $province = [];
    if (($handle = fopen($csv_path, 'r')) !== false) {
        $row = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;
            if ($row === 1) {
                continue; // Skip header row
            }

            $parent = trim($data[0]);
            $child = trim($data[1]);

            if ($parent && $child) {
                $province[$parent][] = $child;
            }
        }
        fclose($handle);
    }

    $options = [];
    foreach ($province as $parent => $children) {
        foreach ($children as $child) {
            $options[] = "$parent - $child"; // Add subcategories
        }
    }

    $tag['raw_values'] = $options;
    $tag['values'] = $options;
    $tag['labels'] = $options;

    return $tag;
}
add_filter('wpcf7_form_tag', 'populate_provinceselect_with_hierarchy', 10, 2);

function enqueue_select2_assets() {
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);

    // Localize the string for the placeholder
    wp_localize_script('select2-js', 'select2_params', [
        'placeholder' => __('Please choose an option', 'jubelio') // Add translatable string
    ]);

    // Initialize Select2 on the form select fields
    wp_add_inline_script('select2-js', "
        jQuery(document).ready(function($) {
            // Initialize Select2 for all selects with 'wpcf7-select' class
            $('.wpcf7-select').select2({
                placeholder: select2_params.placeholder,  // Use localized placeholder
                allowClear: true  // Allow clearing the selection
            });
    
        });
    ");
}
add_action('wp_enqueue_scripts', 'enqueue_select2_assets');

/**
 * Create custom post type: Ebook
 */
register_post_type('ebook', array(
    'labels' => array(
        'name'               => _x('E-book', 'post type general name'),
        'singular_name'      => _x('E-book', 'post type singular name'),
        'add_new'            => _x('Add New', 'ebooks'),
        'add_new_item'       => __('Add New E-book'),
        'edit_item'          => __('Edit E-book'),
        'new_item'           => __('New E-book'),
        'view_item'          => __('View E-book'),
        'search_items'       => __('Search E-books'),
        'not_found'          => __('No E-books found'),
        'not_found_in_trash' => __('No E-books found in trash'),
    ),

    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'query_var'          => true,
    'menu_icon'          => 'dashicons-media-document',
    'hierarchical'       => false,
    'menu_position'      => null,
    'show_in_nav_menus'  => true,
    'has_archive'        => true, // optional

    'rewrite' => array(
        'slug'       => 'ebook',
        'with_front' => false,
    ),

    'capability_type'    => 'post',
    'supports'           => array('title', 'thumbnail', 'excerpt', 'editor'),
));


function get_pdf() {
    if (isset($_GET['pdf_id'])) {
        $pdfid = sanitize_text_field($_GET['pdf_id']); // Security: sanitize input
         
        // Get ACF PDF file field
        $pdf_file = get_field('e-book_file', $pdfid);

        // Get post title
        $post_title = get_the_title($pdfid);

        // Get post thumbnail (featured image)
        $thumbnail_url = get_the_post_thumbnail_url($pdfid, 'full');
        
        // Prepare HTML structure
        $html = '<div class="wrap_pdf">';

        // If thumbnail exists, show image
        if ($thumbnail_url) {
            $html .= '<div class="pdf_image"><img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr($post_title) . '" style="max-width: 100%; height: auto;"></div>';
        }

        // Show the post title
        if ($post_title) {
            $html .= '<div class="pdf_title"><h3>' . esc_html($post_title) . '</h3></div>';
        }

        // Show download link if PDF file exists
        if ($pdf_file) {
            $html .= '<div class="pdf_button"><a href="' . esc_url($pdf_file) . '" target="_blank">Download</a></div>';
        } else {
            $html .= '<div class="pdf_button"><p>PDF Tidak ditemukan.</p></div>';
        }

        $html .= '</div>';
        
        // Add JavaScript to replace &amp; with & in URLs
        $html .= '<script type="text/javascript">
                    document.addEventListener("DOMContentLoaded", function() {
                        var links = document.querySelectorAll("a[href*=\"&amp;\"]");
                        links.forEach(function(link) {
                            link.href = link.href.replace(/&amp;/g, "&");
                        });
                    });
                  </script>';

        return $html;
    }

    return 'PDF Tidak ditemukan';
}
add_shortcode('get_pdf', 'get_pdf');

function get_current_post_id_shortcode() {
    $post_ids = get_the_ID();
    $link = 'https://jubelio.com/terima-kasih-e-book/?pdf_id=' . $post_ids;

    return urldecode($link);
}
add_shortcode('current_post_id', 'get_current_post_id_shortcode');

add_action('wp_head', function() {
    if (is_page()) {
        $schema = get_field('schema_json'); // ACF field
        if ($schema) {
            echo '<script type="application/ld+json">' . $schema . '</script>';
        }
    }
});

// JavaScript fallback to block GA4 page_view if injected elsewhere
add_action( 'wp_footer', function() {
    if ( is_page( array( 'update-page-new' ) ) ) : ?>
        <script>
        (function() {
            // Remove GA4 <script> tags (direct gtag.js)
            document.querySelectorAll('script[src*="googletagmanager.com/gtag/js"]').forEach(el => el.remove());

            // Block GA4 page_view event even if gtag.js is already loaded
            window.dataLayer = window.dataLayer || [];
            const originalPush = window.dataLayer.push;
            window.dataLayer.push = function() {
                if (arguments[0] && arguments[0].event === 'page_view') {
                    console.log('GA4 page_view blocked on this page');
                    return;
                }
                return originalPush.apply(window.dataLayer, arguments);
            };

            // Overwrite gtag() if it exists, block page_view calls
            if (typeof window.gtag === 'function') {
                const originalGtag = window.gtag;
                window.gtag = function() {
                    if (arguments[0] === 'event' && arguments[1] === 'page_view') {
                        console.log('GA4 gtag page_view blocked');
                        return;
                    }
                    return originalGtag.apply(this, arguments);
                };
            }
        })();
        </script>
    <?php endif;
});

add_action( 'elementor/query/combined_resource_query', function( $query ) {
    // 1. Set the post types
    $query->set( 'post_type', [ 'ebook', 'whitepaper', 'template' ] );
    
    // 2. Force status to publish (good practice)
    $query->set( 'post_status', 'publish' );
} );

add_action( 'wp_footer', function() {
    if ( is_page( array( 'update-page-new' ) ) ) : ?>
        <script>
        (function() {
            // Remove GTM <script> tags dynamically
            document.querySelectorAll('script[src*="googletagmanager.com/gtm"]').forEach(el => el.remove());

            // Remove GTM <noscript> iframe
            document.querySelectorAll('noscript iframe[src*="googletagmanager.com/ns.html"]').forEach(el => el.remove());

            // Block GA4 events if already loaded
            if (window.dataLayer) {
                window.dataLayer = window.dataLayer.filter(function(e) {
                    // Keep everything except GA4 page_view events
                    return !(e.event && e.event === 'page_view');
                });

                // Intercept future pushes
                const originalPush = window.dataLayer.push;
                window.dataLayer.push = function() {
                    if (arguments[0] && arguments[0].event === 'page_view') {
                        console.log('GA4 page_view blocked on this page');
                        return;
                    }
                    return originalPush.apply(window.dataLayer, arguments);
                };
            }
        })();
        </script>
    <?php endif;
});

add_filter( 'xmlrpc_enabled', '__return_false' );
add_action( 'init', function() {
    if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
        wp_die( 'XML-RPC is disabled on this site.', 'Forbidden', array( 'response' => 403 ) );
    }
});

add_filter('option_blogname', function ($value) {
    return $value ?: 'Jubelio';
});

function add_bing_verification_meta() {
    echo '<meta name="msvalidate.01" content="7C405658CB7F677DCA91C40FB791C06A" />' . "\n";
}
add_action('wp_head', 'add_bing_verification_meta');



