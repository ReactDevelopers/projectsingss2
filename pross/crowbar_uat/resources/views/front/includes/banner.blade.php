@if(!empty($banner))
	<div class="bannerSection" style='background-image:url("{{ asset('uploads/banner/resize/'.$banner->banner_image) }}")'></div>
@else
	<div class="bannerSection howItWorkBanner"></div>
@endif
