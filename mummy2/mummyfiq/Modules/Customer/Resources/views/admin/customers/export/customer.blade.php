<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Email</th>
			<th>Status</th>
			<th>Created Date Time</th>
			<th>Last Login Date Time</th>
		</tr>
	</thead>
	<tbody>
		@if(count($customers) > 0)
			@foreach($customers as $key => $customer)
				<tr>
					<td>{{ $customer->full_name }}</td>
					<td>{{ $customer->email }}</td>
					<td>{{ $customer->getStatus() }}</td> 
					<td>{{ $customer->created_at && $customer->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($customer->created_at)->format('d/m/Y H:i:s') : null }}</td>
					<td>{{ $customer->last_login && $customer->last_login != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($customer->last_login)->format('d/m/Y H:i:s') : null }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>