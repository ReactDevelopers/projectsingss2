<div class="messages">
	<?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

</div>
<form class="form-horizontal" role="employer_step_two" action="<?php echo e(url(sprintf('%s/hire/talent/process/five',EMPLOYER_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		<?php echo e(csrf_field()); ?>

		<div class="form-group">
			<div class="col-md-12">
				<h4 class="form-sub-heading"><?php echo e(trans('website.W0281')); ?></h4>
				<div class="custom-dropdown">
					<select name="subindustry[]" style="max-width: 400px;"  class="form-control" data-request="tags-true" multiple="true"  data-placeholder="<?php echo e(trans('website.W0799')); ?>">
						<?php echo ___dropdown_options(array_combine(array_column($subindustries_name,'name'), array_column($subindustries_name,'name')),sprintf(trans('website.W0060'),trans('website.W0068')),array_column(array_column($project['subindustries'], 'subindustries'), 'name'),false); ?>

					</select>
					<div class="js-example-tags-container white-tags"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="login-inner-wrapper">
		<h4 class="form-sub-heading"><?php echo e(trans('website.W0280')); ?></h4>
		<div class="form-group">
			<div class="">
				<ul class="filter-list-group">
					<?php $__currentLoopData = expertise_levels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
						<li class="col-md-4">
							<div class="checkbox radio-checkbox">                
								<input type="radio" id="expertise-<?php echo e($value['level']); ?>" name="expertise" value="<?php echo e($value['level']); ?>" data-action="filter" <?php if(($project['expertise'] == $value['level']) || (empty($project['expertise']) && $value['level'] == 'novice')): ?> checked="checked" <?php endif; ?>>
								<label for="expertise-<?php echo e($value['level']); ?>"><span class="check"></span><?php echo e($value['level_name']); ?> <?php echo e($value['level_exp']); ?></label>
							</div>
						</li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
				</ul>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<label class="control-label"><?php echo e(trans('website.W0658')); ?></label>
				<input type="text" name="other_perks" maxlength="4" placeholder="<?php echo e(trans('website.W0074')); ?>" value="<?php echo e($project['other_perks']); ?>" style="max-width: 400px;" class="form-control">
				
			</div>
		</div>
		<input type="hidden" name="id_project" value="<?php echo e($project['id_project']); ?>">
		<input type="hidden" name="industry_id" value="<?php echo e(current($project['industries'])['industries']['id_industry']); ?>">
		<input type="text" class="hide" name="talent_id" value="<?php echo e($talent_id); ?>">
		<input type="text" class="hide" name="action" value="<?php echo e($action); ?>">
		<div class="clearfix"></div>
	</div>

	<div class="form-group button-group">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-7 col-sm-7 col-xs-6">
					<?php if(in_array('two',$steps)): ?>
                        <a href="<?php echo e(url(sprintf("%s/hire/talent/{$action_url}%s{$project_id_postfix}",EMPLOYER_ROLE_TYPE,$steps[count($steps)-2]))); ?>" class="greybutton-line"><?php echo e(trans('website.W0196')); ?></a>
                    <?php endif; ?>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-6">
					<button type="button" class="button" data-request="ajax-submit" data-target='[role="employer_step_two"]'>
						<?php echo e(trans('website.W0229')); ?>

					</button>
				</div>
			</div>
		</div>
	</div>
</form>
