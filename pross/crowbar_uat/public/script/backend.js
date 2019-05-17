$(function(){
	/*INLINE FORM*/
    $(document).on('click','[data-request="inline-form"]',function(){
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();

        var $this       = $(this);
        var $url        = $this.data('url');
        var $target     = $this.data('target');
        
        $.ajax({
            method: "POST",
            url: $url,
        })
        .done(function($response) {
            if($response.status === true){
                if($response.redirect === true){
                    window.location = window.location;
                }else if($response.redirect === 'datatable'){
                    LaravelDataTables["dataTableBuilder"].draw();
                }else if($response.redirect === 'render'){
                    $($target).html($response.data);
                    if($('select').length > 0){
                        $('select').select2({
                            placeholder: function(){
                                $(this).find('option[value!=""]:first').html();
                            }
                        });
                    }                    
                }

                $('.content').prepend($response.message);
            }else{
                $('.content').prepend($response.message);
            }
            $('#popup').hide();
        });
    });
    
    /*INLINE CONFIRM AJAX*/
    $(document).on('click','[data-request="ajax-confirm"]',function(){
        $('.alert').remove();

		var $this 		= $(this);
		var $url 		= $this.data('url');
        var $ask 		= $this.data('ask');
        var $ask_title 	= $this.data('ask_title');

        swal({
            title: $ask_title,
            html: $ask,
            showLoaderOnConfirm: true, 
            showCancelButton: true, 
            showCloseButton: false, 
            allowEscapeKey: false, 
            allowOutsideClick:false, 
            confirmButtonText: "Confirm", 
            cancelButtonText: 'No Thanks', 
            confirmButtonColor: '#0FA1A8', 
            cancelButtonColor: '#CFCFCF',
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                    	$.ajax({
			                method: "POST",
			                url: $url,
			            })
			            .done(function($response) {
                            if(typeof LaravelDataTables !== 'undefined'){
                                LaravelDataTables["dataTableBuilder"].draw();
                            }

                            if($response.message){
                                $('.content').prepend($response.message);
                                
                                if($('.alert').length > 0){
                                    $('html, body').animate({
                                        scrollTop: ($('.alert').offset().top-100)
                                    }, 200);
                                }
                            }

                            if($response.redirect === true){
                                window.location = window.location;
                            }else if($($response.redirect).length > 0){
                                $($response.redirect).remove();
                            }

                            resolve();              
			            });
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
	});

    $(document).on('click','[data-request="ajax-submit"]',function(){
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
                        title: 'Message',
                        html: $response.message,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        confirmButtonText: 'Okay',
                        cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                        confirmButtonColor: '#0FA1A8',
                        cancelButtonColor: '#CFCFCF',
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {
                                    if($response.redirect){
                                        window.location = $response.redirect;
                                    }              
                                    
                                    if($('[data-request="remove-local-document"]').length > 0){
                                        $('[data-request="remove-local-document"]').remove();
                                    }              

                                }
                                resolve();
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

    $(document).on('click','[data-request="inline-submit"]',function(){
        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
        var $this       = $(this);
        var $target     = $this.data('target');
        var $url        = $($target).attr('action');
        var $method     = $($target).attr('method');
        var $data       = new FormData($($target)[0]);

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
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        confirmButtonText: 'Okay',
                        cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                        confirmButtonColor: '#0FA1A8',
                        cancelButtonColor: '#CFCFCF',
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {
                                    LaravelDataTables["dataTableBuilder"].draw();
                                    $($target).find('select').val("").trigger('change');
                                    $($target).find('input[type="text"]').val('');
                                    $($target).find('input[type="hidden"]').not('input[name="action"]').val('');
                                    
                                    resolve();
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

    $(document).on('click','[data-request="status"]',function(){
        $('.alert').remove();

        var $url            = $(this).data('url');
        var $ask             = $(this).data('ask');

        swal({
            title: 'Confirm',
            html: $ask,
            showLoaderOnConfirm: true,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0FA1A8',
            cancelButtonColor: '#CFCFCF',
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                        $('#popup').show();
                        $.ajax({
                            method: "POST",
                            url: $url,
                        }).done(function($response) {
                            if($response.status === true){
                                if($response.redirect === true){
                                    window.location = window.location;
                                }else if($response.redirect === 'datatable'){
                                    LaravelDataTables["dataTableBuilder"].draw();
                                }else if($response.redirect === 'user-delete'){
                                    // SWAL
                                    swal({
                                        title: '',
                                        html: $response.message,
                                        showLoaderOnConfirm: false,
                                        showCancelButton: false,
                                        showCloseButton: false,
                                        allowEscapeKey: false,
                                        allowOutsideClick:false,
                                        confirmButtonText: 'Okay',
                                        cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                                        confirmButtonColor: '#0FA1A8',
                                        cancelButtonColor: '#CFCFCF',
                                    });
                                }else if($response.redirect === 'render'){
                                    $($target).html($response.data);
                                    if($('select').length > 0){
                                        $('select').select2({
                                            placeholder: function(){
                                                $(this).find('option[value!=""]:first').html();
                                            }
                                        });
                                    }                    
                                }
                            }else{
                                // window.location = window.location;
                            }
                        }).always(function() {
                            $('#popup').hide();
                        });
                    }
                    resolve();
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
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

    $(document).on('click','[data-request="status-request"]',function(){
        $('.alert').remove();

        var $url            = $(this).data('url');
        var $ask             = $(this).data('ask');

        swal({
            title: 'Confirm',
            html: $ask,
            showLoaderOnConfirm: true,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0FA1A8',
            cancelButtonColor: '#CFCFCF',
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
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
                    }
                    resolve();
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
    });        

    // $(document).on('click','[data-request="remove-local-document"]', function(){
    //     var $this               = $(this);
    //     var $target             = $this.data('target');
    //     var $source             = $this.data('source');
    //     var $destination        = $this.data('destination');
    //     var fields              = [];

    //     if($($destination).val()){
    //         var fields          = ($($destination).val()).split(",");
    //     }

    //     fields[(fields.length)] = $($target).find($source).val();
    //     $($destination).val(fields.join(','));

    //     $($target).fadeOut();
    //     $($target).remove(); 
    // });

    $(document).on('click','[data-request="remove-local-document"]', function(){
        var $this           = $(this);
        var $target         =  $this.data('remove-image');
        var $input_name     =  $this.data('input-name');
        var $data           = new FormData($('#main-form')[0]);
        $($target).remove();
        $($input_name).val('');
        $('.single-remove').show();
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
            success: function(response){
                if(response.status === true){
                    LaravelDataTables["dataTableBuilder"].draw();
                }
                $('#popup').hide();
            },error: function(error){
                $('#popup').hide();
            }
        }); 
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

    $('select').select2({
        placeholder: function(){
            $(this).find('option[value!=""]:first').html();
        }
    });        
});


    $(document).on('click','[data-request="ajax-modal"]',function(e){
        $('#popup').show();
        var $this       = $(this);
        var $target     = $this.data('target'); 
        var $url        = $this.data('url');

        console.log("target- "+$target);
        console.log("url- "+$url);

        $.ajax({
            url: $url, 
            type: 'POST', 
            success: function($response){
                $('#popup').hide();
                $($target).html($response);
                $($target).modal({show:true});
            },error: function(error){
                
            }
        }); 
    });

function show_validation_error(msg) {
    if ($.isPlainObject(msg)) {
        $data = msg;
    }else {
        $data = $.parseJSON(msg);
    }
    
    $.each($data, function (index, value) {
        var name = index.replace(/\./g, '][');
        if (index.indexOf('.') !== -1) {
            name = name + ']';
            name = name.replace(']', '');
        }
        
        if (name.indexOf('[]') !== -1) {
            $('form [name="' + name + '"]').last().closest('.form-group').addClass('has-error');
            $('form [name="' + name + '"]').last().closest('.form-group').find('.message-group').append('<div class="help-block">' + value + '</div>');
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
                }else{
                    $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                    $('form [name="' + name + '"]').after('<div class="help-block">' + value + '</div>');
                }
            }else{
                $('form [name="' + name + '"]').closest('.form-group').addClass('has-error');
                $('form [name="' + name + '"]').after('<div class="help-block">' + value + '</div>');
            }
        }
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