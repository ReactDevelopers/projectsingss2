<table>
	<thead>
		<tr>
			<th>{{ trans('report::reviews.table.commented by email') }}</th>
            <th>{{ trans('report::reviews.table.reported by email') }}</th>
            <th>{{ trans('report::reviews.table.vendor') }}</th>
            <th>{{ trans('report::reviews.table.content') }}</th>
            <th>{{ trans('core::core.table.created date') }}</th>
		</tr>
	</thead>
	<tbody>
		@if(count($comments) > 0)
			@foreach($comments as $key => $comment)
			 <?php 
                $vendorComment = \Modules\Comment\Entities\Vendorcomment::find($comment->comment_id);
                $emailReporter = '';
                $emailCommenter = '';
                $vendorBusinessName = '';
                $reporter = \Modules\User\Entities\User::find($comment->user_id);
                if(isset($vendorComment) && !empty($vendorComment))
                {
                    $commenter = \Modules\User\Entities\User::find($vendorComment->user_id);
                    $portfolio = \Modules\Portfolio\Entities\Portfolio::find($vendorComment->portfolios_id);
                    if(isset($portfolio) && !empty($portfolio))
                    {
                        $vendor = \Modules\Vendor\Entities\VendorProfile::where('user_id',$portfolio->vendor_id)->first();
                    }
                    
                }
                if(isset($commenter->email) && !empty($commenter->email)){
                    $emailCommenter = $commenter->email;
                }
                if(isset($reporter->email) && !empty($reporter->email)){
                    $emailReporter = $reporter->email;
                }
                if(isset($vendor->business_name) && !empty($vendor->business_name)){
                    $vendorBusinessName = $vendor->business_name;
                }
                $content = $comment->content;
            ?>
				<tr>
					<td>{{ $emailCommenter }}</td>
                    <td>{{ $emailReporter }}</td>
                    <td>{{ $vendorBusinessName }}</td>
                    <td>{{ str_limit($content, 50) }}</td>
                    <td>{{ $comment->created_at && $comment->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i:s') : "" }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>