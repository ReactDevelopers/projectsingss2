{!! ___getmenu('talent-mynetwork','<span class="hide">%s</span><ul class="user-profile-links user_submenu">%s</ul>','active',true,true) !!}
@push('inlinescript')
<script type="text/javascript">
	$(document).ready(function(){
		var menu1 = '';
		var menu2 = '';
		menu1 = "{{Request::segment(3)}}";
		menu2 = "{{Request::segment(4)}}";
		if(menu1 == 'mynetworks' && menu2==''){
			$('.user_submenu li').removeClass('active');
		}
	});
</script>
@endpush