var filter;
var isLoadMore = true;
var canLoadMore = true;
$(document).ready(function(){
    $(document).on('click','[data-request="filter-paginate"]',function(e){
        $('.pager').show();

        /*******************D E F A U L T ** H A N D L E S*********************/
        var $this        = $(this);
        var $url         = $this.data('url');
        var $target      = $this.data('target');
        var $form        = $this.data('form');
        var $showing     = $this.data('showing');
        var $loadmore    = $this.data('loadmore');    
        var $data        = new FormData($($form)[0]);
        // console.log($url);
        filter = $.ajax({
            url: $url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'post',
            data: $data, 
            beforeSend: function(){
                if(filter){
                    filter.abort();
                }
            },
            success: function($response){
                if($response.paging === true){
                    $($target).append($response.data);
                }else{
                    $($target).html($response.data);
                }
                
                $('[name="page"]').val(parseInt($('[name="page"]').val())+1);
                $('.filter-result-title').html($response.filter_title);

                canLoadMore = $response.can_load_more;

                $($loadmore).html($response.loadMore);
                $($loadmore).css("display", "none"); 
                $('.pager').hide();
            },
            error: function(error){
                $('.pager').hide();
            }
        }); 
    });

    if($('[data-request="filter-paginate"]').length > 0){
        isLoadMore = false;
        $('[name="page"]').val(1);
        $('[data-request="filter-paginate"]').trigger('click');
    }

    $(document).on('blur change','input[name*="date_filter"]',function(){
        isLoadMore = false;
        $('[name="page"]').val(1);
        $('[data-request="filter-paginate"]').trigger('click');
    });
    
    $(document).on('click','input[type="checkbox"]',function(){
        if($(this).data('request') !== 'inline-ajax' && $(this).attr('id') !== 'show-description'){
            isLoadMore = false;
            $('[name="page"]').val(1);
            $('[data-request="filter-paginate"]').trigger('click');
        }
    });

    $(document).on('change','select',function(){
        isLoadMore = false;
        $('[name="page"]').val(1);
        $('[data-request="filter-paginate"]').trigger('click');
    });

    $(document).on('keyup','[data-request="search"]',function(){
        isLoadMore = false;
        $('[name="page"]').val(1);
        $('[data-request="filter-paginate"]').trigger('click');
    });

    $(document).on('keyup','input[type="text"]',function(){
        if($(this).attr('name') === 'search'){
            return false;
        }

        isLoadMore = false;
        $('[name="page"]').val(1);
        $('[data-request="filter-paginate"]').trigger('click');
    });

    $(document).on('click','[data-request="clear-filter"]',function(){
        writeCookie('city-filter','');
        window.location = $(this).data('url');
    });
});

var contains = function(needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if(!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                var item = this[i];

                if((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle) > -1;
};

/* Code for scroll load more*/

// $(window).scroll(function() {
//     if($(window).scrollTop() == $(document).height() - $(window).height()) {
//         if(canLoadMore){
//         isLoadMore = true;
//         $('[data-request="filter-paginate"]').trigger('click');   
//         }
//     }
// });
/* OR */
$(window).on('scroll',function () { 
    if ($(window).outerHeight()+ $(window).scrollTop()== $(document).outerHeight()) { 
        if(canLoadMore){
            isLoadMore = true;
            $('[data-request="filter-paginate"]').trigger('click');   
        } 
    } 
});