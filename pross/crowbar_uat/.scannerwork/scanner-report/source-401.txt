<section class="vertual_events_sec community_section">
    <div class="vertual_events-wrapper clearfix">
        <div class="datatable-listing events_details grid2">
            <?php echo $html->table();; ?>

        </div>
    </div>
</section>
<?php $__env->startPush('inlinescript'); ?>

<link href="<?php echo e(asset('css/daterangepicker.css')); ?>" rel="stylesheet">
<script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
<script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
<?php echo $html->scripts(); ?>

<script type="text/javascript" src="<?php echo e(asset('js/masonry.min.js')); ?>"></script>
<script type="text/javascript">

    $(".filter-option").html('<div class="vertual_events_filter">'+
        '<ul class="navigation-group-list clearfix">'+
            '<li>'+
                '<div class="datebox-no startdate">'+
                    '<div class="input-group datepicker">'+
                        '<input type="text" id="select_event_date_for" name="select_event_date_for" class="form-control" placeholder="Select Date" data-action="filter">'+
                    '</div>'+
                '</div>'+
            '</li>'+
            '<li>'+
                '<div class="checkbox checkbox-big">                '+
                    '<input type="checkbox" id="circle" name="event_type[]" value="circle" data-action="filter">'+
                    '<label for="circle"><span class="check"></span>My circle attending</label>'+
                '</div>'+
            '</li>'+
            '<li>'+
                '<div class="checkbox checkbox-big">                '+
                    '<input type="checkbox" id="bookmark" name="event_type[]" value="bookmark" data-action="filter">'+
                    '<label for="bookmark"><span class="check"></span>Bookmarks</label>'+
                '</div>'+
            '</li>'+
        '</ul>'+
    '</div>');
    $('.post-event').html('<div>'+
                '<a href="<?php echo e(url('talent/network/post-event')); ?>" class="button">Post An Event</a>'+
            '</div>');

    // var dateFormat = "dd/mm/yy";
    // $(document).ready(function(){
    //     $("#select_event_date_for").datepicker({
    //         changeMonth: true,
    //         changeYear: true,
    //         minDate: new Date(),
    //         numberOfMonths: 1,
    //         dateFormat: dateFormat
    //     });
    // });

    // $('#select_event_date_for').change(function(){
    //     var event_date = this.value;
    //     LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function (e, settings, data) {
    //     data.event_date = event_date;
    //     }); 
    //     window.LaravelDataTables.dataTableBuilder.draw();
    // });

    function changeLayout2() {
        $grid2.masonry('layout');
    }
        
    var $grid2 = $('.grid2').masonry({
        columnWidth: '.grid-item2',
        itemSelector: '.grid-item2',
        percentPosition: true
    });

    $(document).on('click','[data-request="add-rsvp"]',function(){
        var $this           = $(this);
        var $url            = $this.data('url');
        var data_id         = $this.data('data_id');
        var toremove        = $this.data('toremove');
        var ask             = $this.data('ask');
        swal({
            title: '',
            text: ask,
            showLoaderOnConfirm: true,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            customClass: 'swal-custom-class',
            confirmButtonText: $confirm_botton_text,
            cancelButtonText: $cancel_botton_text,
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                        $.ajax({
                            url         : $url,
                            type        : 'get',
                            dataType    : 'json',
                            success:function(response){
                                if(response['status'] == false){
                                    swal({
                                        title: 'Notification',
                                        html: response['message'],
                                        showLoaderOnConfirm: false,
                                        showCancelButton: false,
                                        showCloseButton: false,
                                        allowEscapeKey: false,
                                        allowOutsideClick:false,
                                        customClass: 'swal-custom-class',
                                        confirmButtonText: $close_botton_text,
                                        cancelButtonText: $cancel_botton_text,
                                        preConfirm: function (res) {
                                            return new Promise(function (resolve, reject) {
                                                if (res === true) {
                                                    resolve();
                                                }
                                            })
                                        }
                                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);

                                }else{
                                    $('#'+toremove+'-'+data_id).fadeOut();
                                    setTimeout(function(){
                                        $('#'+toremove+'-'+data_id).remove();
                                    },1000);
                                    resolve()
                                }

                            }
                        })
                    }
                })
            }
        })
        .then(function(isConfirm){
            
        },function (dismiss){
            // console.log(dismiss);
        })
        .catch(swal.noop);
    });

    $(document).on('click','[data-request="favorite-event"]',function(){
        $('#popup').show(); 
        var $this   = $(this);
        var $url    = $this.data('url');

        $.ajax({
            url: $url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            type: 'get',
            success: function($response){
                $('#popup').hide();
                if($this.hasClass('active')){
                    $this.removeClass('active');
                }else{
                    $this.addClass('active');
                }
            },error: function(error){
                $('#popup').hide();
            }
        }); 

    });

    $('input[name*="event_type"]').click(function() {
        var checkbox_val = [];
        var i = 0;
        $('input[name*="event_type"]:checked').each(function() {
            checkbox_val[i++] = $(this).val();
        });
        LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function (e, settings, data) {
            data.check = checkbox_val;
        }); 
        window.LaravelDataTables.dataTableBuilder.draw();
    });

    $(document).on('click','[data-request="invite-member"]',function(e){
        $('#popup').show();
        var $this       = $(this);
        var $target     = $this.data('target'); 
        var $url        = $this.data('url');

        $.ajax({
            url: $url, 
            type: 'get', 
            success: function($response){
                $('#popup').hide();

                if ($response.status === true) {
                    if(!$response.nomessage){
                        swal({
                            title: $alert_message_text,
                            html: $response.message,
                            showLoaderOnConfirm: false,
                            showCancelButton: false,
                            showCloseButton: false,
                            allowEscapeKey: false,
                            allowOutsideClick:false,
                            customClass: 'swal-custom-class',
                            confirmButtonText: $close_botton_text,
                            cancelButtonText: $cancel_botton_text,
                            preConfirm: function (res) {
                                return new Promise(function (resolve, reject) {
                                    if (res === true) {
                                        if($response.redirect){
                                            window.location = $response.redirect;
                                        }              
                                    }
                                    resolve();
                                })
                            }
                        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);

                    }

                }

            },error: function(error){
                
            }
        }); 
    });

</script>
<script type="text/javascript">

        setTimeout(function(){
            $(window).on('load', function() {
                function changeLayout() {
                $grid.masonry('layout');

            }
            var $grid = $('.vertual_events-wrapper .datatable-listing .table tbody').masonry({
                columnWidth: '.vertual_events-wrapper .datatable-listing .table tbody tr',
                itemSelector: '.vertual_events-wrapper .datatable-listing .table tbody tr',
                percentPosition: true
            });
            });
        }, 500);
        
        setTimeout(function(){
            $(window).on('load resize', function() {
                function changeLayout() {
                $grid.masonry('layout');

            }
            
            var $grid = $('.vertual_events-wrapper .datatable-listing .table tbody').masonry({
                columnWidth: '.vertual_events-wrapper .datatable-listing .table tbody tr',
                itemSelector: '.vertual_events-wrapper .datatable-listing .table tbody tr',
                percentPosition: true
            });
            });
        },500);
</script>
<script src="<?php echo e(asset('js/daterangepicker.js')); ?>" type="text/javascript"></script> 
<script type="text/javascript">
    $('#select_event_date_for').daterangepicker();
    $('#select_event_date_for').on('apply.daterangepicker', function(ev, picker) {
        var event_date = $('#select_event_date_for').val();

        LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function (e, settings, data) {
        data.event_date = event_date;
        }); 
        window.LaravelDataTables.dataTableBuilder.draw();


    });
</script>
<?php $__env->stopPush(); ?>