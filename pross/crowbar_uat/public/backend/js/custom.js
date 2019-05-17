$(document).ready(function() {

	$('header .toggle-btn').click(function(){
		//$(this).parent('div, section').toggleClass('open');
		$('.dashboard-left').slideToggle(300);
		$('.categories').slideToggle(300);
	});

});

// Window Scroll Functions //

/////////////////////////////////////////////////////////////////////////////

$(window).scroll( function(){
	var win_scroll = $(window).scrollTop();
		win_hgt = $(window).innerHeight();
		body_hgt = $('body').height();
		header_hgt = $('.dashboard-header').height();
		home_footer_hgt = $('.home-footer').height();

	if(window.innerWidth > 768){

		// For Header Fix //

		if(win_scroll >= 10){
			$('.dashboard-header').addClass('fixed');
			$('.wrapper').css({ "padding-top": header_hgt+"px" });
		}
		else{
			$('.dashboard-header').removeClass('fixed');
			$('.wrapper').css({ "padding-top": "0" });
		}

		// For Categories Fix //

	var categories_hgt = $('.categories').height();
		//categories_btm_pos = $('.categories').offset().top + categories_hgt;

		if(categories_hgt < (win_hgt - home_footer_hgt - header_hgt)){
			if(win_scroll >= 220){
				$('.categories').addClass('fixed-top');
			}
			else{$('.categories').removeClass('fixed-top');}
		}
		else{
			$('.categories').removeClass('fixed-top');
		}

		if(((win_hgt - home_footer_hgt - header_hgt) < categories_hgt) && ($('.panel-body .thumbnail').length >= 6)){

			if(win_scroll >= 400){
				if((win_scroll + win_hgt) >= categories_hgt){
					$('.categories').addClass('fixed-bottom');
				}
				else{$('.categories').removeClass('fixed-bottom');}
			}
			else{$('.categories').removeClass('fixed-bottom');}
		}
		else{
			$('.categories').removeClass('fixed-bottom');
		}
	}
	else{}
});

//////////////////////////////////////////////////////////////////////////////

$(window).on("resize load", function(){

	var sec_right_height = $('.section-right').height();
		header_hgt = $('.dashboard-header').height();
		banner_hgt = $('.banner').height();
		dashbrd_rgt_hgt = $('.dashboard-right').height();
		dashbrd_lft_wdt = $('.dashboard-left').width();
		footer_hgt = $('.footer').height();
		profile_pic_wdt = $('.message-panel .profile-pic').width();
		msg_panel_wdt = $('.message-panel').width();
		msg_wdt = $('.message-panel .message').width();
		thumb_width = $('.product-panel .thumbnail').innerWidth();
		snd_msg_hgt = $('.send-message').height();

	/*    Forgot Password Page    */
	if(window.innerWidth > window.innerHeight){
		$('.forgot-pswrd-form').css({
			'margin-top': + (sec_right_height-40)/4 +'px'
		});
	}
	else{}

	/*    Dashboard Pages    */

	$('.profile-name').css({
		'width': + (msg_wdt-profile_pic_wdt)-15 +'px'
	});
	
	// For Profile Dashboard width

    if(window.innerWidth >= 1350){
        $('.my-shop .product-panel.shop-products-list').css({
            'width': ($('html').innerWidth() - $(".dashboard-left").width() - msg_panel_wdt - 40) +'px'
        });
    }
    else{
    }

	if(window.innerWidth > (768)){
		$('.message-panel .panel-body').css({
			'height': + (window.innerHeight)-87 +'px'
		});

		$('.outer-page .message-panel .panel-body').css({
			'height': + (window.innerHeight-header_hgt-footer_hgt)-76 +'px'
		});

		$('.message-board .message-container').css({
			'height': + (window.innerHeight-header_hgt-footer_hgt-snd_msg_hgt)-221 +'px'
		});

	}
	else{
	}

    // Header Width

    if(window.innerWidth >= 991){
        $('.dashboard-header').css({
            'width': ($('html').innerWidth() - msg_panel_wdt) +'px'
        });
    }
    else{
        $('.dashboard-header').css({
            'width': '100%'
        });
    }
	
	if(window.innerWidth > (735)){
		$('.dashboard-left').css({
			'padding-top': + (header_hgt + banner_hgt) +'px'
		});
	}
	else{
	}

});

// Masonry grid //

/*$(window).on('load resize', function(){
	var win_width = $(window).innerWidth();

		 $('.product-panel .masonry-grid').masonry({
			// options
			itemSelector : '.thumbnail',
			columnWidth: function( containerWidth ) {

				if(win_width >= 1400){
					return containerWidth / 4;
				}
				else{}
				if(win_width >= 768){
					return containerWidth / 3;
				}
				else{}
				if(win_width <= 767){
					return containerWidth / 2;
				}
				else{}
			}
		});
});*/

$(window).on('load resize orientationchange', function(){
    setTimeout( function(){
        $('.masonry-grid').masonry({
            // set itemSelector so .grid-sizer is not used in layout
            itemSelector: '.thumbnail',
            // use element for option
            //columnWidth: '.thumbnail-sizer',
            percentPosition: true
        });
    }, 2000);
});


// Banner Slider //

$('.bxslider').bxSlider({
  auto: 'true'
});
