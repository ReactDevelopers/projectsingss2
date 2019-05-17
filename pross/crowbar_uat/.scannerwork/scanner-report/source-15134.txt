<form class="form-horizontal" role="post_event" action="{{url('/talent/post-event/editprocess/'.___encrypt($eventDetails['id_events']))}}" method="post" accept-charset="utf-8">
	{{ csrf_field() }}
	<div class="login-inner-wrapper">
		<h2 class="form-heading no-padding">{{trans('website.W0849')}}</h2>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="form-group">
							<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0850')}}</label>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div>
									<select name="event_type" id="event_type" style="max-width: 400px; width: 100%;" class="form-control select2-hidden-accessible">
										<option value="">{{trans('website.W0851')}}</option>
										<option value="live" data-condition="live" @if($eventDetails['event_type'] == "live") selected @endif>{{trans('website.W0852')}}</option>
										<option value="virtual" data-condition="virtual" @if($eventDetails['event_type'] == "virtual") selected @endif>{{trans('website.W0853')}}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="form-group">
							<label class="control-label col-md-12">{{trans('website.W0014')}}</label>
							<div class="col-md-12">
								<div class="custom-dropdown">
									<input type="text" name="event_name" class="form-control" value="{{$eventDetails['event_title']}}">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="datebox-no startdate">
							<label class="control-label">{{trans('website.W0368')}}</label>  
							<div class="input-group datepicker">
								<input type="text" id="event_date" name="event_date" class="form-control" placeholder="DD/MM/YYYY" value="{{___convert_date($eventDetails['event_date'],'JS','d/m/Y')}}" maxlength="10">
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div>
							<label class="control-label">{{trans('website.W0854')}}</label> 
							<input type="text" id="time_hour" name="time_hour" autocomplete="off" class="form-control hasTimepicker" placeholder="HH:MM" maxlength="5">
						</div>
					</div>
				</div>
				@php
					if($eventDetails['event_type'] == "live"){
						$live_sec = "display:block";
						$virtual_sec = "display:none";
					}else{
						$live_sec = "display:none";
						$virtual_sec = "display:block";
					}
				@endphp
				<div class="live-section" style="{{$live_sec}}">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-6">
							<div class="form-group">
							    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0855')}}</label>
							    <div class="col-md-12 col-sm-12 col-xs-12">
							        <div class="custom-dropdown">
							            <select class="form-control" name="country" data-placeholder="{{trans('website.W0856')}}"></select>
							        </div>
							    </div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-6">
							<div class="form-group">
							    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0857')}}</label>
							    <div class="col-md-12 col-sm-12 col-xs-12">
							        <div class="custom-dropdown">
							            <select class="form-control" name="state" data-placeholder="{{trans('website.W0858')}}"></select>
							        </div>
							    </div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-6">
							<div class="form-group">
							    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0859')}}</label>
							    <div class="col-md-12 col-sm-12 col-xs-12">
							        <div class="custom-dropdown">
							            <select class="form-control" name="city" data-placeholder="{{trans('website.W0294')}}"></select>
							        </div>
							    </div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-6">
							<div class="form-group">
								<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0201')}}</label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="custom-dropdown">
										<input type="text" name="location" id="auto_complete" class="form-control" value="{{$eventDetails['location']!='0'? $eventDetails['location'] : '' }}">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="virtual-section" style="{{$virtual_sec}}">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-6">
							<div class="form-group">
								<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0860')}}</label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="custom-dropdown">
										<input type="text" name="video_url" class="form-control" value={{$eventDetails['video_url'] !=''? $eventDetails['video_url']:''}}>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="form-group">
							<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0861')}}</label>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="custom-dropdown">
									<select name="emails[]" class="form-control" data-request="email-tags" multiple="true" >
										@foreach($eventDetails['emails'] as $key => $value)
											<option value="{{$value['email']}}" selected>{{$value['email']}}</option>
										@endforeach
									</select>
									<div class="js-example-tags-container white-tags"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="form-group">
							<label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0862')}}</label>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="custom-dropdown">
									<input type="text" name="event_attendee" class="form-control" value="{{$eventDetails['maximum_attendees']}}">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
	                       <label class="control-label col-md-12">
	                           {{trans('website.W0863')}}
	                           <span></span>
	                       </label>
	                       <div class="col-md-12">
	                           <textarea style="height:auto;" type="text" name="event_desp" rows="6" class="form-control">{{$eventDetails['event_description']}}</textarea>
	                       </div>
	                   </div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="form-group attachment-group">
                            <label class="control-label col-md-12">{{trans('website.W0112')}}</label>
                            <div class="col-md-12">
                                <input type="hidden" name="attached_doc_id" id="attached_doc_id" value="0" />
                                <div class="upload-box" id="appenddochere">
                                </div>
                                <div class="single-remove">
                                @php
	                                if(!empty($eventDetails['file'])){

	                                	$event_file = $eventDetails['file'];
	                                	$url_delete = sprintf(
					                        url('%s/delete_event_file?id_file=%s'),
					                        TALENT_ROLE_TYPE,
					                        $event_file['id_file']
					                    );
										echo (sprintf(RESUME_TEMPLATE,
											$event_file['id_file'],
											url(sprintf('/download/file?file_id=%s',___encrypt($event_file['id_file']))),
											asset('/'),
											$event_file['filename'],
											$event_file['size'],
											$url_delete,
											$event_file['id_file'],
											asset('/')
										))	;
	                                }
                                @endphp
                                    @if(empty($eventDetails['file']))
	                                    <div class="fileUpload upload-docx"><span>{{trans('website.W0865')}}</span>
	                                        	<input type="file" name="file" class="upload" data-request="doc-submit" data-toadd=".upload-box" data-after-upload=".single-remove" data-target="[role=&quot;doc-submit&quot;]" data-single="true" action="{{url(sprintf('%s/doc-submit',TALENT_ROLE_TYPE))}}">
	                                    </div>
	                                    <span class="upload-hint">{{trans('website.W0866')}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>						
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="event-availability">
							<div class="radio color_pink">                
	                            <input type="radio" id="my_circle" name="visibility" value="circle" data-request="focus-input-checkbox" @if($eventDetails['visibility'] == "circle") checked="checked" @endif>
	                            <label for="my_circle"><span class="check"></span>{{trans('website.W0867')}}</label>
	                        </div>
						</div>
						{{-- <div class="event-availability">
							<div class="radio color_pink">                
	                            <input type="radio" id="member_only" name="visibility" value="premium" data-request="focus-input-checkbox" @if($eventDetails['visibility'] == "premium") checked="checked" @endif>
	                            <label for="member_only"><span class="check"></span>{{trans('website.W0868')}}</label>
	                        </div>
						</div> --}}
						<div class="event-availability">
							<div class="radio color_pink">                
	                            <input type="radio" id="free_entry" name="visibility" value="public" data-request="focus-input-checkbox" @if($eventDetails['visibility'] == "public") checked="checked" @endif>
	                            <label for="free_entry"><span class="check"></span>{{trans('website.W0869')}}</label>
	                        </div>
	                        @php
		                        if($eventDetails['is_free'] == "yes"){
		                        	$enter_fee = "display:none";
		                        }else{
		                        	$enter_fee = "display:block";
		                        }
	                        @endphp
	                        <div class="form-group mg_side0" id="div_enter_fee" style="{{$enter_fee}}">
		                        <div class="custom-dropdown">
		                        	<label class="control-label">{{trans('website.W0870')}}</label>
									<input type="text" name="event_fee" class="form-control" value="{{($eventDetails['entry_fee'] !=0 ? $eventDetails['entry_fee']:'' )}}">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="checkbox small-checkbox">                
                            <input name="agree" type="checkbox" id="agree" checked="checked" value="yes">
                            <label for="agree">
                                <span class="check"></span>
                                {{trans('website.W0871')}}                                
                            </label>
                        </div>
                    </div>
                </div> 
			</div>
		</div>                      
	</div>                        
	<div class="form-group button-group p-r-60">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-5 col-sm-5 col-xs-6">
					<button type="button" data-request="ajax-submit" data-target='[role="post_event"]' name="save" class="button">
						{{trans('website.W0901')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

@push('inlinescript')
    <link rel="stylesheet" href="{{asset('css/jquery.timepicker.min.css')}}">
@endpush
@push('inlinescript')
	<script type="text/javascript" src="{{asset('js/bootstrap-timepicker.min.js')}}"></script>
	<script src="{{asset('js/jquery.timepicker.min.js')}}"></script>
	<script type="text/javascript">
		var dateFormat = "dd/mm/yy";
		var from = $("#event_date").datepicker({
			changeMonth: true,
			changeYear: true,
			minDate: new Date(),
			numberOfMonths: 1,
			dateFormat: dateFormat
		});

		$('input:radio[name="visibility"]').click(function() {
			var visibility = $('input[name="visibility"]:checked').val();
		    if (visibility == "public") {
		    	$('#div_enter_fee').hide();
		    }else{
		    	$('#div_enter_fee').show();
		    }
		});

	    $("#event_type").change(function () {

		      var event_type = $(this).val();
		      var event_type_val = $(this).find(':selected').data("condition");

		      if(event_type_val == "live"){
		      	$('.live-section').show();
		      	$('.virtual-section').hide();
		      }else{
		      	$('.live-section').hide();
				$('.virtual-section').show();
		      }

		});

		$('input[name="file"]').on('change',function(){

			var base_url = "{{url('/talent/post-event/file')}}";

			var token  = "<?php echo csrf_token(); ?>";
		    var input = document.getElementById("file");
		    file = input.files[0];
			formData = new FormData();
		    formData.append("file", file);
		    formData.append("_token", token);

		      $.ajax({
		        url: base_url,
		        type: "POST",
		        data: formData,
		        processData: false,
		        contentType: false,
		        success: function(response) {
					if (Object.size(response.data) > 0 && response.status == false) {
                        /*TO DISPLAY FORM ERROR USING .has-error class*/
                        show_validation_error(response.data);
                    }else{
						$('#appenddochere').append(response.data);
						$('.single-remove').hide();
						$('#attached_doc_id').val(response.fileReturnId);
                    }
		        }
		      });
		});

		setTimeout(function(){
			$('[name="country"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/countries',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '{{$eventDetails['country_name']['id_country']}}', text: '{{$eventDetails['country_name']['country_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="state"]').val('').trigger('change');
                $('[name="city"]').val('').trigger('change');
            });


            $('[name="state"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/states',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            country: $('[name="country"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '{{$eventDetails['state_name']['id_state']}}', text: '{{$eventDetails['state_name']['state_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="city"]').val('').trigger('change');
            });

            $('[name="city"]').select2({
                ajax: {
                    url: base_url+'/cities',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            state: $('[name="state"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '{{$eventDetails['city_name']['id_city']}}', text: '{{$eventDetails['city_name']['city_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });
        },2000);

        //For deleting event file
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
	                                $('.single-remove').show();
	                                setTimeout(function(){
	                                    $('#'+toremove+'-'+data_id).remove();
	                                },1000);
	                                if($this.data('single') === true){
	                                    $(after_upload).show();
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

        $('#time_hour').timepicker({
		    timeFormat: 'h:mm p',
		    interval: 60,
		    minTime: '10',
		    maxTime: '11:00pm',
		    defaultTime: '{{date('H',strtotime($eventDetails['event_time']))}}', 
		    startTime: '08:00',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true
		});

	</script>
	{{-- For Autocomplete --}}
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_AUTO_SEARCH_KEY;?>&libraries=places&callback=initAutocomplete" async defer></script>
	<script type="text/javascript">
	var placeSearch, autocomplete, geocoder;

	function initAutocomplete() {
	  	geocoder = new google.maps.Geocoder();
	  	autocomplete = new google.maps.places.Autocomplete(
	    	(document.getElementById('auto_complete'))/*,
	    	{types: ['(cities)']}*/);

	  	autocomplete.addListener('place_changed', fillInAddress);
	}

	function codeAddress(address) {
		geocoder.geocode( { 'address': address}, function(results, status) {
	    	if (status == 'OK') {
	        	console.log(results[0].geometry.location);
	      	}else{
	        	console.log('Geocode was not successful for the following reason: ' + status);
	      	}
	    });
	}

	function fillInAddress() {
		var place = autocomplete.getPlace();
		// console.log(place.address_components);
		// console.log("place.address_components[0]");
       	var address='';

       	for(var i=0; i<2 ;i++){
       		address += place.address_components[i].long_name +', ';
       	}
       	address = address.substring(0, address.length-2);
        $('#auto_complete').val(address);
	}
		
	</script>
@endpush