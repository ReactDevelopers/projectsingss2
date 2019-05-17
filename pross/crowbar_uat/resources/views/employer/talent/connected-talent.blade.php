@include('employer.job.includes.talent-profile-menu')
@if(!empty($connected_user))
	@foreach($connected_user as $key => $value)
		<div class="content-box find-job-listing clearfix">
			<div class="">
				<div class="content-box-header clearfix">
					<img src="{{$value->get_profile}}" alt="profile" class="job-profile-image"><div class="contentbox-header-title">
						<h3>
							<a href="{{url('employer/find-talents/profile?talent_id='.___encrypt($value->user->id_user))}}">{{$value->user->name}}</a>
						</h3>
						@if($value->user->country!=null)
							<span class="company-name">{{$countries[$value->user->country]}}</span>
						@endif

						@if(count($value->industry) > 0)
							<span class="">{{$value->industry[0]['name']}}</span>
						@endif
					</div>
				</div>
			</div>
		</div>
	@endforeach
@else
<div class="content-box find-job-listing clearfix">
	<div class="">
		<div class="content-box-header clearfix">
			<p>
				No Records Found
			</p>
		</div>
	</div>
</div>
@endif


