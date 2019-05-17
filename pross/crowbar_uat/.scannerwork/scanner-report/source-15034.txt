<div class="content-box find-job-listing clearfix owner-ship-list">
	<div class="find-job-left">
		<div class="content-box-header clearfix">
			<img src="{{$talent['picture']}}" alt="profile" class="job-profile-image"><div class="contentbox-header-title">
				<h3>
					<a href="javascript:void(0);">{{$talent['name']}}</a>
				</h3>
				<span class="company-name">{{$talent['country_name']}}</span>
			</div>
		</div>
	</div>
	<div class="find-job-right">
		<div class="contentbox-price-range">
			<a href="javascript:void(0);" class="button" data-target="#add-member" data-request="ajax-modal" data-url="{{ url(sprintf('%s/confirm-transfer-ownership?id=%s',TALENT_ROLE_TYPE,$talent['id_user'])) }}" href="javascript:void(0);">
                {{trans('website.W0977')}}
            </a>
		</div>
	</div>
</div>
																																																																																																																																																																																																																																																																																																																																																																																	