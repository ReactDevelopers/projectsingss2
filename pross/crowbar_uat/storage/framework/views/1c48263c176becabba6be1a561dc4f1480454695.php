<div class="login-inner-wrapper" id="events-<?php echo e($id_events); ?>">
	<div class="row">
		<div class="col-md-10 col-sm-10 col-xs-9">
			<h6 class="form-heading no-padding"><?php echo e($event_title); ?></h6>
		</div>
		<div class="col-md-2 col-sm-2 col-xs-3 edit_del_icon">
			<a href="<?php echo e(url(sprintf('%s/network/edit-event',TALENT_ROLE_TYPE)).'?id_events='.___encrypt($id_events)); ?>"><img src="<?php echo e(asset('images/edit-icon.png')); ?>"></a>
			<a href="javascript:void(0);" data-url="<?php echo e(url(sprintf('%s/delete-event',TALENT_ROLE_TYPE)).'?id_events='.$id_events); ?>" data-single="true" data-after-upload=".single-remove" data-toremove="events" title="Delete" data-request="delete" data-event_id="<?php echo e($id_events); ?>" data-delete-id="event_id" data-edit-id="event_id" class="delete-attachment c-p" data-ask="Do you really want to delete the event?"><img src="<?php echo e(asset('images/delete-icon.png')); ?>"></a>
		</div>
	</div>
</div>