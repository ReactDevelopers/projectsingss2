@php 
	$header = 'innerheader';
	$footer = 'footer';
	$settings = \Cache::get('configuration');
@endphp
@extends('layouts.front.main')
@section('content')
<div class="contentWrapper socialEventDetails">
	<div class="container">
		<div class="vertual_events-wrapper clearfix">
		    <div class="datatable-listing events_details grid2">
		    	<table class="table">
		    		<tr>
		    			<td>
							<div class="grid-item2">
								<div class="events_desc">
									<h2><a href="javascript:void(0);"  class="form-heading">{{$event['event_title']}}</a></h2>
									<div class="events_listing">
										<label>{{trans('website.W0902')}}</label>
										<span>{{date('d M Y',strtotime($event['event_date']))}} {{date('H:i',strtotime($event['event_time']))}}</span>
									</div>

									@if($event['event_type'] == "virtual")
										<div class="events_listing">
											<label>{{trans('website.W0903')}}</label>
											<span><a href="{{$event['video_url']}}" target="_blank">{{$event['video_url']}}</a></span>
										</div>
									@else
										<div class="events_listing">
											<label>{{trans('website.W0904')}}</label>
											<span>{{$event['location']}}, {{$event['city_name']['city_name']}}, {{$event['state_name']['state_name']}}, {{$event['country_name']['country_name']}}</span>
										</div>
									@endif

									@if(!empty($event['file']))
										<div class="uploaded_banner">
										@php
											$base_url = ___image_base_url();
										@endphp
										<img src="{{$base_url.$event['file']['folder'].$event['file']['filename']}}"/>
										</div>
									@endif
									<p>{{$event['event_description']}}</p>
								</div>
							</div>
		    			</td>
		    		</tr>
		    	</table>
		    </div>
		</div>
	</div>
</div>
@endsection