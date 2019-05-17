<table>
	<thead>
		<tr>
			<th>Customer Name</th>
			<th>Customer Email</th>
			<th>Vendor</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>Title</th>
			<th>Content</th>
			<th>Status</th>
			<th>Created Date Time</th>
		</tr>
	</thead>
	<tbody>
		@if(count($comments) > 0)
			@foreach($comments as $key => $comment)
			<?php $customer = \Modules\User\Entities\User::find($comment->user_id);
                  $customerEmail = '';
                  if(isset($customer) && !empty($customer))
                  {
                      $customerEmail = $customer->email;
                  }
             ?>
				<tr>
					<td>{{ Modules\Vendor\Entities\Vendor::find($comment->user_id)?Modules\Vendor\Entities\Vendor::find($comment->user_id)->first_name : '' }}</td>
					<td>{{ $customerEmail }}</td>
					<td>{{  Modules\Vendor\Entities\Vendor::find($comment->vendor_id) ? Modules\Vendor\Entities\Vendor::find($comment->vendor_id)->vendorProfile->business_name : '' }}</td>
					<td>{{ $comment->reviewer_category }}</td>
					<td></td>
					<td>{{ $comment->title }}</td>
					<td>{{ $comment->content }}</td>
					<td>{{ $comment->status ? 'Active' : 'InActive' }}</td>
					<td>{{ $comment->created_at && $comment->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i:s') : null }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>