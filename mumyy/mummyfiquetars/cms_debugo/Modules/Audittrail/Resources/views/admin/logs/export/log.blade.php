<table>
	<thead>
		<tr>
			<th>id</th>
			<th>Event name</th>
			<th>Performed User</th>
			<th>Email</th>
			<th>Title</th>
			<th>Created Date Time</th>
		</tr>
	</thead>
	<tbody>
		@if(count($logs) > 0)
			@foreach($logs as $key => $log)
				<tr>
					<td>{{ $log->id }}</td>
                    <td>{{ $log->event_name }}</td>
                    <td>{{ $log->performedUser ? $log->performedUser->first_name . ' ' .  $log->performedUser->last_name : "N/A" }}</td>
                    <td>{{ $log->performedUser ? $log->performedUser->email : "N/A" }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->created_at && $log->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') : "" }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>