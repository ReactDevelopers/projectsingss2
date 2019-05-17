<?php echo $__env->make('employer.job.includes.talent-profile-menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php if(!empty($connected_user)): ?>
	<?php $__currentLoopData = $connected_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
		<div class="content-box find-job-listing clearfix">
			<div class="">
				<div class="content-box-header clearfix">
					<img src="<?php echo e($value->get_profile); ?>" alt="profile" class="job-profile-image"><div class="contentbox-header-title">
						<h3>
							<a href="<?php echo e(url('employer/find-talents/profile?talent_id='.___encrypt($value->user->id_user))); ?>"><?php echo e($value->user->name); ?></a>
						</h3>
						<?php if($value->user->country!=null): ?>
							<span class="company-name"><?php echo e($countries[$value->user->country]); ?></span>
						<?php endif; ?>

						<?php if(count($value->industry) > 0): ?>
							<span class=""><?php echo e($value->industry[0]['name']); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<?php else: ?>
<div class="content-box find-job-listing clearfix">
	<div class="">
		<div class="content-box-header clearfix">
			<p>
				No Records Found
			</p>
		</div>
	</div>
</div>
<?php endif; ?>


