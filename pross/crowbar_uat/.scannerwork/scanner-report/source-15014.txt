<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<div class="row">
	@if(!empty(\Auth::user()))
	<div class="col-md-3 col-sm-12 col-xs-12">
		<div class="profile-view talent-profile-wrapper text-center">
			<div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
		        <div class="profile-left">
		            <div class="user-profile-image">
		                <div class="user-display-details">
		                    <div class="user-display-image" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%"></div>
		                </div>
		            </div>
		        </div>
		        <div class="user-name-info">
		            <p>{{$user['first_name']}}</p>
		        </div>
		        <div class="user-memeber">
		            <p>member since</p>
		        </div>
		        <div class="profile-expertise-column">
					@if(!empty($user['expertise']))
		                <span class="experience">{{ !empty($user['experience']) ? sprintf("%s %s",$user['experience'],trans('website.W0669')) : '' }}</span>
		            @endif
		            @if(!empty($user['expertise']))
		                <span class="label-green color-grey">{{ expertise_levels($user['expertise']) }}</span>
		            @endif
		        </div>
		        <div class="rating-review">
		            <span class="rating-block">
		                {!! ___ratingstar($user['rating']) !!}
		            </span>
		            <a href="javascript:void(0);" class="reviews-block underline">{{ $user['review'] }} {{trans('website.W0213')}}</a>
		        </div>
		        @if(!empty(\Auth::user()) && \Auth::user()->type == 'talent')
			        <div class="item-list">
			            <span class="item-heading">Expected Rate</span>
				        <span class="item-description">
						    @foreach($user['remuneration'] as $item)
						        @if($item['interest'] != 'fixed')
						            {!!sprintf('%s/%s',___currency($item['workrate'],true,true),substr($item['interest'],0,-2)).'<br>'!!}
						        @else
						            <span class="label-green color-grey">{{$item['interest']}}</span>
						        @endif
						    @endforeach
						</span>
			        </div>
		        @endif
		        <div class="completion-bar">
		            <span style="width: {{ ___decimal($user['profile_percentage_count']) }}%;">
		                <span class="percentage-completed floated-percent">{{ ___decimal($user['profile_percentage_count']) }}%</span>
		            </span>
		        </div>
			</div>
		</div>
	</div>
	@endif
	<div class="col-md-6 col-sm-12 col-xs-12 p-l-5 p-r-5 middle-section" >
		<section class="home-feed-section">
			<div class="listing-wrapper owl-carousel" id="listing-caroousel">
					<div class="item {{ ($get_group == 0) ? 'active':'' }}">
						<a href="{{url('network/home')}}"><span>My Feeds</span></a>
					</div>
					@if(!empty($groups))
						@foreach($groups as $key1=>$value1)
							<div class="item {{ ($get_group == $value1['id']) ? 'active':'' }}">
								<a href="{{url('network/home').'?group='.___encrypt($value1['id'])}}"><span>{{$value1['name']}}</span></a>
							</div>
						@endforeach
					@endif
			</div>	
			<form method="post" role="find-jobs" accept-charset="utf-8" class="form-horizontal" autocomplete="off" onkeypress="return event.keyCode != 13;">
				<div class="search-article-wrapper">
					<h2>Search Article, Question or Event</h2>
					<div class="search-wrapper">
						{{-- <input type="search" class="form-control"> --}}
						<input type="text" value="{{ \Request::get('_search') }}" name="search" placeholder="Search" class="form-control" id="search_networks" data-request="search"/>
						<input type="hidden" value="{{ $get_group_id }}" name="get_group_id" class="form-control"/>
						<buttton class="btn button searching">
							<img src="{{asset('images/white-search-icon.png')}}">
						</button>
					</div>

					{{-- CODE FOR RADIO BUTTON --}}
					{{-- <ul class="article-listing">
						<li>
							<div class="radio radio-inline article-checkbox">                
								<input name="listing_radio" type="radio" id="article" value="article">
								<label for="article">
									<span>Article</span>
								</label>
							</div>
						</li>
						<li class="question-link">
							<div class="radio radio-inline question-checkbox">                
								<input  name="listing_radio" type="radio" id="question" value="forum">
								<label for="question">
									<span>Question</span>
								</label>
							</div>
						</li>
						<li class="event-link">
							<div class="radio radio-inline event-checkbox">                
								<input  name="listing_radio" type="radio" id="event" value="event">
								<label for="event">
									<span>Event</span>
								</label>
							</div>
						</li>
					</ul> --}}

					{{-- CODE FOR LINKS --}}

					<ul class="article-listing">
						<li>
							<div class="radio radio-inline article-checkbox">
								<label>
									<a href="{{url('network/article/add')}}">Article</a>
								</label>
							</div>
						</li>
						<li class="question-link">
							<div class="radio radio-inline question-checkbox">
								<label>
									<a href="{{url('network/community/forum/question/ask')}}">Question</a>
								</label>
							</div>
						</li>
						@if(!empty(\Auth::user()) && \Auth::user()->type == 'talent')
							<li class="event-link">
								<div class="radio radio-inline event-checkbox">
									<label>
										<a href="{{url('/talent/network/post-event')}}">Event</a>
									</label>
								</div>
							</li>
						@endif
					</ul>

				</div>
				<br/>
				{{-- Page Loader --}}
                
				{{-- Append HTML in following section --}}
				<div class="post-wrapper-section">
					<div id="home_listing" class="timeline timeline-inverse"></div>
                	<div class="pager text-center"><img src="{{ asset('images/loader.gif') }}"></div>
					<div>
	                   <div id="loadmore">
	                       <button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="{{ url(sprintf('/_mynetworks/_home')) }}" data-target="#home_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role='find-jobs']">{{ trans('website.W0254') }}</button>
	                   </div>
	               </div>
	               <input type="hidden" name="page" value="1">
				</div>
				{{--/ Append HTML in following section --}}
			</form>
		</section>
	</div>
	<div class="col-md-3 col-sm-12 col-xs-12">
		<div class="home-right-section">
			<div class="suggested-job-header">
				<h2>{{$suggestion_right_txt}}</h2>
			</div>
			{{-- For Talent --}}
			@if(!empty($suggested_jobs))
				@foreach($suggested_jobs as $key=>$value)
					<div class="suggested-job-links">
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="company-image">
									<img class="attendies-image" src="{{asset($value['company_logo'])}}">
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="price-text">
									<div class="contentbox-price-range">
										<span>{{___format($value['price'],true,true)}}<br>
											<span class="label-green color-grey">{{ucfirst($value['expertise'])}}</span>
										</span>
									</div>
								</div>
							</div>
						</div>
						<h5>{!!$value['description']!!}</h5>
						<span class="company-name">{{$value['company_name']}}</span>
						<label class="posted-ago">Posted <span class="posted-ago-grey">{{___ago($value['created'])}}</span></lalbel>
					</div>
				@endforeach
			@endif
			{{-- For Employer --}}
			@if(!empty($suggested_talents))
				@foreach($suggested_talents as $key1=>$value1)
					<div class="suggested-job-links">
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="company-image">
									<img class="attendies-image" src="{{asset($value1['user_img'])}}">
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="price-text">
									<div class="contentbox-price-range">
										<span class="label-green color-grey">{{ucfirst($value1['expertise'])}}</span>
										</span>
									</div>
								</div>
							</div>
						</div>
						<h5>{{$value1['name']}}</h5>
					</div>
				@endforeach
			@endif	
		</div>
	</div>
</div>
@push('inlinescript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="{{ asset('script/homefilter.js') }}" type="text/javascript"></script>
<script type="text/javascript">

$(".searching").on('click',function(e) {
	isLoadMore = false;
	$('[name="page"]').val(1);
    $('[data-request="filter-paginate"]').trigger('click');
});

$('#listing-caroousel').owlCarousel({
    loop:false,
    margin:30,
    dots:false,
	items:4,
    dotspeed:1000,
    autoplay:false,
    nav:true,
    responsive:{
        0:{
            items:4
        },
        400:{
            items:4
        },
    }
});

$(window).on('load resize',function(){
	var a =$(window).width();
	if(a <= 991){
		$('.middle-section').removeClass('p-l-5 p-r-5');
	}else{
		$('.middle-section').addClass('p-l-5 p-r-5');
	}
});


/*$(document).on('click','[data-request="home-follow-question"]',function(){
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
                $this.html($response.data);
                $('.follow_user_'+$response.user_id).removeClass('active');
                $('.follow_user_'+$response.user_id).html($response.data);

            }else{
                $this.addClass('active');
                $this.html($response.data);
				$('.follow_user_'+$response.user_id).addClass('active');
                $('.follow_user_'+$response.user_id).html($response.data);
            }
        },error: function(error){
            $('#popup').hide();
        }
    });
});

$(document).on('click','[data-request="home-follow-event"]',function(){
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
                $this.html($response.data);
                $('.follow_event_user_'+$response.user_id).removeClass('active');
                $('.follow_event_user_'+$response.user_id).html($response.data);
            }else{
                $this.addClass('active');
                $this.html($response.data);
				$('.follow_event_user_'+$response.user_id).addClass('active');
                $('.follow_event_user_'+$response.user_id).html($response.data);
            }
        },error: function(error){
            $('#popup').hide();
        }
    });
});
*/
$(document).on('click','[data-request="home-favorite-event"]',function(){
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
                $('.fav_evt_'+$response.event_id).removeClass('active');
            }else{
                $this.addClass('active');
				$('.fav_evt_'+$response.event_id).addClass('active');
            }
        },error: function(error){
            $('#popup').hide();
        }
    });
});
$(document).on('click','[data-request="home-add-rsvp"]',function(){
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
</script>
@endpush