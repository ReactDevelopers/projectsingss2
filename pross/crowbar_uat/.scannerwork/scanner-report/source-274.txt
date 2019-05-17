<div class="grid-item2">
	<div class="events_desc">
		<h2><span href="javascript:void(0);"  class="form-heading"><?php echo e($event->event_title); ?></span></h2>
		<div class="events_listing">
			<label><?php echo e(trans('website.W0902')); ?></label>
			<span><?php echo e(date('d M Y',strtotime($event->event_date))); ?> <?php echo e(date('H:i',strtotime($event->event_time))); ?></span>
		</div>

		<?php if($event->event_type == "virtual"): ?>
			<div class="events_listing">
				<label><?php echo e(trans('website.W0903')); ?></label>
				<span><a href="<?php echo e($event->video_url); ?>" target="_blank"><?php echo e($event->video_url); ?></a></span>
			</div>
		<?php else: ?>
			<div class="events_listing">
				<label><?php echo e(trans('website.W0904')); ?></label>
				<span><?php echo e($event->location); ?>, <?php echo e($event->city); ?>, <?php echo e($event->state); ?>, <?php echo e($event->country); ?></span>
			</div>
		<?php endif; ?>

		<div class="events_listing">
			<label><?php echo e(trans('website.W0905')); ?></label>
			<span><?php echo e($event->total_attending); ?> <?php echo e(trans('website.W0887')); ?> (<?php echo e($event->in_circle_attending); ?> <?php echo e(trans('website.W0931')); ?>)</span>
		</div>

		<?php if(!empty($event->image)): ?>
			<div class="uploaded_banner">
				<img src="<?php echo e($event->image); ?>"/>
			</div>
		<?php endif; ?>

		<p><?php echo e($event->event_description); ?></p>
	</div>
	<div class="social_listing">
		<ul>
			<?php if($event->rsvp_response_status != 'yes' ): ?>
				<li id="rsvp-<?php echo e($event->id_events); ?>">
					<a href="javascript:void(0);" data-request="add-rsvp" data-toremove="rsvp" data-data_id="<?php echo e($event->id_events); ?>" data-url="<?php echo e(url(sprintf('%s/addRsvp?event_id=%s',TALENT_ROLE_TYPE,$event->id_events))); ?>" data-user="<?php echo e($event->id_events); ?>" data-ask="<?php echo e(trans('website.W0911')); ?>" class="rsvp_icon">
						<?php echo e(trans('website.W0906')); ?> 
						<img src="<?php echo e(asset('images/rsvp.png')); ?>">
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="javascript:void(0);" class="invite_icon" data-target="#add-member" data-request="ajax-modal" data-url="<?php echo e(url(sprintf('%s/invite-member?event_id=%s',TALENT_ROLE_TYPE,$event->id_events))); ?>" href="javascript:void(0);">
					<?php echo e(trans('website.W0907')); ?>

					<img src="<?php echo e(asset('images/invite_member.png')); ?>">
				</a>
			</li>
			<li class="social_listing_links">
				<div class="dropdown socialShareDropdown">
					<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false"><?php echo e(trans('website.W0908')); ?></a>
					<ul class="dropdown-menu">
						<li>
							<a href="javascript:void(0);" class="linkdin_icon">
								<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
								<script type="IN/Share" data-url="<?php echo e(url('/mynetworks/eventsdetail/'.$event->id_events)); ?>"></script>
								<img src="<?php echo e(asset('images/linkedin.png')); ?>">
							</a>
						</li>
						<li>
							<a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(url('/mynetworks/eventsdetail/'.$event->id_events)); ?>" target="_blank">
								<img src="<?php echo e(asset('images/facebook.png')); ?>">
							</a>
							
						</li>
						<li>
							<a  target="_blank" href="https://twitter.com/share?url=<?php echo e(url('/mynetworks/eventsdetail/'.$event->id_events)); ?>" class="twiter_icon">
								<img src="<?php echo e(asset('images/twitter.png')); ?>">
							</a>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0)" class="events_book bookmark_icon <?php if($event->saved_bookmark): ?> == 1) active <?php endif; ?>"  data-request="favorite-event" data-url="<?php echo e(url(sprintf('%s/fav-event?event_id=%s',TALENT_ROLE_TYPE,$event->id_events))); ?>"><?php echo e(trans('website.W0909')); ?>

				</a>
			</li>
		</ul>
	</div>
</div>