<div class="grid-item2">
	<div class="events_desc">
		<h2><span href="javascript:void(0);"  class="form-heading">{{$event->event_title}}</span></h2>
		<div class="events_listing">
			<label>{{trans('website.W0902')}}</label>
			<span>{{date('d M Y',strtotime($event->event_date))}} {{date('H:i',strtotime($event->event_time))}}</span>
		</div>

		@if($event->event_type == "virtual")
			<div class="events_listing">
				<label>{{trans('website.W0903')}}</label>
				<span><a href="{{$event->video_url}}" target="_blank">{{$event->video_url}}</a></span>
			</div>
		@else
			<div class="events_listing">
				<label>{{trans('website.W0904')}}</label>
				<span>{{$event->location}}, {{$event->city}}, {{$event->state}}, {{$event->country}}</span>
			</div>
		@endif

		<div class="events_listing">
			<label>{{trans('website.W0905')}}</label>
			<span>{{$event->total_attending}} {{trans('website.W0887')}} ({{$event->in_circle_attending}} {{trans('website.W0931')}})</span>
		</div>

		@if(!empty($event->image))
			<div class="uploaded_banner">
				<img src="{{$event->image}}"/>
			</div>
		@endif

		<p>{{$event->event_description}}</p>
	</div>
	<div class="social_listing">
		<ul>
			@if($event->rsvp_response_status != 'yes' )
				<li id="rsvp-{{$event->id_events}}">
					<a href="javascript:void(0);" data-request="add-rsvp" data-toremove="rsvp" data-data_id="{{$event->id_events}}" data-url="{{ url(sprintf('%s/addRsvp?event_id=%s',TALENT_ROLE_TYPE,$event->id_events))}}" data-user="{{$event->id_events}}" data-ask="{{trans('website.W0911')}}" class="rsvp_icon">
						{{trans('website.W0906')}} 
						<img src="{{asset('images/rsvp.png')}}">
					</a>
				</li>
			@endif
			<li>
				<a href="javascript:void(0);" class="invite_icon" data-target="#add-member" data-request="ajax-modal" data-url="{{ url(sprintf('%s/invite-member?event_id=%s',TALENT_ROLE_TYPE,$event->id_events)) }}" href="javascript:void(0);">
					{{trans('website.W0907')}}
					<img src="{{asset('images/invite_member.png')}}">
				</a>
			</li>
			<li class="social_listing_links">
				<div class="dropdown socialShareDropdown">
					<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans('website.W0908')}}</a>
					<ul class="dropdown-menu">
						<li>
							<a href="javascript:void(0);" class="linkdin_icon">
								<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
								<script type="IN/Share" data-url="{{ url('/mynetworks/eventsdetail/'.$event->id_events) }}"></script>
								<img src="{{asset('images/linkedin.png')}}">
							</a>
						</li>
						<li>
							<a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{ url('/mynetworks/eventsdetail/'.$event->id_events) }}" target="_blank">
								<img src="{{asset('images/facebook.png')}}">
							</a>
							
						</li>
						<li>
							<a  target="_blank" href="https://twitter.com/share?url={{ url('/mynetworks/eventsdetail/'.$event->id_events) }}" class="twiter_icon">
								<img src="{{asset('images/twitter.png')}}">
							</a>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0)" class="events_book bookmark_icon @if($event->saved_bookmark) == 1) active @endif"  data-request="favorite-event" data-url="{{ url(sprintf('%s/fav-event?event_id=%s',TALENT_ROLE_TYPE,$event->id_events))}}">{{trans('website.W0909')}}
				</a>
			</li>
		</ul>
	</div>
</div>