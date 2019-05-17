<?php echo ___getmenu('talent-mynetwork','<span class="hide">%s</span><ul class="user-profile-links user_submenu">%s</ul>','active',true,true); ?>

<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		var menu1 = '';
		var menu2 = '';
		menu1 = "<?php echo e(Request::segment(3)); ?>";
		menu2 = "<?php echo e(Request::segment(4)); ?>";
		if(menu1 == 'mynetworks' && menu2==''){
			$('.user_submenu li').removeClass('active');
		}
	});
</script>
<?php $__env->stopPush(); ?>