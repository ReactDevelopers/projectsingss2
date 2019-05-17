$(function(){
	$(document).on('click','[data-request="profile-calendar"]',function(){
		$('.pager').show();
		var $this 	= $(this);
		var $url 	= $this.data('url');
		var $target = $this.data('target');

        var $options = {
            "header":{
                "left":"prev title next",
                "center":"",
                "right":"month,agendaWeek,agendaDay"
            },
            "editable":true,
            "eventLimit":true,
            "navLinks":true,
            "events":function( start, end, timezone, callback ) {
                var b = $($target).fullCalendar('getDate');
                var selected_date = b.format('YYYY-MM-DD');
                $.ajax({
                    url: $url + '?date='+selected_date,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'get',
                    success: function($response){
                        if($response.status === true){
                            callback($response.data);
                        }else{
                            /*console.log($response.data);*/
                        }
                        $('.pager').hide();
                    },error: function(error){
                        $('.pager').hide();
                        callback([]);
                    }
                });
            },
            "eventRender": function (event, element, view) {
                console.log('availability_type');
                console.log(event.availability_type);

                $('.fc-day[data-date="' + getEventDate(event) + '"]').addClass("hasEvent");
                if(event.availability_day_class){
                    element.addClass(event.availability_day_class);               
                }

                if(event.availability_type == 'busy'){
                    element.addClass('out-of-office');
                    element.removeClass('in-office');
                }else{
                    element.addClass('in-office');
                    element.removeClass('out-of-office');
                }
            },
            "editable": false,
            "eventLimit": true,
            "displayEventTime": false,
            "views": {
                "agenda": {
                    "eventLimit": 3,
                },
                "month": {
                    "eventLimit": 2
                }
            },
            "dayClick": function(events, element, view){
                $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
                $('[data-request="deadline"]').find('input').val('');

                var $left = events.pageX;
                var $top = events.pageY;

                if($left < 240){$left = 240;}
                if($left > 980){$left = 980;}

                var clicked_date_formatted      = get_clicked_date_formatted(events);
                var clicked_date                = get_clicked_date(events);
                var clicked_day                 = get_clicked_day(events);

                if(clicked_date_formatted){
                    $('[data-target="availability-formated-date"]').text(clicked_date_formatted);
                }

                if(clicked_date){
                    $('[data-request="availability-date"]').find('input').val(clicked_date);
                }

                if(clicked_day){
                    $('[data-target="availability-day"]').text(clicked_day);
                }

                // $("#add-availability").css( {
                //     top:(parseInt($top)/2), 
                //     left: $left,
                //     display: 'block'
                //  });

                /* CUSTOMIZING CELL AS PER DESIGN*/
                $('.fc-day').on('click', function(event){
                    $('.fc-day').removeClass('dark-red-active'); 
                    $(this).addClass('dark-red-active set-white-text');
                    $thisdate = $(this).data('date');
                    
                    $('.fc-content-skeleton td').each(function(){
                        if($(this).data('date') == $thisdate){
                            $('.fc-content-skeleton td').removeClass('active-skelton-cell');
                            $(this).addClass('active-skelton-cell');
                        }; 
                    })
                    $('.fc-content-skeleton td').removeClass('selected-cell');
                    /*$(this).parents('.fc-bg').siblings('.fc-content-skeleton').find('thead tr').children().eq(element.toElement.cellIndex).addClass('selected-cell');
                    $(this).parents('.fc-bg').siblings('.fc-content-skeleton').find('tbody tr').children().eq(element.toElement.cellIndex).addClass('selected-cell');*/

                    // var $xDist = event.pageX - 60;
                    // var $yDist = event.pageY + 50;
                    // $('.add-availability').removeClass('onRight');
                    // $('.add-availability').removeClass('onLeft');
                    // if($(window).width()/2 < $xDist) {
                    //     $("#add-availability").css( {
                    //         top: $yDist, 
                    //         left: $xDist,
                    //         display: 'block'

                    //      });

                    //     $('.add-availability').removeClass('onRight');
                    //     $('.add-availability').addClass('onLeft');
                    // }else {
                    //     $("#add-availability").css( {
                    //         top: $yDist, 
                    //         left: $xDist,
                    //         display: 'block'
                    //      });
                    //     $('.add-availability').removeClass('onLeft');
                    //     $('.add-availability').addClass('onRight');
                        
                    // }

                    // if ($(window).height()/4 < event.pageY) {
                    //     $('.add-availability').removeClass('onBottom');
                    //     $('.add-availability').addClass('onTop');
                    // }else {
                    //     $('.add-availability').removeClass('onTop');
                    //     $('.add-availability').addClass('onBottom');
                    // }

                });

                $('.greybutton-line,.close-popup-box ').on('click', function() {
                    $('.fc-day').removeClass('dark-red-active');
                    $('.fc-content-skeleton td').removeClass('selected-cell');
                     
                });
            },
        };

        $($target).fullCalendar($options);
    });

    if($('[data-request="profile-calendar"]').length > 0){
        $('[data-request="profile-calendar"]').trigger('click');
    }

    $(document).on('click','[data-request="close"]',function(){
        $(this).closest($(this).data('target')).fadeOut();
    });
});

function getEventDate(event) {
    var dateobj = event.start._d;
    date = dateobj.getFullYear()+'-'+("0" + (dateobj.getMonth()  + 1)).slice(-2)+'-'+("0" + dateobj.getDate()).slice(-2);
    return date;
}

function get_clicked_date_formatted(event) { 
    var selected_date = event._d;
    var new_date = ("0" + selected_date.getDate()).slice(-2)+' '+((((month[selected_date.getMonth()]).toUpperCase())).substring(0,3))+', '+selected_date.getFullYear();    

    return new_date;
}

function get_clicked_date(event) { 
    var selected_date = event._d;

    var new_date = ("0" + selected_date.getDate()).slice(-2)+'/'+("0" + (selected_date.getMonth()  + 1)).slice(-2)+'/'+selected_date.getFullYear();    

    return new_date;
}

function get_clicked_day(event) { 
    var selected_date = event._d;
    return weekday[selected_date.getDay()];
}


