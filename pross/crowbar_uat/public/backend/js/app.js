$(function(){
    $(document).on('click','[data-request="trigger"]',function(){
        var target = $(this).data('target');
        $(target).trigger('click');
    });

    $(document).on('click','[data-toggle="offcanvas"]',function(){
        $('#popup').show();
        $.ajax({
            url: $(this).data('url'), 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'get', 
            success: function(data){
                $('#popup').hide();
            },error: function(error){
                $('#popup').hide();
            }
        }); 
    });

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
            success: function(data){
                if(data.status === true){
                    _this.html(data.html);
                }
                $('#popup').hide();
            },error: function(error){
                $('#popup').hide();
            }
        }); 
    });

    $(document).on('click','[data-request="click-target-focus"]',function(){
        $('[name="'+$(this).data('target')+'"]').trigger('focus');
    });

    $(document).on('click','[data-request="filter"]',function(){
        var _this       = $(this);
        var content     = _this.data('content');

        $('[type="search"]').val(content);
        $('[type="search"]').trigger('keyup');
        
    });
    
    $(document).on('click','[data-request="confirm"]',function(){
        var _this = $(this);
        var href            = _this.data('href');
        
        $('.alert').remove();
        if(href){   
            var before              = _this.data('before');
            var after               = _this.data('after');
            var removeclass         = _this.data('removeclass');
            var addclass            = _this.data('addclass');
            var ask                 = _this.data('ask');
            var datarefresh         = _this.data('refresh');
            var allowcolumnupdate   = _this.data('allow-column-update');
            var target              = _this.data('target');
            var redirect            = _this.data('redirect');
            
            var r = confirm(ask);
            if(r == true){
                $('#popup').show();
                $.ajax({
                    method: "POST",
                    url: href,
                    data:{
                        'status':   before
                    }
                }).done(function(record) {
                    if(redirect != true){
                        if(record == '1'){   
                            var data = $.parseJSON(record);
                            
                            if(removeclass){
                                _this.removeClass(removeclass);   
                            }

                            if(addclass){
                                _this.addClass(addclass);
                            }
                            
                            if(after == 'remove-row'){
                                _this.parent().parent().remove();
                            }else if(after){
                                if(allowcolumnupdate){
                                    _this.parent().prev().html(before);
                                }

                                if(target){
                                    $(target).html(before);
                                }
                                
                                _this.html(after);
                            }

                            if(datarefresh){
                                _this.data('after',before);
                                _this.data('before',after);
                            }else{
                                _this.data('href','');
                            }
                        }else{
                            if(record){
                                $('#content-wrapper .content').prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> '+record+'</div>');
                                scrollToErrorAlert();
                            }else{
                                $('#content-wrapper .content').prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> Something wrong, please try again after some time.</div>');
                                scrollToErrorAlert();
                            }
                        }
                    }else{
                        window.location = window.location;
                    }
                }).fail(function(error) {
                    
                }).always(function() {
                    $('#popup').hide();
                });
            }else{
                return false;
            }
        }else{
            return false;
        }
    });
    
    $(document).on('click','[data-submit="ajax"]',function(e){
        $('.alert').remove();
        var formID = $(this).data('form');
        var data = new FormData($(formID)[0]);
        $('#popup').show();
        $.ajax({ 
            url: $(this).data('url'), 
            data: data, 
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
            type: 'POST', 
            success: function(data){
                $(".has-error").removeClass('has-error');
                $(".error").remove();
                if (data.success) {
                    $(formID).parent().prepend('<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-check"></i> '+data.success+'</div>');

                    setTimeout(function(){
                        if(data.redirect){
                            window.location = data.redirect;
                        }
                    },2000);
                    
                    if(data.assign){
                        $.each(data.assign, function(key,value) {
                            $("[name='"+key+"']").val(value);
                        });
                    }
                    
                    if(data.show){
                        $.each(data.show, function(key,value) {
                            $(value).show();
                        });
                    }
                    
                    if (data.error) {
                        showValidation(data.error);
                    }
                }else{
                    if (data.error) {
                        $(formID).parent().prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> '+data.error+'</div>');
                    }
                    showValidation(data);
                }
                $('#popup').hide();
            }
        }); 
    });
    

    $(document).on('click','[data-request="paginate"]',function(e){
        $('#popup').show();
        
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
                $('#popup').hide();
            },error: function(error){
                $('#popup').hide();
            }
        }); 
    });

    if($('[data-request="paginate"]').length > 0){
        $('[data-request="paginate"]').trigger('click');
    }

    $(document).on('click','[data-action="trigger"]',function (event) {
        var $triggerClass = $(this).data('trigger');
        $('.'+$triggerClass).show();
        $('.'+$triggerClass+' .SGCreator-view').trigger('click');
    });
    
    $(document).on('click','[data-action="image"]',function (event) {
        $('.alert').remove();
        var process = $(this).data('process');
        var record  = $(this).data('record');
        var url     = $(this).data('url');
        var _this   = $(this);
        $('#popup').show();
        if(process == 'delete'){
            $.ajax({ 
                url: url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                type: 'get', 
                success: function(data){
                    if(data.status == true){
                        $('#'+(data.type)+'_'+(data.record)).parent().parent().parent().fadeOut(500);
                        setTimeout(function(){
                            $('#'+(data.type)+'_'+(data.record)).parent().parent().parent().remove();
                        },500);
                    }else{
                        $('#content-wrapper .content').prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> '+data.message+'</div>');
                        scrollToErrorAlert();
                    }
                    $('#popup').hide();
                }
            });
        }else if(process == 'softdelete'){
            $.ajax({ 
                url: url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                type: 'get',
                success: function(data){
                    if(data.status == true){
                        $('#'+(data.type)+'_'+(data.record_id)).parent().attr('style','background: url('+data.image+') no-repeat center center;background-size:100% 100%');
                        $('#'+(data.type)+'_'+(data.record_id)).find('img').attr('src',data.image);
                        $('.'+(data.type)+'_'+(data.record_id)+' .fancybox').attr('href',data.image);
                        $('.'+(data.type)+'_'+(data.record_id)).children('span').addClass('hide');
                    }else{
                        $('#content-wrapper .content').prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> '+data.message+'</div>');
                        scrollToErrorAlert();
                    }
                    $('#popup').hide();
                }
            });
        }else if(process == 'swap'){
            $.ajax({ 
                url: url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                type: 'get', 
                success: function(data){  
                    if(data.status == true){
                        window.location = window.location;
                    }else{
                        $('#content-wrapper .content').prepend('<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-ban"></i> '+data.message+'</div>');
                        scrollToErrorAlert();
                    }    
                    $('#popup').hide();
                }
            });
        }else if(process == 'approve'){
            $.ajax({ 
                url: url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                type: 'get', 
                success: function(data){    
                    _this.hide();
                    _this.parent().children().removeClass('hide');
                    $('#popup').hide();
                }
            });
        }
    });

    $(document).on("show.bs.modal","#modal-box",function (event) {
        $('.alert').remove();
        $('.error').remove();
        $('.has-error').removeClass('has-error');
        
        var obj     = $(event.relatedTarget);
        var request = obj.data("request");
        $(this).find(".modal-title").text(obj.data("title"));
        $(this).find(".submit").text(obj.data("button"));
        $(this).find(".submit").attr('data-url',obj.data("url"));

        if(obj.data("request")){
            $.ajax({ 
                url: request, 
                cache: false, 
                contentType: false, 
                processData: false, 
                dataType: 'json', 
                type: 'get', 
                success: function(data){
                    console.log(data);
                    $.each(data, function (index, value) {
                        $('form [name="' + index + '"]').val(value);
                    });
                }
            });
        }else{
            $('[type="hidden"]').val('');
            $('[name="category_name"]').val('');
            $('[name="bank_name"]').val('');
        }
    });
});

function showValidation(msg) {
    if ($.isPlainObject(msg)) {
        $data = msg;
    }else {
        $data = $.parseJSON(msg);
    }
    
    $('.error').remove();
    $('.has-error').removeClass('has-error');
    
    $.each($data, function (index, value) {
        var name = index.replace(/\./g, '][');
        if (index.indexOf('.') !== -1) {
            name = name + ']';
            name = name.replace(']', '');
        }
        
        if (name.indexOf('[]') !== -1) {
            $('form [name="' + name + '"]').last().parent().parent().parent().addClass('has-error');
            $('form [name="' + name + '"]').last().parent().parent().parent().after('<p class="error"><span class="help-block">' + value + '</span></p>');
        }else{
            $('form [name="' + name + '"]').parent().addClass('has-error');
            $('form [name="' + name + '"]').after('<p class="error"><span class="help-block">' + value + '</span></p>');
        }
    });
    scrollToErrorInput();
}

function scrollToErrorInput() {
    if ($(".has-error").length > 0) {
        $('html, body').animate({
            scrollTop: $(".has-error").offset().top
        }, 200);
    }
}

function scrollToErrorAlert() {
    if ($(".alert").length > 0) {
        $('html, body').animate({
            scrollTop: $(".alert").offset().top
        }, 200);
    }
}

setTimeout(function(){
    if($('.fancybox').length > 0){
        if (!(/Android|webOS|iPhone|iPad|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
            $('.fancybox').fancybox({
                'padding'       :  0,
                'title'         : false,
                'type'          : 'image',
                'fitToView'     : true,
                'autoSize'      : true,
                'helpers'       : { 'overlay' : { 'locked' : false } },
                beforeLoad: function(){
                  $('body').addClass('no-transform');
                },
                afterClose: function(){
                  $('body').removeClass('no-transform');
                }
            });
        }else {
            $('.fancybox').fancybox({
                'title'         : false,
                'type'          : 'image',
                'fitToView'     : true,
                'autoSize'      : true,
                'helpers'       : { 'overlay' : { 'locked' : false } },
                beforeLoad: function(){
                  $('body').addClass('no-transform');
                },
                afterClose: function(){
                  $('body').removeClass('no-transform');
                }
            });
        }
    }
},2000);