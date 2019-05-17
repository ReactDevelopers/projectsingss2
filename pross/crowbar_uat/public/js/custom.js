$(window).on('load', function(){

	
    if ($(window).width() <= 1024) {
    	$('.open-toggle-menu').click( function(){
	    	$('.resp-tabs-list').slideToggle();
	    	$(this).parent().toggleClass('active');
	    });

	   
	    $('html, body').on('click', function(e){ var targetElem = $(e.target);
	    if (!targetElem.is('.toggle-menu-wrap') && !targetElem.is('.toggle-menu-wrap *')) { $('.resp-tabs-list').slideUp();
	    	$('.open-toggle-menu').parent().removeClass('active'); } }); 
    }

	// Testimonial Slider
    if($(".slick-slider").length > 0){
	    $(".slick-slider").slick({
		  	infinite: true,
		  	slidesToShow: 2,
		  	dots: false,
		  	arrows: false,
		  	autoplay: true,
	  	  	autoplaySpeed: 5000,

		  	responsive: [{

		      	breakpoint: 1024,
		      	settings: {
		        	slidesToShow: 2,
		        	infinite: true
		      	}
		    },{
		      	breakpoint: 767,
		      	settings: {
		        	slidesToShow: 1,
		        	infinite: true
		      	}
		    }]
		});
	}
});

// Listing carousel



$(document).ready( function(){

	$('.scrollNextLink').click( function(e){
		e.preventDefault();
		var target = $(this).attr('href');
		
		$('html, body').animate({
			scrollTop: $(target).offset().top
		}, 1200);
	});


	//Autocomplete
	// $('#skills').easySelect({
	// 	removeIcon: '<img src="'+base_url+'/images/close-icon.png" alt="x" />'
	// });
	// $('#skills + .easyselect-container .easyselect-field').addClass('form-control').attr('placeholder', 'Enter skills you offer');


	//Datepicker
	
	

	if ($('#datetimepicker').length>0) {
		var todayDate = new Date().getDate();
	    $('#datetimepicker').datetimepicker({
	    	format: 'DD/MM/YYYY',
	    	minDate: new Date()/*,
	    	maxDate: new Date(new Date().setDate(todayDate + 90))*/
	    });
	}

});


$(document).on('change', '.custom-dropdown select', function(){
 	if($(this).val()){
  		$(this).next('.select2').addClass('active');
    }else {
  		$(this).next('.select2').removeClass('active');
    }
});

setTimeout(function(){
	$('.custom-dropdown select').each(function (){
		if($(this).val()){
	  		$(this).next('.select2').addClass('active');
	    }else {
	  		$(this).next('.select2').removeClass('active');
	    }
	});
},800);

$(document).on('ready',function(){
	$('.dataTables_empty').parents('.datatable-listing').addClass('nothing-found');

});


