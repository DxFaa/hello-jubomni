jQuery(document).ready( function() { 
	slider_marketplace();
	slider_vendor();
	toggle_button_fitur();
	fitur_custom_tabs();
	jubelio_accordion();
	jubelio_brand_slider();
	whatsapp_popup();
	slider_testimonial();
	affiliate_();
});

function isMobile() {
	return jQuery(window).width() <= 500; 
}

function loadMobileScript() {
	if (isMobile()) {
		jQuery('.pp-advanced-tabs-content .gallery figure').hide();
		jQuery('.pp-advanced-tabs-content .gallery figure:nth-child(n+1):nth-child(-n+6)').slideToggle();
				
		jQuery('.testimonial_tab .pp-tabs-panel a').click(function(){
			jQuery(this).parent().parent().find('.gallery figure:not(:visible):lt(6)').slideToggle();
			
			var container = jQuery(this).parent().parent().parent().find('.pp-advanced-tabs-content');
			var totalHeight = 0;

			container.find('.gallery figure:visible').each(function() {
				totalHeight += jQuery(this).outerHeight(true);
			});

			container.height(totalHeight);
	
			return false;
		})
	}
}

function toggle_more_less() {
	if( jQuery('.testimonial_tab').length > 0 ) {
		
		loadMobileScript();
		
		jQuery(window).resize(function() {
			loadMobileScript();
		})
	}
}

function slider_vendor() {
	if( jQuery('.testimonial_tab').length > 0 )
	{
      	jQuery('.testimonial_tab .pp-advanced-tabs-content-wrapper').on('init', function(event, slick){
        	jQuery('.testimonial_tab .pp-advanced-tabs-wrapper .pp-advanced-tabs-title').each(function(index) {
			  jQuery(this).attr('data-slide', index);
			});
		})
		
		jQuery('.testimonial_tab .pp-advanced-tabs-content-wrapper').slick({
		  infinite: true,
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  arrows: false,
		  autoplay: true,
		  mobileFirst: true,
		  speed: 1000,
		  fade: true,
		  autoplaySpeed: 5000,
		  adaptiveHeight: true,
		});
	
		jQuery('.pp-advanced-tabs-title').on('click', function() {
		  var slideIndex = jQuery(this).data('slide');
		  jQuery('.testimonial_tab .pp-advanced-tabs-content-wrapper').slick('slickGoTo', slideIndex);
		});
		
		jQuery('.testimonial_tab .pp-advanced-tabs-content-wrapper').on('afterChange', function(event, slick, currentSlide, nextSlide){
		   var get_index = jQuery('.slick-slide.slick-current.slick-active').attr('data-slick-index');
		  jQuery('.testimonial_tab .pp-advanced-tabs-wrapper').find(".pp-advanced-tabs-title").removeClass("pp-tab-active");
		  jQuery('.testimonial_tab .pp-advanced-tabs-wrapper .pp-advanced-tabs-title[data-slide="' + get_index + '"]').addClass("pp-tab-active");
		});
	}
}

function affiliate_() {
    if( jQuery('body.page-id-19768.logged-in').length > 0 ){
      window.location.href = 'https://jubelio.com/affiliate-area/';
    }
}

function slider_testimonial(){
	if( jQuery('.testimonials_slider_section').length > 0 )
	{
		jQuery('.testimonials_slider_section > div').slick({
		  infinite: true,
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  arrows: false,
		  autoplay: true,
		  mobileFirst: true,
		  speed: 1000,
		  centerMode: false,
		  dots: true,
		  fade: true,
		  cssEase: 'ease-in-out',
		});
	}
}
function whatsapp_popup()
{
	setTimeout(function() {
		jQuery('.wa__btn_popup').trigger('click');
	}, 5e3);
}

function jubelio_brand_slider()
{
	if( jQuery('.box_wrap_brand').length > 0 )
	{
		console.log('ada brand');
		jQuery('.box_wrap_brand').slick({
			slidesToShow: 5,
			slidesToScroll: 1,
			arrows: false,
			autoplay: true,
			mobileFirst: true,
			speed: 1000,
			autoplaySpeed: 2000,
			centerMode: false
		});
	}
}
function contact_form_redirect()
{
	var get_lang = document.documentElement.lang; 
	
	if( get_lang === 'id-ID')
	{
		if( jQuery('#wpcf7-f1460-p185-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f1460-p185-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/terima-kasih/?affiliate';
			}, false );
		}
	
		if( jQuery('#wpcf7-f1398-p190-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f1398-p190-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/terima-kasih/?ambassador';
			}, false );
		}
	
		if( jQuery('#wpcf7-f15011-p44-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f15011-p44-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/terima-kasih/?demo';
			}, false );
		}
	}
	
	if( get_lang === 'en-US')
	{
		if( jQuery('#wpcf7-f1460-p185-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f1460-p185-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/en/thankyou/?affiliate';
			}, false );
		}
	
		if( jQuery('#wpcf7-f1398-p190-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f1398-p190-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/en/thankyou/?ambassador';
			}, false );
		}
	
		if( jQuery('#wpcf7-f15011-p44-o1').length > 0 )
		{
			document.getElementById('#wpcf7-f15011-p44-o1 form').addEventListener( 'wpcf7mailsent', function( event ) {
			  location = 'https://landing-v2.staging-k8s.jubelio.com/en/thankyou/?demo';
			}, false );
		}
	}
	
}

function jubelio_accordion()
{
	if( jQuery('#accordion').length > 0 )
	{
		 var icons = {
			  header: "iconClosed",
			  activeHeader: "iconOpen"
		 };
		
		 jQuery('#accordion').accordion({
			 collapsible: true,
			 icons: icons
		 });
	}
}
function fitur_custom_tabs()
{
	if( jQuery('#responsiveTabsDemo').length > 0 ){
		var tabs = jQuery("#responsiveTabsDemo");
		
		tabs.responsiveTabs({
		  rotate: false,
		  animation: "fade",
		  scrollToAccordion: true,
		  startCollapsed: "accordion",
		  collapsible: "accordion",
		  setHash: true,
		  activate: function (e, tab) {
			jQuery(".info").html("Tab <strong>" + tab.id + "</strong> activated!");
		  },
		  activateState: function (e, state) {
			jQuery(".info").html(
			  "Switched from <strong>" +
				state.oldState +
				"</strong> state to <strong>" +
				state.newState +
				"</strong> state!"
			);
		  },
    	});
	}
}

function toggle_button_fitur()
{
	if( jQuery('.toggle_nonjubelio').length > 0 )
	{
		jQuery('.toggle_nonjubelio').addClass('toggle_active');
		
		jQuery('.toggle_nonjubelio a').on('click', function(){
			jQuery('.section_jubelio').fadeOut(0);
			jQuery('.section_nonjubelio').delay(10000).fadeIn(500);
			
			jQuery('.toggle_nonjubelio').addClass('toggle_active');
			jQuery('.toggle_jubelio').removeClass('toggle_active');
			return false;
		});
		
		jQuery('.toggle_jubelio a').on('click', function(){
			jQuery('.section_nonjubelio').fadeOut(0);
			jQuery('.section_jubelio').delay(10000).fadeIn(500);
			
			jQuery('.toggle_jubelio').addClass('toggle_active');
			jQuery('.toggle_nonjubelio').removeClass('toggle_active');
			
			return false;
		});
	}
}
function slider_marketplace()
{
	if( jQuery('.slider_marketplace').length > 0 )
	{
		jQuery('.slider_marketplace > div').slick({
		  infinite: true,
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  arrows: false,
		  autoplay: true,
		  mobileFirst: true,
		  speed: 100,
		  autoplaySpeed: 100,
		  centerMode: false
		});
	}
}
