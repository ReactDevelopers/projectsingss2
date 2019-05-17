$(function(){
	$(document).on('click', '[data-request="toggle-class"]', function(){
		var $target 	= $(this).data('target');
		var $id 		= $(this).data('id');

		if($($id).prop('checked') === true){
			$($target).removeClass("hide");
		}else{
			$($target).addClass("hide");
		}
	});

	$(document).on('click', '[data-request="save"]', function(){
		$('#popup').show();$('.alert').remove();$('.has-error').removeAttr('title');$('.has-error').removeClass('has-error');

		var $url = $($(this).data('target')).attr('action');
		var $data = new FormData($($(this).data('target'))[0]);
		
		$.ajax({
            method: "POST",
            url: $url,
            data: $data,
            cache: false, 
            contentType: false, 
            processData: false, 
            dataType: 'json', 
        }).done(function(response) {
            
            if(response.status === true){
            	dataTableInstance.draw();
            	$('.table-responsive').before(response.message);
            }else{
            	$.each(response.errors, function( field_name, item) {
				  	$('[name="'+field_name+'"]').parent().addClass('has-error');
				  	$('[name="'+field_name+'"]').attr('title',item);
				});

			  	$('.table-responsive').before(response.message);
            }

            $('#popup').hide();
        }).fail(function(error) {
            $('#popup').hide();
        }).always(function() {
            $('#popup').hide();
        });
	});

    $(document).on('click','[data-request="html"]',function(){
        $('.alert').remove();

        var $this           = $(this);
        var $url          	= $this.data('url');
        var $ask            = $this.data('ask');
        var $target         = $this.data('target');

        var r = confirm($ask);
        if(r == true){
            $('#popup').show();
            $.ajax({
                method: "POST",
                url: $url,
            }).done(function(response) {
              	if(response.status === true){
              		if($target){
              			console.log(response.target);
             			$($target).html(response.target);
              		}
          			
          			$this.parent().html(response.html);
              	}else{
              		//window.location = window.location;
              	}
            }).always(function() {
                $('#popup').hide();
            });
        }else{
            return false;
        }
    });
	
	$(document).on('click','[data-request="confirmed"]',function(){
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

	scrollToErrorInput();
});

function scrollToErrorInput() {
    if ($(".has-error").length > 0) {
        $('html, body').animate({
            scrollTop: $(".has-error").offset().top
        }, 200);
    }
}