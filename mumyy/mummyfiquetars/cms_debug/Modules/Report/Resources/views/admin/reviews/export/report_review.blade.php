<?php
    $reportReviewTitle = ['Scams','Inappropriate Language','Spamming Advertisements/Links','Others'];
    $review_reason = [
        'contains_offensive_content' => 'Contains Offensive Content',
        'contains_copyright_violation' => 'Contains Copyright Violation',
        'contains_adult_content' => 'Contains Adult Content',
        'invades_my_privacy' => 'Invades My Privacy',
    ];
?>
<table>
	<thead>
		<tr>
			<th>{{ trans('report::reviews.table.reviewed by email') }}</th>
            <th>{{ trans('report::reviews.table.reported by email') }}</th>
            <th>{{ trans('report::reviews.table.vendor') }}</th>
            <th>{{ trans('report::reviews.table.title') }}</th>
            <th>{{ trans('report::reviews.table.content') }}</th>
            <th>{{ trans('core::core.table.created date') }}</th>
		</tr>
	</thead>
	<tbody>
		@if(count($reviews) > 0)
			@foreach($reviews as $key => $review)
			<?php 

                $userReview = \Modules\Report\Entities\UserReview::find($review->review_id);
                $emailReporter = '';
                $emailReviewer = '';
                $emailVendor = '';
                $reporter = \Modules\User\Entities\User::find($review->user_id);
                if(isset($userReview) && !empty($userReview))
                {
                	$reviewer = \Modules\User\Entities\User::find($userReview->user_id);
                	$vendor = \Modules\User\Entities\User::find($userReview->vendor_id);
                }
                if(isset($reviewer->email) && !empty($reviewer->email)){
                    $emailReviewer = $reviewer->email;
                }
                if(isset($reporter->email) && !empty($reporter->email)){
                    $emailReporter = $reporter->email;
                }
                if(isset($vendor->email) && !empty($vendor->email)){
                    $emailVendor = $vendor->email;
                }
                $title = '';
                if($review->reason)
                {
                    $title = $review_reason[$review->reason];
                }
                $content = $review->content;
                foreach ($reportReviewTitle as $keyTitleReport => $valueTitleReport) {
                    if(is_numeric(strpos($review->content, $valueTitleReport )))
                    {
                        $result = explode ( $valueTitleReport , $review->content);
                        $title = $valueTitleReport;
                        $content1 = str_replace( '/n', '', $result[1] );
                        $content2 = str_replace( 'Optional("', '', $content1 );
                        $content = str_replace( '")', '', $content2 );
                        
                    }
                }
            ?>
				<tr>
					<td>{{ $emailReviewer }}</td>
                    <td>{{ $emailReporter }}</td>
                    <td>{{ $emailVendor }}</td>
                     <td>{{ str_limit($title, 50) }}</td>
                    <td>{{ str_limit($content, 50) }}</td>
                    <td>{{ $review->created_at && $review->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i:s') : "" }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>