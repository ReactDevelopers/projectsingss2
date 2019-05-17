<h3 style="font-size: 16px;">Project Details</h3>
<table border="1" cellspacing="0" cellpadding="10" width="100%" style="font-family:Helvetica Neue,Helvetica,Arial,sans-serif;border-collapse: collapse;border: 1px solid #cccccc;">
	<tr>
		<td><b>Project Name</b></td>
		<td>{{$project->title}}</td>
		<td><b>Project Type</b></td>
		<td>{{ucfirst($project->employment)}}</td>
	</tr>
	<tr>
		<td><b>Timeline</b></td>
		<td>{{___date_difference($project->startdate, $project->enddate)}}</td>
		<td><b>Total Time</b></td>
		<td>
			@if(!empty($project->projectlog))
				{{substr($project->projectlog->total_working_hours,0,-3)}} {{trans('website.W0759')}}
			@else
				{{ '00:00 '.trans('website.W0759')}}
			@endif
		</td>
	</tr>
	<tr>
		<td><b>Transaction ID</b></td>
		<td>{{strtoupper($payment['CORRELATIONID'])}}</td>
		<td><b>Transaction Date</b></td>
		<td>{{___d(date('Y-m-d'))}}</td>
	</tr>
	<tr>
		<td><b>Payment Amount</b></td>
		<td>{{___cache('currencies')[DEFAULT_CURRENCY].$amount}}</td>
		<td><b>Commission</b></td>
		<td>{{___cache('currencies')[DEFAULT_CURRENCY].$commission}}</td>
	</tr>
</table>

<h3 style="font-size: 16px;">Timesheet Details</h3>
<table border="1" cellspacing="0" cellpadding="10" width="100%" style="font-family:Helvetica Neue,Helvetica,Arial,sans-serif;border-collapse: collapse;border: 1px solid #cccccc;">
	@if(!empty($projectlog))
		@foreach($projectlog as $item)
			<tr>
				<td><b>Date</b></td>
				<td>{{___d($item->workdate)}}</td>
				<td><b>Time</b></td>
				<td>
					@if(!empty($project->projectlog))
						{{substr($item->total_working_hours,0,-3)}} {{trans('website.W0759')}}
					@else
						{{ '00:00 '.trans('website.W0759')}}
					@endif
				</td>
			</tr>
		@endforeach
	@endif
</table>
<br>