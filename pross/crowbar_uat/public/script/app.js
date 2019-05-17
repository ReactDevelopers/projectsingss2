var $edit_availability = false;
var $city_filter = '';
/*if($is_mobile_device == 'no'){
    if(typeof Notification != 'undefined'){
        if (Notification.permission !== "granted"){
            Notification.requestPermission();
        }
    }
}*/

$(function(){
    if($('[data-toggle="tooltip"]').length > 0){
        $('[data-toggle="tooltip"]').tooltip();  
    }
    
    setTimeout(function(){
        if($('.dataTables_empty').length > 0){
            $('.dataTables_empty').text('No record(s) found.');
        }
    },500);

    setTimeout(function(){
        if($('.dataTables_empty').length > 0){
            $('.dataTables_empty').text('No record(s) found.');
        }
    },2000);
    
    $(document).on('focus','.datepicker input[type="text"]',function(e){
        $(this).next().trigger('click'); 
    });

    $(document).on('click','[data-request="focus-input"]',function(e){
        var $this   = $(this);
        if(e.target.type == 'text'){
            var $value = ($this.closest('ul').find('[type="text"]').eq(0).val() || $this.closest('ul').find('[type="text"]').eq(1).val() || $this.closest('ul').find('[type="text"]').eq(2).val());
            $this.closest('ul').find('[type="text"]').val(''); 
            $this.closest('li').find('[type="radio"]').prop('checked',true); 
            $this.closest('li').find('[type="text"]').val($value); 
        }else if(e.target.type == 'radio'){
            if($this.prop('checked')){
                var $value = ($this.closest('ul').find('[type="text"]').eq(0).val() || $this.closest('ul').find('[type="text"]').eq(1).val() || $this.closest('ul').find('[type="text"]').eq(2).val());
                $this.closest('ul').find('[type="text"]').val(''); 
                $this.closest('li').find('[type="text"]').trigger('focus'); 
                $this.closest('li').find('[type="text"]').val($value); 
            }else{
                $this.closest('li').find('[type="text"]').val(''); 
            }
        }
    });


    $(document).on('keyup','[data-allow="number"]',function(e){
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $(document).on('paste drop drag','[data-request="live-length"]',function(e){
        e.preventDefault();
        return false;
    });
    
    $(document).on('keyup','[data-request="live-length"]',function(e){
        var $this       = $(this);
        var $maxlength  = $this.data('maxlength');
        var $length     = $this.val().split(' ').length;

        if($('.word-count').length < 1){
            $this.after('<span class="word-count">'+($maxlength-$length)+' '+$words_text+'</span>');
        }else{
            $this.next('.word-count').text(($maxlength-$length)+' '+$words_text);
        }

        if($length > ($maxlength-1)){
            $this.attr('maxlength',$this.val().length);
        }else{
            $this.removeAttr('maxlength');
        }
    });

    $('[data-request="display-popup-on-post"]').click(function(){
        $('#popup').show();    
    });

    $(document).on('click','[data-request="focus-input-checkbox"]',function(e){
        var $this   = $(this);
        if(e.target.type == 'text'){
            $this.closest('li').find('[type="checkbox"]').prop('checked',true); 
        }else if(e.target.type == 'checkbox'){
            if($this.prop('checked')){
                $this.closest('li').find('[type="text"]').trigger('focus'); 
            }else{
                $this.closest('li').find('[type="text"]').val(''); 
            }
        }
    });

    $(document).on('doubleclick','[data-request="job-actions"]',function(e){
        e.stopPropagation();
        var $this   = $(this);
        var $url    = $(this).data('url');

        $.ajax({
            url: $url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'get', 
            success: function($response){
                if($response.status === true){
                    $this.html($response.data.html);
                }else{
                    $this.html($response.message);
                }
            }
        });
    });

    if($('[data-request="job-actions"]').length > 0){
        $('[data-request="job-actions"]').trigger('doubleclick');
    }

    $(document).on('click','[data-request="favorite"]',function(){
        $('#popup').show();        
        var _this = $(this);

        $.ajax({
            url: $(this).data('url'), 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'get', 
            success: function(response){
                if(response.status === true){
                    _this.html(response.data.html);
                }
                $('#popup').hide();
            },error: function(error){
                $('#popup').hide();
            }
        }); 
    });

    $(document).on('click','[data-request="chat-initiate"]',function(){
        var $this           = $(this);
        var $url            = $this.data('url');
        var $user           = $this.data('user');

        /* SAVING CURRENT CHAT WINDOW */
        writeCookie('current_chat_window',$user);

        window.location = $url;
    });

    $(document).on('click','[data-request="send-chat-request"]',function(){
        $('.popup').show();
        var $this           = $(this);
        var $url            = $this.data('url');
        var $target         = $this.data('target');
        var $sender         = $this.data('sender');
        var $receiver       = $this.data('receiver');
        var $project_id     = $this.data('project');

        $.ajax({
            url: $url+'?sender='+$sender+'&receiver='+$receiver+'&project_id='+$project_id+'&html=true',
            type: 'get',
            success: function($response) {
                $('.popup').hide();
                $($target).html($response.data.html);
            }
        });
    });

    $(document).on('keyup focus','[data-request="city-filter"]',function(e){
        var $this           = $(this);
        var $url            = $this.data('url');
        var $target         = $this.data('target');
        
        console.log(e.type);
        if(e.type == 'focus'){
            var $page = 1;
        }else{
            var $page           = $this.attr('data-page');
        }

        var $selected_city  = readCookie('city-filter');
        if(!$selected_city){
            $selected_city = '';
        }
        
        if($target){
            var $handle = $($target);
        }

        $handle.parent().append('<img class="option-loader" src="'+asset_url+'/images/loader.gif">');
        
        $city_filter = $.ajax({
            url: $url+'?search='+$this.val()+'&selected='+$selected_city+'&page='+$page,
            type: 'get',
            beforeSend: function(){
               if($city_filter){
                   $city_filter.abort();
               }
            },
            success: function($response) {
                if($handle.find('.mCSB_container').length > 0){
                    $handle.find('.mCSB_container').html($response);
                    $this.attr('data-page',(parseInt($page)+1));
                }else{
                    $handle.html($response);
                    $this.attr('data-page',(parseInt($page)+1));
                }
                
                if($('[data-request="custom-scrollbar"]').length > 0){
                    setTimeout(function(){
                        $('[data-request="custom-scrollbar"]').mCustomScrollbar('update');
                    },10);
                }

                $('.option-loader').remove();
            }
        });
    });

    if($('[data-request="city-filter"]').length > 0){
        $('[data-request="city-filter"]').trigger('focus');
    }

    $(document).on('change','[data-request="save-city-filter"]', function(){
        if($(this).prop('checked') === true){
            if(readCookie('city-filter')){
                if(readCookie('city-filter').includes($(this).val())){

                }else{
                    writeCookie('city-filter',readCookie('city-filter')+','+$(this).val());
                }
            }else{
                writeCookie('city-filter',$(this).val());
            }
        }else{
            if(readCookie('city-filter').includes($(this).val())){
                var city_filters = readCookie('city-filter').split(",");
                var filter_index = city_filters.indexOf($(this).val());

                if (filter_index > -1) {
                    city_filters.splice(filter_index, 1);
                }
                console.log(city_filters);
                writeCookie('city-filter',city_filters.join(","));
            }
        }
    });    

    $(document).on('change','[data-request="option"]',function(){
        var $this   = $(this);
        var $url    = $this.data('url');
        var $target = $this.data('target');

        if($target){
            var $handle = $($target).find('select');
        }else{
            var $handle = $this.closest('.form-group').next().find('select');
        }

        $handle.parent().append('<img class="option-loader" src="'+asset_url+'/images/loader.gif">');

        $.ajax({
            url: $url+'?record_id='+$this.val(),
            type: 'get',
            success: function($response) {
                $handle.html($response);
                $handle.trigger('change');
                $('.option-loader').remove();
            }
        });
    });

    $(document).on('click','[data-request="trigger-proposal"]', function(){
        var $this              = $(this);
        var $target            = $this.data('target');
        var $source            = $this.data('copy-source');
        var $destination       = $this.data('copy-destination');
        var fields = [];
        
        $($source).each(function(i){
            fields[i] = $(this).val();
        });

        $($destination).val(fields.join(','));

        $($target).trigger('click');
    });

    $(document).on('click','[data-request="remove-local-document"]', function(){
        var $this               = $(this);
        var $target             = $this.data('target');
        var $source             = $this.data('source');
        var $destination        = $this.data('destination');
        var fields              = [];

        if($($destination).val()){
            var fields          = ($($destination).val()).split(",");
        }

        fields[(fields.length)] = $($target).find($source).val();
        $($destination).val(fields.join(','));

        $($target).fadeOut();
        $($target).remove(); 
    });

    $(document).on('keypress keyup','[data-request="numeric"]', function(event){
        if(event.which == 8){

        } else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });    


    $(document).on('click','[data-request="scroll"]', function(){
        if($(this).data('section')){
            $('html, body').animate({
                scrollTop: ($('#'+$(this).data('section')).offset().top-50)
            }, 500);            
        }
    });

    if($('[data-request="scroll"]').length > 0){
        $('[data-request="scroll"]').trigger('click');
    }

    // $('[data-request="tags"]').select2({
    //     'tags': true,
    //     insertTag: function(data, tag) {
    //         var $found = false;
    //         $.each(data, function(index, value) {
    //             if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
    //                 $found = true;
    //             }
    //         });

    //         if(!$found) data.unshift(tag);
    //     }
    // }).on('change', function(e){
    //     setTimeout(function(){
    //         $(e.target).next().find('[type="search"]').attr('placeholder',$(e.target).data('placeholder'));
    //     },100);
    // });

    $('select').select2({
        placeholder: function(){
            $(this).find('option[value!=""]:first').html();
        }
    });

    $(document).on('change','[data-request="calendar"]',function(){
        if($edit_availability === true){
            return false;
        }

        $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();

        var $this       = $(this);
        var $url        = $this.data('url');
        var $year       = $('[name="year"]');
        var $month      = $('[name="month"]:checked');
        var $day        = $('[name="day"]:checked');

        $.ajax({
            url: $url, 
            type: 'post', 
            data: {
                'year': $year.val(),
                'month': $month.val(),
                'day': $day.val(),
            }, 
            success: function($response){
                if($response.status === false){
                    if($this.get(0).tagName === 'INPUT'){
                        $this.parent().removeClass('active');
                        $this.prop('checked',false);
                    }else{
                        $this.val("").trigger('change');
                        $('[name="month"]').prop('checked',false);
                        $('[name="month"]').parent().removeClass('active');
                        $('[name="day"]').prop('checked',false);
                        $('[name="day"]').parent().removeClass('active');
                    }
                    $this.closest('.form-group').parent().append('<div class="help-block">'+$response.message+'</div>');
                    $this.closest('.form-group').parent().addClass('has-error');
                    $this.closest('.form-group').addClass('has-error');
                }else{
                    $('.selected-date').html($response.data.selected_date);
                    $('.date-section').html($response.data.dates_html);
                }
            },error: function($error){
                  
            }
        }); 
    });

    $(document).on('click','[data-request="toggle-hide-show"]',function(){
        var $this               = $(this);
        var $target             = $this.data('target');
        var $action             = $this.attr('data-action');
        
        $('.select2').css({'width':'100%'});

        if($action == 'show'){
            $($target).show();
            $this.hide();
            $this.attr('data-action','hide');
        }else{
            $($target).hide();
            $this.show();
            $this.attr('data-action','show');
        }
    });

    $(document).on('change','[data-request="show-hide"]',function(){
        var $this               = $(this);
        var $condition          = $this.data('condition');
        var $target             = $this.data('target');
        var $true_condition     = $this.data('true-condition');
        var $false_condition    = $this.data('false-condition');
        
        if($(this).val() == $condition){
            $($true_condition).show();
            $($false_condition).hide();
            
            $($false_condition).find('input').attr('disabled','disabled');
            $($false_condition).find('select').attr('disabled','disabled');

            $($true_condition).find('input').removeAttr('disabled');
            $($true_condition).find('select').removeAttr('disabled');
        }else{
            $($true_condition).hide();
            $($false_condition).show();
            $($true_condition).find('input').attr('disabled','disabled');
            $($true_condition).find('select').attr('disabled','disabled');

            $($false_condition).find('input').removeAttr('disabled');
            $($false_condition).find('select').removeAttr('disabled');
        }

        if($('.employment_type_postfix').length){
            $('.employment_type_postfix').html('');
            if($this.data('tag').trim()){
                $('.employment_type_postfix').html('<small>('+($this.data('tag')).trim()+')</small>');
            }
        }

        $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        $('.select2').css({'width':'100%'});
    });

    if($('[data-request="show-hide"]').length > 0){
        $('[data-request="show-hide"]:checked').trigger('change');
    }

    $(document).on('change','[data-request="show-hide-multiple"]',function(){
        var $this               = $(this);
        var $condition          = $this.data('condition');
        var $target             = $this.data('target');
        var $true_condition     = $this.data('true-condition');
        var $false_condition    = $this.data('false-condition');
        var $one                = []; 
        var $two                = []; 
        var $index              = 0;
        
        $('[data-request="show-hide-multiple"]:checked').each(function(){
            if($(this).val() == $condition){
                $one[$index++] = $(this).val();
            }
        });

        var $index              = 0;
        $('[data-request="show-hide-multiple"]:checked').each(function(){
            if($(this).val() !== $condition){
                $two[$index++] = $(this).val();
            }
        });

        if($one.length !== 0){
            $($true_condition).show();
            $($true_condition).find('input').removeAttr('disabled');
            $($true_condition).find('select').removeAttr('disabled');
        }else{
            $($true_condition).hide();
            $($true_condition).find('input').attr('disabled','disabled');
            $($true_condition).find('select').attr('disabled','disabled');
        }

        if($two.length !== 0){
            $($false_condition).show();
            $($false_condition).find('input').removeAttr('disabled');
            $($false_condition).find('select').removeAttr('disabled');
        }else{
            $($false_condition).hide();
            $($false_condition).find('input').attr('disabled','disabled');
            $($false_condition).find('select').attr('disabled','disabled');
        }

        if($('.employment_type_postfix').length){
            $('.employment_type_postfix').html('');
            if($this.data('tag').trim()){
                $('.employment_type_postfix').html('<small>('+($this.data('tag')).trim()+')</small>');
            }
        }

        $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        $('.select2').css({'width':'100%'});
    });

    if($('[data-request="show-hide-multiple"]').length > 0){
        $('[data-request="show-hide-multiple"]').trigger('change');
    }
    
    $(document).on('click','[data-request="custom-tags"]',function(){
        var $this   = $(this);
        var $name   = $this.data('name');
        var $target = $this.data('target');
        var $source = $this.data('source');
        var $tag    = $($source).val();
        
        if($.trim($tag)){
            $($target).append('<a class="custom-tags-box-item">'+
                $.trim($tag)+
                '<img data-request="custom-tags-remove" src="'+asset_url+'/images/close-icon.png" alt="x">'+
                '<input name="'+$name+'[]" type="hidden" value="'+$.trim($tag)+'">'+
                '</a>'
            );

            $($source).val('');
        }
    });

    $(document).on('click','[data-request="custom-tags-remove"]',function(){
        $(this).parent().remove();
    });

    $(document).ready(function(){
        var $this   = $('[data-request="custom-tags"]');
        var $name   = $this.data('name');
        var $target = $this.data('target');
        var $tags   = $this.data('tags');
        
        $.each($tags, function( $index, $value ) {
            $($target).append('<a class="custom-tags-box-item">'+
                $.trim($value)+
                '<img data-request="custom-tags-remove" src="'+asset_url+'/images/close-icon.png" alt="x">'+
                '<input name="'+$name+'[]" type="hidden" value="'+$.trim($value)+'">'+
                '</a>'
            );
        }); 
    });

    $('[data-request="tags"]').select2({
        multiple: true,
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);
    }).trigger('change');

    $('[data-request="tagssearch"]').select2({
        minimumInputLength: 3,
        multiple: true,
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);
    }).trigger('change');

    $('[data-request="temp-tags"]').select2({
        multiple: true,
        maximumSelectionLength: 3,
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);

    }).trigger('change');

    $('[data-request="single-tags"]').select2({
        multiple: false
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');
        if($(this).val()){

            var $list = $('<ul>');
            $selected.each(function(k, v) {
                var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
                $li.children('a.destroy-tag-selected')
                .off('click.select2-copy')
                .on('click.select2-copy', function(e) {
                    var $opt = $(this).data('select2-opt');
                    $opt.attr('selected', false);
                    $opt.parents('select').trigger('change');
                }).data('select2-opt', $(v));
                $list.append($li);
            });
            $container.html('').append($list);
        }else{
            $container.html('');
        }
    }).trigger('change');

    $('[data-request="tag"]').select2({
        tags: true
    });

    $('[data-request="tags-true"]').select2({
        multiple: true,
        tags: true,
        maximumInputLength: 50,
        insertTag: function(data, tag) {
            if(strip_html_tags(tag.text) !== tag.text){
                return false;
            }

            if(!(/^[a-zA-Z( )&/-]+$/.test(tag.text))){
                return false;
            }

            var $found = false;
            $.each(data, function(index, value) {
                if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
                    $found = true;
                }
            });

            if(!$found) data.unshift(tag);   
        }
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);
    }).trigger('change');

    $('[data-request="email-tags"]').select2({
        multiple: true,
        tags: true,
        maximumInputLength: 50,
        insertTag: function(data, tag) {
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            if((reg.test(tag.text)) == false){
                return false;
            }

            var $found = false;
            $.each(data, function(index, value) {
                if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
                    $found = true;
                }
            });

            if(!$found) data.unshift(tag);   
        },
        language: {
            noResults: function (params) {
                return $valid_email_note;
            }
        }
    }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);
    }).trigger('change');    


    $(document).on('click','[data-request="trigger"]',function(){
        var target = $(this).data('target');
        $(target).trigger('click');
    });

    $(document).on('click','[data-request="alert"]',function(){
        var $this = $(this);
        var $title = $this.data('title');
        var $message = $this.data('message');

        swal({
            title: $title,
            html: $message,
            showLoaderOnConfirm: false,
            showCancelButton: false,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            customClass: 'swal-custom-class',
            confirmButtonText: $close_botton_text,
            cancelButtonText: $no_thanks_botton_text,
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                        resolve();              
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);

    });
    
    $(document).on('click','[data-request="confirm-ajax"]',function(){
        var $this       = $(this);
        var $title      = $this.data('title');
        var $ask        = $this.data('ask');
        var $url        = $this.data('url');
        var $receiver   = $this.data('receiver');
        var $sender     = $this.data('sender');

        swal({
            title: $title,
            html: $ask,
            showLoaderOnConfirm: false,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            customClass: 'swal-custom-class',
            confirmButtonText: $confirm_botton_text,
            cancelButtonText: $no_thanks_botton_text,
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                        $('#popup').show(); 
                        $.ajax({
                            url: $url, 
                            cache: false, 
                            contentType: false, 
                            processData: false, 
                            type: 'get', 
                            success: function($response){
                                $($this.data('target')).html($response.message);
                                $('#popup').hide();
                                
                                if($response.redirect){
                                    window.location = $response.redirect;
                                }

                                if($response.status === false){
                                    swal({
                                        title: '',
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
                                                    resolve();              
                                                }
                                            })
                                        }
                                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                                }

                                if($receiver){
                                    if(typeof socket != 'undefined'){
                                        socket.emit('send.notification.action',$receiver);
                                    }
                                }

                                if($sender){
                                    if(typeof socket != 'undefined'){
                                        socket.emit('send.notification.action',$sender);
                                    }
                                }
                            },error: function(error){
                                $('#popup').hide();
                            }
                        }); 
                        resolve();              
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
    });

    $(document).on('click','[data-request="inline-ajax"]',function(){
        $('#popup').show(); var $this = $(this);

        var $message_title = "Notification";

        var $user       = $this.data('user');
        var $receiver   = $this.data('receiver');
        var $sender     = $this.data('sender');

        /* SAVING CURRENT CHAT WINDOW */
        if($user){
            writeCookie('current_chat_window',$user);
        }

        var $url = $(this).data('url');

        $.ajax({
            url: $url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            type: 'get',
            success: function($response){

                $($this.data('target')).html($response.message);
                $('#popup').hide();
                
                if($response.redirect){
                    setTimeout(function(){
                        window.location = $response.redirect;
                    }, 1000);
                }

                if($response.head_message !=''){
                    $message_title = $response.head_message;
                }

                if(typeof $message_title === "undefined"){
                    $message_title = "Notification";
                }

                if($response.status === false){
                    swal({
                        title: $message_title,
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
                                    if(typeof LaravelDataTables !== 'undefined'){
                                        LaravelDataTables["dataTableBuilder"].draw();
                                    }
                                    resolve();              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }

                if($response.status === true){
                    if(typeof LaravelDataTables !== 'undefined'){
                        LaravelDataTables["dataTableBuilder"].draw();
                    }
                }

                if($receiver){
                    if(typeof socket != 'undefined'){
                        console.log("EMIT RECEIVER");
                        socket.emit('send.notification.action',$receiver);
                    }
                }
                if($sender){
                    if(typeof socket != 'undefined'){
                        console.log("EMIT SENDER");
                        socket.emit('send.notification.action',$sender);
                    }
                }
            },error: function(error){
                $('#popup').hide();
            }
        }); 

    });

    $(document).on('click','[data-request="inline-ajax-2"]',function(){
        $('#popup').show(); var $this = $(this);

        var $url = $(this).data('url');

        $.ajax({
            url: $url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            type: 'post',
            success: function($response){

                $($this.data('target')).html($response.message);
                $('#popup').hide();

                if($response.status == true){

                    swal({
                        title: 'Success',
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
                                    if($response.redirect_url){
                                        window.location = $response.redirect_url;
                                    }              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);

                }


            },error: function(error){
                $('#popup').hide();
            }
        }); 

    });

    $(document).on('click','[data-request="mark-read"]',function(){
        var $this = $(this);
        var $user       = $this.data('user');
        var $receiver   = $this.data('receiver');
        var $sender     = $this.data('sender');
        var $url        = $this.data('url');
        var $confirm    = $this.data('confirm');
        var $ask        = $this.data('ask');

        /* SAVING CURRENT CHAT WINDOW */
        if($user){
            writeCookie('current_chat_window',$user);
        }

        if($confirm){
            swal({
                title: $alert_message_text,
                html: $ask,
                showLoaderOnConfirm: false,
                showCancelButton: true,
                showCloseButton: false,
                allowEscapeKey: false,
                allowOutsideClick:false,
                customClass: 'swal-custom-class',
                confirmButtonText: $proposal_botton_text,
                cancelButtonText: $no_thanks_botton_text,
                preConfirm: function (res) {
                    return new Promise(function (resolve, reject) {
                        if (res === true) {
                            $.ajax({
                                url: $url, 
                                cache: false, 
                                contentType: false, 
                                processData: false, 
                                type: 'get',
                                success: function($response){
                                    if($response.redirect){
                                        window.location = $response.redirect;
                                    }
                                },error: function(error){
                                    $('#popup').hide();
                                }
                            }); 
                            resolve();              
                        }
                    })
                }
            }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
        }else{
            $.ajax({
                url: $url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                type: 'get',
                success: function($response){
                    if($response.redirect){
                        window.location = $response.redirect;
                    }
                },error: function(error){
                    $('#popup').hide();
                }
            });     
        }
    });

    $(document).on('click','[data-request="favorite-save"]',function(){
        $('#popup').show(); 
        var $this = $(this);
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

                /*Show sweet alert*/
                if($response.head_message !=''){
                    swal({
                        title: 'Message',
                        html: $response.head_message,
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
                }


            },error: function(error){
                $('#popup').hide();
            }
        }); 

    });

    $(document).on('click','[data-request="ajax-submit"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);

        var $alert_message_text = 'Notification';
        
        if(!$method){ $method = 'get'; }
        
        $.ajax({ 
            url: $url, 
            data: $data,
            cache: false, 
            type: $method, 
            dataType: 'json',
            contentType: false, 
            processData: false,
            success: function($response){
                if($response.message == '2popup'){
                    $('#temp-me').modal('hide');
                    $('#temp-me-2').modal('show');
                    setTimeout(function(){ 
                        location.reload(); 
                    }, 1800);
                }

                if($response.head_message != ''){
                    $alert_message_text = $response.head_message; 
                }

                if(typeof $alert_message_text === "undefined"){
                    $alert_message_text = "Notification";
                }

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
                    }else{
                        if($response.redirect){
                            window.location = $response.redirect;
                        }
                    }

                    /*UPDATE TARGET IF RENDER IS AVAILABLE*/
                    if($response.data.render === true){
                        $($response.data.target).html($response.data.html);
                        $($response.data.clear.target).val($response.data.clear.value);
                    }

                    /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                    if($response.data){
                        $.each($response.data, function(key,value) {
                            $("[name='"+key+"']").val(value);
                        });
                    }
                    
                    /*USELESS FOR NOW*/
                    if($response.show){
                        $.each($response.show, function(key,value) {
                            $(value).show();
                        });
                    }
                }else{
                    if($response.message.length > 0 && $response.message !== 'M0000'){
                        $('.messages').html($response.message);
                    }

                    if (Object.size($response.data) > 0) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $('#popup').hide();
            }
        }); 
    });

    $(document).on('click','[data-request="paypal-ajax-submit"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);
        
        if(!$method){ $method = 'get'; }
        
        $.ajax({ 
            url: $url, 
            data: $data,
            cache: false, 
            type: $method, 
            dataType: 'json',
            contentType: false, 
            processData: false,
            success: function($response){
                if ($response.status === true) {

                    $("#paypal_url").attr("href", $response.data.href+'&displayMode=minibrowser');
                    $("#paypal_url").trigger('click');
                    $("#paypal_url").show();

                }else{
                    if($response.message.length > 0 && $response.message !== 'M0000'){
                        $('.messages').html($response.message);
                    }

                    if (Object.size($response.data) > 0) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $('#popup').hide();
            }
        }); 
    });



    $(document).on('click','[data-request="confirm-ajax-submit"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);
        var $ask        = $this.data('ask');
        var $title      = $this.data('title');
        
        if(!$method){ 
            $method = 'get'; 
        }
        
        // swal({
        //     title: $title,
        //     html: $ask,
        //     showLoaderOnConfirm: false,
        //     showCancelButton: true,
        //     showCloseButton: false,
        //     allowEscapeKey: false,
        //     allowOutsideClick:false,
        //     customClass: 'swal-custom-class',
        //     confirmButtonText: $confirm_botton_text,
        //     cancelButtonText: $cancel_botton_text,
        //     preConfirm: function (res) {
        //         return new Promise(function (resolve, reject) {
        //             if (res === true) {
                        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
                        $.ajax({ 
                            url: $url, 
                            data: $data,
                            cache: false, 
                            type: $method, 
                            dataType: 'json',
                            contentType: false, 
                            processData: false,
                            success: function($response){
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
                                    }else{
                                        if($response.redirect){
                                            window.location = $response.redirect;
                                        }
                                    }

                                    /*UPDATE TARGET IF RENDER IS AVAILABLE*/
                                    if($response.data.render === true){
                                        $($response.data.target).html($response.data.html);
                                        $($response.data.clear.target).val($response.data.clear.value);
                                    }

                                    /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                                    if($response.data){
                                        $.each($response.data, function(key,value) {
                                            $("[name='"+key+"']").val(value);
                                        });
                                    }
                                    
                                    /*USELESS FOR NOW*/
                                    if($response.show){
                                        $.each($response.show, function(key,value) {
                                            $(value).show();
                                        });
                                    }
                                }else{
                                    if($response.message.length > 0 && $response.message !== 'M0000'){
                                        $('.messages').html($response.message);
                                    }

                                    if (Object.size($response.data) > 0) {
                                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                                        show_validation_error($response.data);
                                    }
                                }
                                $('#popup').hide();
                            }
                        }); 
                        // resolve();              
            //         }
            //     })
            // }
        // }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
    });


    $(document).on('click','[data-request="confirm-ajax-submit2"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);
        var $ask        = $this.data('ask');
        var $title      = $this.data('title');
        
        if(!$method){ 
            $method = 'get'; 
        }
        
        swal({
            title: $title,
            html: $ask,
            showLoaderOnConfirm: false,
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
                        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
                        $.ajax({ 
                            url: $url, 
                            data: $data,
                            cache: false, 
                            type: $method, 
                            dataType: 'json',
                            contentType: false, 
                            processData: false,
                            success: function($response){
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
                                    }else{
                                        if($response.redirect){
                                            window.location = $response.redirect;
                                        }
                                    }

                                    /*UPDATE TARGET IF RENDER IS AVAILABLE*/
                                    if($response.data.render === true){
                                        $($response.data.target).html($response.data.html);
                                        $($response.data.clear.target).val($response.data.clear.value);
                                    }

                                    /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                                    if($response.data){
                                        $.each($response.data, function(key,value) {
                                            $("[name='"+key+"']").val(value);
                                        });
                                    }
                                    
                                    /*USELESS FOR NOW*/
                                    if($response.show){
                                        $.each($response.show, function(key,value) {
                                            $(value).show();
                                        });
                                    }
                                }else{
                                    if($response.message.length > 0 && $response.message !== 'M0000'){
                                        $('.messages').html($response.message);
                                    }

                                    if (Object.size($response.data) > 0) {
                                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                                        show_validation_error($response.data);
                                    }
                                }
                                $('#popup').hide();
                            }
                        }); 
                        resolve();              
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
    });

    $(document).on('click','[data-request="ajax-submit-job"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);
        
        if(!$method){ $method = 'get'; }
        
        $.ajax({ 
            url: $url, 
            data: $data,
            cache: false, 
            type: $method, 
            dataType: 'json',
            contentType: false, 
            processData: false,
            success: function($response){
                if ($response.status === true) {

                    swal({
                        title: '',
                        html: $response.message,
                        showLoaderOnConfirm: true,
                        showCancelButton: true,
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
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                    
                    /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                    if($response.data){
                        $.each($response.data, function(key,value) {
                            $("[name='"+key+"']").val(value);
                        });
                    }
                    
                    /*USELESS FOR NOW*/
                    if($response.show){
                        $.each($response.show, function(key,value) {
                            $(value).show();
                        });
                    }
                }else{
                    if ($response.data) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $('#popup').hide();
            }
        }); 
    });

    $(document).on('click','[data-request="multi-ajax"]',function(){
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this           = $(this);
        var $target         = $this.data('target');
        var $url            = $($target).attr('action');
        var $method         = $($target).attr('method');
        var $data           = new FormData($($target)[0]);
        var $message        = $this.data('message');
        var id_education    = $($this.data('box-id')).val();
        var toremove        = $this.data('toremove');
        
        $.ajax({
            url:$url,
            data: $data,
            type:$method,
            cache: false,
            dataType: 'json',
            contentType: false, 
            processData: false,
            success:function($response){
                if ($response.status === true) {
                    if($('[data-request="toggle-hide-show"]').length > 0){
                        $this.closest('.add-form').next().find('[data-request="toggle-hide-show"]').trigger('click');
                    }

                    setTimeout(function(){
                        if($response.redirect){
                            window.location = $response.redirect;
                        }
                    },1000);

                    if($response.data){
                        if(id_education){
                            $('#'+toremove+'-'+id_education).html($($response.data).html());
                            
                            if($response.message){ 
                                $('html, body').animate({
                                    scrollTop: ($('#'+toremove+'-'+id_education).offset().top-100)
                                }, 100);            
                            }
                            $('#'+toremove+'-'+id_education).fadeOut();
                            $('#'+toremove+'-'+id_education).fadeIn();
                        }else{
                            $($this.data('box')).prepend($response.data);
                            
                            if($response.message){ 
                                $('html, body').animate({
                                    scrollTop: ($($this.data('box')).offset().top-100)
                                }, 100);            
                            }
                        }
                        
                        $($target).find('input[type="checkbox"]').prop('checked', false);
                        $($target).find('input[type="radio"]').prop('checked', false);
                        $($target).find('input[type="text"]').val('');
                        $($target).find('input[type="password"]').val('');
                        $($target).find('input[type="date"]').val('');
                        $($target).find('input[type="time"]').val('');
                        $($target).find('input[type="hidden"]').val('');
                        $($target).find('textarea').val('');
                        $($target).find('select').val("").trigger('change');
                        $($target).find('label').removeClass('active');
                    }
                }else{
                    if($response.message && $message){
                        $($message).html($response.message);

                        $('html, body').animate({
                            scrollTop: ($($message).offset().top-100)
                        }, 100); 
                    }

                    if ($response.data) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $('#popup').hide();
            }
        });
    });

    $(document).on('click','[data-request="multi-ajax-calendar"]',function(){
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this           = $(this);
        var $target         = $this.data('target');
        var $url            = $($target).attr('action');
        var $method         = $($target).attr('method');
        var $data           = new FormData($($target)[0]);
        var $message        = $this.data('message');
        var id_education    = $($this.data('box-id')).val();
        var toremove        = $this.data('toremove');
        
        $.ajax({
            url:$url,
            data: $data,
            type:$method,
            cache: false,
            dataType: 'json',
            contentType: false, 
            processData: false,
            success:function($response){
                if ($response.status === true) {
                    swal({
                        title: '',
                        html: $this.data('success'),
                        showLoaderOnConfirm: true,
                        showCancelButton: true,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        customClass: 'swal-custom-class',
                        confirmButtonText: $close_botton_text,
                        cancelButtonText: $cancel_botton_text,
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                window.location = window.location;
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }else{
                    if($response.message && $message){
                        swal({
                            title: '',
                            html: $response.message,
                            showLoaderOnConfirm: true,
                            showCancelButton: true,
                            showCloseButton: false,
                            allowEscapeKey: false,
                            allowOutsideClick:false,
                            customClass: 'swal-custom-class',
                            confirmButtonText: $close_botton_text,
                            cancelButtonText: $cancel_botton_text
                        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                        
                    }

                    if ($response.data) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $('#popup').hide();
            }
        });
    });

    $(document).on('change','[data-request="doc-submit"]', function(){
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this = $(this);
        var $target         = $this.data('target');
        var $url            = $($target).attr('action');
        var $method         = $($target).attr('method');
        var $data           = new FormData($($target)[0]);
        var after_upload    = $this.data('after-upload');
        $.ajax({
            url  : $url,
            data : $data,
            cache : false,
            type : $method,
            dataType : 'json',
            contentType : false,
            processData : false,
            success : function($response){
                if($response.status==true){
                    if($this.data('place') == 'prepend'){
                        $($this.data('toadd')).prepend($response.data);
                    }else{
                        $($this.data('toadd')).append($response.data);
                    }
                    if($this.data('single') === true){
                        $(after_upload).hide();
                        $('#for_portfolio').hide();
                    }
                }else{
                    if ($response.data) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                $this.val('');
                $('#popup').hide();
            }
        });
    });

    $(document).on('click','[data-request="delete"]',function(){
        var $this           = $(this);
        var $url            = $this.data('url');
        var data_id         = $this.data($this.data('edit-id'));
        var toremove        = $this.data('toremove');
        var ask             = $this.data('ask');
        var after_upload    = $this.data('after-upload');
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
                            type        : 'post',
                            dataType    : 'json',
                            success:function(response){
                                $('#'+toremove+'-'+data_id).fadeOut();
                                setTimeout(function(){
                                    $('#'+toremove+'-'+data_id).remove();
                                },1000);
                                if($this.data('single') === true){
                                    $(after_upload).show();
                                    $('#for_portfolio').show();
                                }
                                if(response.reload == true){
                                    location.reload();
                                }
                                resolve()
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

    $(document).on('click','[data-request="edit"]',function(){
        var $this = $(this);
        var $url = $this.data('url');
        var id_education = $this.data($this.data('edit-id'));

        $this.closest('.addIcons').parent().parent().parent().next().fadeOut();
        $this.closest('.addIcons').parent().parent().parent().next().fadeIn();

        $('html, body').animate({
            scrollTop: ($this.closest('.addIcons').parent().parent().parent().next().offset().top-50)
        }, 100);

        if($('[data-request="toggle-hide-show"]').length > 0){
            $this.closest('.box-item-data').parent().find('[data-request="toggle-hide-show"]').trigger('click');
        }

        $.ajax({
            url     : $url,
            type    : 'post',
            dataType : 'json',
            success : function(response){
                /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                if(response.data){
                    $.each(response.data, function(key,value) {
                        var $element = $("[name='"+key+"']").prop('tagName');

                        if($element === 'INPUT'){
                            if($("[name='"+key+"']").attr('type') == 'radio'){
                                $("[name='"+key+"'][value='"+value+"']").prop('checked',true).trigger('change');
                            }else{
                                $("[name='"+key+"']").val(value);
                            }
                        }else if($element === 'SELECT'){
                            $("[name='"+key+"']").val(value).trigger('change');
                        }else if($element === 'TEXTAREA'){
                            $("[name='"+key+"']").val(value);
                        }else{
                            $("[name='"+key+"']").val(value);
                        }

                        if(key === 'state'){
                            setTimeout(function(){
                                $("[name='"+key+"']").val(value).trigger('change');
                            },1000);
                        }
                    });
                }
            }
        })
    });

    $(document).on('click','[data-request="edit-availability"]',function(){
        $edit_availability = true;
        var $this = $(this);
        var $url = $this.data('url');
        var id_education = $this.data($this.data('edit-id'));

        $this.closest('.availability-box').next().next().fadeOut();
        $this.closest('.availability-box').next().next().fadeIn();

        $('html, body').animate({
            scrollTop: ($this.closest('.availability-box').next().next().offset().top-50)
        }, 100);

        $.ajax({
            url     : $url,
            type    : 'post',
            dataType : 'json',
            success : function(response){
                $('label.active').removeClass('active');
                /*ASSIGN IF ANY DEFAULT VALUE EXISTS*/
                $('.weekly-availability-section').hide();
                if(response.data){
                    $.each(response.data, function(key,value) {
                        var $element = $("[name='"+key+"']").prop('tagName');

                        if($element === 'INPUT'){
                            if($("[name='"+key+"']").attr('type') == 'radio'){
                                $("[name='"+key+"'][value='"+value+"']").prop('checked',true);
                                $("[name='"+key+"']:checked").parent().addClass('active');

                            }else if($("[name='"+key+"']").attr('type') == 'checkbox'){

                            }else{
                                $("[name='"+key+"']").val(value);
                            }
                        }else if($element === 'SELECT'){
                            $("[name='"+key+"']").val(value).trigger('change');
                        }else if($element === 'TEXTAREA'){
                            $("[name='"+key+"']").val(value);
                        }else{
                            $("[name='"+key+"']").val(value);
                        }

                        if(key == 'selected_date'){
                            $('.selected-date').html(value);
                        }

                        if(key == 'availability_day'){
                            value.forEach(function(item) {
                                $("#availability-day-"+item).parent().addClass('active');
                                $("#availability-day-"+item).prop('checked', true);
                            });
                        }

                        if(key == 'repeat' && value == 'weekly'){
                            $('.weekly-availability-section').show();
                        }
                    });
                }
            }
        })
    });

    $(document).on('click','[data-request="ajax-modal"]',function(e){
        $('#popup').show();
        var $this       = $(this);
        var $target     = $this.data('target'); 
        var $url        = $this.data('url');

        console.log("target- "+$target);

        $.ajax({
            url: $url, 
            type: 'get', 
            success: function($response){
                $('#popup').hide();
                $($target).html($response);
                $($target).modal({show:true});
            },error: function(error){
                
            }
        }); 
    });
/*
    $(document).on('click','[data-request="ajax-modal-submit"]',function(e){
        $('#popup').show();
        var $this       = $(this);
        var $target     = $this.data('target'); 
        var $url        = $this.data('url');

        console.log("target- "+$target);

        $.ajax({
            url: $url, 
            type: 'get', 
            success: function($response){
                $('#popup').hide();
                $($target).html($response);
                $($target).modal({show:true});
            },error: function(error){
                
            }
        }); 
    });*/

    $(document).on('click','[data-request="ajax-modal-cb-invite"]',function(e){
        var search_txt = '';
        search_txt = $("[type='search']").val();

        $('#popup').show();
        var $this       = $(this);
        var $target     = $this.data('target'); 
        var $url        = $this.data('url');

        console.log("target- "+$target);

        $.ajax({
            url: $url, 
            type: 'get', 
            success: function($response){
                $('#popup').hide();
                $($target).html($response);
                $($target).modal({show:true});
                $('#invite_to_cb_email').val(search_txt);
            },error: function(error){
                
            }
        }); 
    });

    $(document).on('click','[data-request="add-member"]',function(e){
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

    $(document).on('click','[data-request="invite-to-crowbar"]',function(e){
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

    $(document).on('click','[data-request="paginate"]',function(e){
        $('.pager').show();
        
        var url         = $(this).data('url');
        var target      = $(this).data('target');
        var showing     = $(this).data('showing');
        var loadmore    = $(this).data('loadmore');
        
        $.ajax({
            url: url, 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'get', 
            success: function(data){
                $(target).append(data.data);
                $(showing).html('Showing '+(data.recordsFiltered)+' pending request(s) of total '+(data.recordsTotal)+' request(s). &nbsp;&nbsp;&nbsp;&nbsp;');
                $(loadmore).html(data.loadMore);
                $('.pager').hide();
            },error: function(error){
                $('.pager').hide();
            }
        }); 
    });

    $(document).on('click','[data-request="delete-card"]',function(){
        $('#popup').show(); var $this = $(this);

        $.ajax({
            url: $(this).data('url'), 
            cache: false, 
            contentType: false, 
            processData: false, 
            type: 'get', 
            success: function($response){
                $('#popup').hide();
                
                $this.closest('.removable-box').remove();
                $($this.data('target')).html($response.data.html);
                
                if($response.data.is_card_available === false){
                    $("[data-target='#add-cards']").trigger('click');
                }
                
                if($response.status === false){
                    swal({
                        title: 'Please stay calm..',
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
                                    resolve();              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }
            },error: function(error){
                $('#popup').hide();
            }
        }); 

    });

    $(document).on('click','[data-request="add-card"]',function(){
        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);
        if(!$method){ $method = 'get'; }
        
        $.ajax({ 
            url: $url, 
            data: $data,
            cache: false, 
            type: $method, 
            dataType: 'json',
            contentType: false, 
            processData: false,
            success: function($response){
                $('#popup').hide();

                if ($response.status === true) {
                    $($this.data('content')).html($response.data.html);
                    
                    if($this.data('callback')){
                        $($this.data('callback')).trigger('click');
                    }
                    
                    if($this.data('dismiss')){
                        $($this.data('dismiss')).modal('toggle');
                    }

                    if($response.redirect == true){
                        window.location = window.location;
                    }
                }else{
                    if($response.message.length > 0){
                        $('.messages').html($response.message);
                    }

                    if (Object.size($response.data) > 0) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error($response.data);
                    }
                }
                
            }
        }); 
    });

    $(document).on('click','[data-request="like-dislike"]',function(){
        $('#popup').show();
        $this       = $(this);
        $url        = $this.data('url');
        $value      = $this.data('value');
        $inactive   = $this.data('inactive');
        $target     = $this.data('target');
        $.ajax({
            url:$url,
            data:{response:$value},
            type: 'POST',             
            dataType: 'json',
            success : function($response){
                $this.addClass('active');
                $('.'+$inactive).removeClass('active');
                $($target).html($response.data);
                $('#popup').hide();
            }
        })
    });

    $(document).on('click','[data-request="accept-decline"]',function(){
        $('#popup').show();
        $this       = $(this);
        $url        = $this.data('url');
        $value      = $this.data('value');
        $inactive   = $this.data('inactive');
        $target     = $this.data('target');
        $.ajax({
            url:$url,
            data:{response:$value},
            type: 'POST',             
            dataType: 'json',
            success : function($response){
                if($response.status === true){
                    swal({
                        title: $response.message,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        customClass: 'swal-custom-class',
                        confirmButtonText: $close_botton_text,
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {
                                    LaravelDataTables["dataTableBuilder"].draw();
                                    resolve();              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);                     
                }else{
                    swal({
                        title: $response.message,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        customClass: 'swal-custom-class',
                        confirmButtonText: $close_botton_text,
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {
                                    resolve();              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);                    
                }
                $('#popup').hide();
            }
        })
    });

    if($('[data-request="paginate"]').length > 0){
        $('[data-request="paginate"]').trigger('click');
    }

    if($(".proposal-found-then > div").length <= 0){
        $('.not-found-then').hide();
    }


    // setTimeout(function(){
    //     if(typeof fancybox != 'undefined'){
    //         if($('.fancybox').length > 0){
    //             $('.fancybox').fancybox({
    //                 'padding'       :  0,
    //                 'title'         : false,
    //                 'type'          : 'image',
    //                 'fitToView'     : true,
    //                 'autoSize'      : true,
    //                 'helpers'       : { 'overlay' : { 'locked' : false } },
    //                 beforeLoad: function(){
    //                   $('body').addClass('no-transform');
    //                 },
    //                 afterClose: function(){
    //                   $('body').removeClass('no-transform');
    //                 }
    //             });
    //         }
    //     }
    // },3000);
    
    live_notification($userID);
    interval_id = setInterval(function(){
        live_notification($userID);
    }, 8000); 

    function live_notification($userID){
        $.ajax({
            method: "GET",
            url: base_url+'/ajax/notification/count?user_id='+$userID,
        }).success(function($response) {
            var $counter_element = '[data-target="notification-count"]';

            if($response.data){
                var notification_counter = parseInt($response.data);

                if(notification_counter < 100){
                    $($counter_element).html($response.data);
                }else{
                    $($counter_element).html('99+');
                }
                $($counter_element).show();
            } 
        });
    }

    $(document).on('click','[data-request="status-request"]',function(){
        $('.alert').remove();

        var $url            = $(this).data('url');
        var $ask             = $(this).data('ask');

        var r = confirm($ask);
        if(r == true){
            $('#popup').show();
            $.ajax({
                method: "POST",
                url: $url,
            }).done(function(response) {
                if(response.status === true){
                    LaravelDataTables["dataTableBuilder"].draw();
                }else{
                    window.location = window.location;
                }
            }).always(function() {
                $('#popup').hide();
            });
        }else{
            return false;
        }
    });

    $(document).on('click','[data-request="delete-job"]',function(){
        var $this   = $(this);
        var $url    = $this.data('url');
        var $title  = $this.data('title');
        var $ask    = $this.data('ask');
        
        swal({
            title: $title,
            html: $ask,
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
                            url:$url,
                            type:'get',
                            success:function($response){
                                if($response.status === true){
                                    window.location = $response.redirect;
                                }else{
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
                                                resolve();
                                            })
                                        }
                                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                                }
                            }
                        });
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
    });

    $('#notification-toggle').on('click',function(){
        var $this   = $(this);
        
        $.ajax({
            url:base_url+"/ajax/notification/list",
            type:'get',
            success:function($response){
                $('[data-target="notification-list"]').html($response.data);
            }
        });
    });
});

function show_validation_error(msg) {
    if ($.isPlainObject(msg)) {
        $data = msg;
    }else {
        $data = $.parseJSON(msg);
    }
    
    $.each($data, function (index, value) {
        var name    = index.replace(/\./g, '][');
        
        if (index.indexOf('.') !== -1) {
            name = name + ']';
            name = name.replace(']', '');
        }
        if (name.indexOf('[]') !== -1) {
            $('form [name="' + name + '"]').last().closest('.form-group').addClass('has-error');
            $('form [name="' + name + '"]').last().closest('.form-group').find('.message-group').append('<div class="help-block">' + value + '</div>');
        }else if($('form [name="' + name + '[]"]').length > 0){
        	$('form [name="' + name + '[]"]').closest('.form-group').addClass('has-error');
            $('form [name="' + name + '[]"]').parent().after('<div class="help-block">' + value + '</div>');
        }else{
            if($('form [name="' + name + '"]').attr('type') == 'checkbox' || $('form [name="' + name + '"]').attr('type') == 'radio'){
                if($('form [name="' + name + '"]').attr('type') == 'checkbox'){
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').parent().after('<div class="help-block">' + value + '</div>');
                }else{
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').parent().parent().append('<div class="help-block">' + value + '</div>');
                }
            }else if($('form [name="' + name + '"]').get(0)){
                if($('form [name="' + name + '"]').get(0).tagName == 'SELECT'){
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').parent().after('<div class="help-block">' + value + '</div>');
                }else if($('form [name="' + name + '"]').attr('type') == 'password' && $('form [name="' + name + '"]').hasClass('hideShowPassword-field')){
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').parent().after('<div class="help-block">' + value + '</div>');
                }
                // else if($('form [name="' + name + '"]').get(0).tagName == 'TEXTAREA'){
                //     $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                //     $('form [name="' + name + '"]').next('div').after('<div class="help-block">' + value + '</div>');
                // }
                else{
                    console.log($('form [name="' + name + '"]').get(0).tagName);
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').after('<div class="help-block">' + value + '</div>');
                }
            }else{
                $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                $('form [name="' + name + '"]').after('<div class="help-block">' + value + '</div>');
            }
        }

        // $('.help-block').html($('.help-block').text().replace(".,",". "));
    });

    /*SCROLLING TO THE INPUT BOX*/
    scroll();
}

function scroll() {
    if ($(".has-error").not('.modal .has-error').length > 0) {
        $('html, body').animate({
            scrollTop: ($(".has-error").offset().top - 100)
        }, 200);
    }
}

function strip_html_tags(str){
    if ((str===null) || (str==='')){
        return false;
    }else{
        str = str.toString();
    }
    return str.replace(/<[^>]*>/g, '');
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

(function($){
    $(window).on("load",function(){
        setTimeout(function(){
            if($('[data-request="custom-scrollbar"]').length > 0){
                $('[data-request="custom-scrollbar"]').mCustomScrollbar();
            }
        },200);
    });
})(jQuery);

$(window).on('load resize',  function(){
    $('select').css('width', "100%");
});
