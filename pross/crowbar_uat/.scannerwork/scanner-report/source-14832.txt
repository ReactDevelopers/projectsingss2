<form class="form-horizontal" role="employer_step_one" action="{{url(sprintf('%s/hire/talent/process/one',EMPLOYER_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
	{{ csrf_field() }}
	<div class="login-inner-wrapper">
		<h2 class="form-heading no-padding">{{trans('website.W0652')}}</h2>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="control-label col-md-12">{{ trans('website.W0285') }}</label>
					<div class="col-md-12">
						<div class="custom-dropdown">
							<input type="text" name="title" class="form-control" placeholder="{{trans('website.W0654')}}" value="{{$project['title']}}">
							<input type="text" class="hide" name="id_project" value="{{$project['id_project']}}">
							<input type="text" class="hide" name="action" value="{{$action}}">
							<input type="text" class="hide" name="talent_id" value="{{$talent_id}}">
						</div>              
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0284')}}</label>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<textarea id="description" name="description" placeholder="{{ trans('website.W0653') }}" class="form-control">{{$project['description']}}</textarea>
					</div>
				</div>
				<div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="checkbox small-checkbox">                
                            <input name="agree" type="checkbox" id="agree">
                            <label for="agree">
                                <span class="check"></span>
                                {!!
                                    sprintf(
                                        trans('website.W0149'),
                                        "<a class='underline' target='_blank' href='".url('/page/terms-and-conditions')."'>".trans('website.W0147')."</a>",
                                        "<a class='underline' target='_blank' href='".url('/page/privacy-policy')."'>".trans('website.W0148')."</a>"
                                    )
                                !!}
                            </label>
                        </div>
                    </div>
                </div> 
			</div>
		</div>                      
	</div>                        
	<div class="form-group button-group">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-7 col-sm-7 col-xs-6">
				</div>
				<div class="col-md-5 col-sm-5 col-xs-6">
					<button type="button" data-request="job-post" data-target='[role="employer_step_one"]' name="save" class="button">
						{{trans('website.W0659')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
@push('inlinescript')
	<style>
		.cke_inner {
		    background: none!important;
		}
	</style>
	<script src="//cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
	<script type="text/javascript">
		CKEDITOR.config.allowedContent = true;
		CKEDITOR.config.forcePasteAsPlainText = true;
		CKEDITOR.config.extraAllowedContent = "div(*)";
		CKEDITOR.config.toolbar = 'Basic';
		CKEDITOR.config.height = '250px';
		CKEDITOR.config.toolbar_Basic = [['Bold','NumberedList', 'BulletedList']];
		CKEDITOR.replace('description',{contentsCss: "{{asset('css/iframe.ckeditor.css')}}"});

		$(document).on('click','[data-request="job-post"]',function(){
	        /*REMOVING PREVIOUS ALERT AND ERROR CLASS*/
	        $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
	        var $this       = $(this);
	        var $target     = $this.data('target');
	        var $url        = $($target).attr('action');
	        var $method     = $($target).attr('method');
	        var $data       = new FormData($($target)[0]);
	        $data.append('description',CKEDITOR.instances.description.getData());
	        
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

	    $.ajax({
            type : 'GET',
            url : '{{ url("employer/check-job/payment-configure") }}',
            // data : register_form_data ,
            success : function($response){
                console.log('hello');
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
	            }
            }
        });
	</script>
@endpush