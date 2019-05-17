// Theme JavaScript

(function($) {
    // Tab JS
    $( 'ul.nav.nav-tabs  a' ).click( function ( e ) {
        e.preventDefault();
        $( this ).tab( 'show' );
    } );
    
    // Owl slider JS 

    $('.owl-carousel').owlCarousel({
        loop: true,
        autoplay: true,
        margin: 0,
        responsiveClass: true,
        dots: true,
        responsive: {
            0: {
                items: 1,
                nav: true
            },
            600: {
                items: 1,
                nav: false
            },
            767: {
                items: 1,
                nav: false
            },
            1000: {
                items: 1,
                nav: true,
                loop: true
            }
        }
    })

    // Custom Scrollbar JS 
    $(window).on("load", function () {
        if ($('.aboutContent.mCustomScrollbar').length >= 1) {
            $(".aboutContent").mCustomScrollbar();
        }
    });


})(jQuery);
