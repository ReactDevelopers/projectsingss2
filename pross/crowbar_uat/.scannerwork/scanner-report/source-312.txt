<?php  
	$header = 'innerheader';
	$footer = 'footer';
	$settings = \Cache::get('configuration');
 ?>

<?php $__env->startSection('content'); ?>
<div class="contentWrapper socialEventDetails">
	<div class="container">
		<div class="vertual_events-wrapper clearfix">
		    <div class="datatable-listing events_details grid2">
		    	<table class="table">
		    		<tr>
		    			<td>
							<div class="grid-item2">
								<div class="events_desc">
									<h2><a href="javascript:void(0);"  class="form-heading"><?php echo e($event['event_title']); ?></a></h2>
									<div class="events_listing">
										<label><?php echo e(trans('website.W0902')); ?></label>
										<span><?php echo e(date('d M Y',strtotime($event['event_date']))); ?> <?php echo e(date('H:i',strtotime($event['event_time']))); ?></span>
									</div>

									<?php if($event['event_type'] == "virtual"): ?>
										<div class="events_listing">
											<label><?php echo e(trans('website.W0903')); ?></label>
											<span><a href="<?php echo e($event['video_url']); ?>" target="_blank"><?php echo e($event['video_url']); ?></a></span>
										</div>
									<?php else: ?>
										<div class="events_listing">
											<label><?php echo e(trans('website.W0904')); ?></label>
											<span><?php echo e($event['location']); ?>, <?php echo e($event['city_name']['city_name']); ?>, <?php echo e($event['state_name']['state_name']); ?>, <?php echo e($event['country_name']['country_name']); ?></span>
										</div>
									<?php endif; ?>

									<?php if(!empty($event['file'])): ?>
										<div class="uploaded_banner">
										<?php 
											$base_url = ___image_base_url();
										 ?>
										<img src="<?php echo e($base_url.$event['file']['folder'].$event['file']['filename']); ?>"/>
										</div>
									<?php endif; ?>
									<p><?php echo e($event['event_description']); ?></p>
								</div>
							</div>
		    			</td>
		    		</tr>
		    	</table>
		    </div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>