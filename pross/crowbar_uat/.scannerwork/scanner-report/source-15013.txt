<div class="post-block event-block">
	<div class="post-header">
		<span class="before-image"></span>
		<div class="post-header-content">
			<a href="{{url('mynetworks/eventsdetail/'.$event['id_events'])}}">
				<h3>{{$event['event_title']}}</h3>
			</a>
			<label>Posted <span></span>{{ ___ago($event['created']) }}</label>
		</div>
		<a href="javascript:void(0)" class="fav_evt_{{$event['id_events']}} events_book pull-right bookmark_icon 
		@if($event['saved_bookmark']) == 1) active @endif"  data-request="home-favorite-event" data-url="{{ url(sprintf('/network/home//fav-event?event_id=%s',$event['id_events']))}}">a</a>
	</div>
	<div class="post-content">
		<p>
			{!! str_limit($event['event_description'],25)  !!} 
		</p>
		<div class="event-blocks">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					@if($event['event_type'] == 'live')
						<div class="event-listing">
							<h3 class="event-heading">Location</h3>
							<ul>
								<li>{{$event['location']}}</li>
								<li>{{$event['state']}}</li>
								<li>{{$event['country']}}</li>
							</ul>
						</div>
					@else
						<div class="event-listing">
							<h3 class="event-heading">Link</h3>
							<ul>
								<li>{{$event['video_url']}}</li> 
							</ul>
						</div>
					@endif
				</div>	
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="event-listing">
							<h3 class="event-heading">Date & Time</h3>
							<ul>
								<li>{{date('dS F Y',strtotime($event['event_date'])) }}</li>
								<li>{{date('h:i A',strtotime($event['event_time'])) }}</li>
							</ul>
						</div>
					</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="event-listing members-block">
						<h3 class="event-heading">Attendees</h3>
						{{-- <img class="attendies-image" src="{{asset('images/attendies.png')}}"> --}}
						<h4 class="members">{{$event['total_attending']}} Member(s) ({{$event['in_circle_attending']}} {{trans('website.W0931')}})</h4>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="event-listing members-block">
						@if($event['is_free'] != 'yes')
							<h3 class="event-heading">Entry Fee</h3>
							<span class="entry-fee">{{ $event['entry_fee'] }}</span>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="post-footer">
		<div class="row">
			<div class="col-md-3 col-sm-3 col-xs-12">
				<div class="comment-author">
					<img src="{{asset($userDetails['user_img'])}}" class="author-image">
					<h4>{{$userDetails['user_name']}}</h4>
					@if(\Auth::user() && \Auth::user()->id_user != $userDetails['posted_by'])
					    <div class="forum-follow-detail">
					        @php
					            if($userDetails['is_evt_following'] == 1){
					                $event_is_following = 'active';
					                $event_follow_text  = 'Following';
					            }else{
					                $event_is_following = '';
					                $event_follow_text  = 'Follow';
					            }
					        @endphp
					        <a href="javascript:void(0);" class="follow-icon follow_event_user_{{$userDetails['posted_by'].' '.$event_is_following}}" data-user_id="{{$userDetails['posted_by']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$userDetails['posted_by']))}}">{{$event_follow_text}}
					        </a>
					    </div>
					@endif
				</div>
			</div>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<div class="social-listing">
					<ul class="social-listing-links text-right">
						@if($event['rsvp_response_status'] != 'yes' )
							<li id="rsvp-{{$event['id_events']}}" class="socialShareDropdown comment-links">
								<a href="javascript:void(0);" data-request="home-add-rsvp" data-toremove="rsvp" data-data_id="{{$event['id_events']}}" data-url="{{ url(sprintf('%s/addRsvp?event_id=%s',TALENT_ROLE_TYPE,$event['id_events']))}}" data-user="{{$event['id_events']}}" data-ask="{{trans('website.W0911')}}" class="rsvp_icon home-page-rsvp">
									{{trans('website.W0906')}}
								</a>
							</li>
						@endif

						<li class="socialShareDropdown comment-links">
							<a href="javascript:void(0);" class="invite_icon home-page-invite" data-target="#add-member" data-request="ajax-modal" data-url="{{ url(sprintf('%s/invite-member?event_id=%s&ret_page=home',TALENT_ROLE_TYPE,$event['id_events'])) }}" href="javascript:void(0);">
								{{trans('website.W0907')}}
							</a>
						</li>
						<li>
							<div class="dropdown socialShareDropdown">
								<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans('website.W0908')}}</a>
								@php
									$event_share_url = url('/mynetworks/eventsdetail/'.$event['id_events']); 
								@endphp
								<ul class="dropdown-menu">
									<li>
										<a href="javascript:void(0);" class="linkdin_icon">
											<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
											<script type="IN/Share" data-url="{{ $event_share_url }}"></script>
											<img src="{{asset('images/linkedin.png')}}">
										</a>
									</li>
									<li>
										<a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{ $event_share_url }}" target="_blank">
											<img src="{{asset('images/facebook.png')}}">
										</a>
										
									</li>
									<li>
										<a  target="_blank" href="https://twitter.com/share?url={{ $event_share_url }}" class="twiter_icon">
											<img src="{{asset('images/twitter.png')}}">
										</a>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>