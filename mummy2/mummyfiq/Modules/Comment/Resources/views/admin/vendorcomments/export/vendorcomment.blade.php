<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Customer Name</th>
			<th>Customer Email</th>
			<th>Portfolio</th>
			<th>Content</th>
			<th>Created Date Time</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		@if(count($comments) > 0)
			@foreach($comments as $key => $comment)
			<?php $customer = \Modules\User\Entities\User::find($comment->user_id);
				  $portfolio = \Modules\Portfolio\Entities\Portfolio::find($comment->portfolios_id);
                  $customerEmail = '';
                  $customerName = '';
                  $portfolioTitle = '';
                  if(isset($customer) && !empty($customer))
                  {
                      $customerEmail = $customer->email;
                      $customerName = $customer->first_name;
                  }
                  if(isset($portfolio) && !empty($portfolio))
                  {
                  	  $portfolioTitle = $portfolio->title;
                  }
             ?>
				<tr>
					<td>
                        {{ $comment->id }}
                    </td>
                    <td>
                        {{ $customerName }}
                    </td>
                    <td>
                        {{ $customerEmail }}
                    </td>
                    <td>
                        {{ $portfolioTitle }}
                    </td>
                     <td>
                        {{ str_limit($comment->comment, 50) }}
                    </td>
                    <td>
                        {{ $comment->created_at && $comment->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i:s') : "" }}
                    </td>
                    <td>
                        @if($comment->status == 1)
                        Active
                        @else
                        InActive
                        @endif
                    </td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>