<section class="community-members">
	@if(!empty($request_list))
		<h6>My network</h6>
		<ul class="allRequests">
			@foreach($request_list as $key=>$val)
				<li>
					<div class="request-content-box clearfix">
						<div class="profile-img-cell">
							<span class="requestprofile-img">
						        <img src="{{$val['picture']}}">							
							</span>
						</div>
				     	<div class="requestprofile-details">
				         	<div class="contentbox-header-title">
				            	<h3 class="member-detail-link"><a href="{{url(sprintf("%s/view/%s",TALENT_ROLE_TYPE,___encrypt($val['id_user'])))}}">{{$val['name']}}</a></h3>
				            	@if(!empty($val['industry_name']) && !empty($val['country']))
				            	<span class="company-name">{{$val['industry_name'].' ('.$val['country'].')'}}</span>
				            	@endif
				            	<span class="company-name">Member since {{date('d F Y',strtotime($val['created']))}}</span>
				            	@if(!empty($val['note']))
				            		<p>Note- {{$val['note']}}</p>
				            	@endif
				         	</div>
				      	</div>
					    <div class="requestprofile-actions">
					    	<ul class="requestprofile-actionList">
					    		<li>
									<button type="button" data-request="accept-member-req" data-value="ignore" name="later" data-url="{{ url(sprintf('%s/acceptmember?member_id=%s&user_id=%s&status=%s',TALENT_ROLE_TYPE,$val['member_id'],$val['user_id'],'rejected')) }}" class="greybutton-line">Ignore</button>					    			
					    		</li>
					    		<li>
									<button type="button" data-request="accept-member-req" data-value="accept" name="save" class="button" data-url="{{ url(sprintf('%s/acceptmember?member_id=%s&user_id=%s&status=%s',TALENT_ROLE_TYPE,$val['member_id'],$val['user_id'],'accepted')) }}">Accept</button>
					    		</li>
					    			
					    	</ul>
					    </div>
					</div>
				</li>
			@endforeach
		</ul>
	@endif
	<div class="">
		<h6>People you may know</h6>
		<div class="no-table datatable-listing">
	        {!! $html->table(); !!}
	    </div>
	</div>
</section>
@push('inlinescript')
	<script type="text/javascript" src="{{ asset('js/jquery.dataTables.js') }}"></script>
    {!! $html->scripts() !!}
    <script type="text/javascript">
		$(document).on('click','[data-request="accept-member-req"]',function(e){
		    $('#popup').show();
		    var $this       = $(this);
		    var $value      = $this.data('value'); 
		    var $url        = $this.data('url');

		    $.ajax({
		        url: $url, 
		        type: 'get', 
		        success: function($response){
		            $('#popup').hide();

		            if ($response.status === true){
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
@endpush