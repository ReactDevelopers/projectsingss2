$(function(){
	$(document).on('click','[data-request="profile-calendar"]',function(){
		$('.pager').show();

		var $this 	= $(this);
		var $url 	= $this.data('url');
        var $user_id= $this.data('user_id');
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
                    url: $url + '?date='+selected_date+'&id_user='+$user_id,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'get',
                    success: function($response){
                        if($response.status === true){
                            callback($response.data);
                        }else{
                            console.log($response.data);
                        }
                        $('.pager').hide();
                    },error: function(error){
                        $('.pager').hide();
                        callback([]);
                    }
                });
            },
            "eventRender": function (event, element, view) {
                $('.fc-day[data-date="' + getEventDate(event) + '"]').addClass("hasEvent");
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

                var $left = event.pageX;
                var $top = event.pageY;

                if($left < 240){$left = 240;}
                if($left > 980){$left = 980;}

                var clicked_date = get_day_clicked_date(events);

                if(clicked_date){
                    $('[data-request="availability-date"]').find('input').val(clicked_date);
                }

                $("#add-availability").css( {top:(parseInt($top)/2), left: $left, display: 'block'});
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

function get_day_clicked_date(event) {
    var selected_date = event._d;
    var today = new Date();

    if(selected_date > today){
        return ("0" + selected_date.getDate()).slice(-2)+'/'+("0" + (selected_date.getMonth()  + 1)).slice(-2)+'/'+selected_date.getFullYear();
    }else{
        return false;
    }
}
