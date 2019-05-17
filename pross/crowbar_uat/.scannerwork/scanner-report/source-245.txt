<form class="form-horizontal" role="employer_step_two" action="<?php echo e(url(sprintf('%s/hire/talent/process/three',EMPLOYER_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		<?php echo e(csrf_field()); ?>

		<div class="row">
			<div class="messages"></div>
		</div>
		<h4 class="form-sub-heading"><?php echo e(sprintf(trans('website.W0661'),'')); ?></h4>
		<div class="form-group step-three">
			<div class="">
				<div class="col-md-3">  
					<label class="control-label"><?php echo e(trans('website.W0286')); ?></label>
				</div>
				<div class="col-md-4">
					<label class="control-label"><?php echo e(trans('website.W0660')); ?></label>
				</div>
			</div>
			<div class="col-md-12">
				<ul class="filter-list-group clear-list">
					<?php $__currentLoopData = employment_types('web_post_job'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
						<li>
							<div class="row">
								<div class="col-md-3">                
									<div class="checkbox radio-checkbox">                
										<input type="radio" id="employement-<?php echo e($value['type']); ?>" name="employment" value="<?php echo e($value['type']); ?>" <?php if(($project['employment'] == $value['type']) || (empty($project['employment']) && $value['type'] == 'hourly')): ?> checked="checked" <?php endif; ?> data-request="focus-input">
										<label for="employement-<?php echo e($value['type']); ?>"><span class="check"></span> <?php echo e(strtolower($value['type_name'])); ?></label>
									</div>
								</div>
								<div class="col-md-4">
									<div class="price-range">
		                                <div class="leftLabel form-control">
		                                    <input type="text" name="price[]" class="form-control text-left" <?php if($project['employment'] == $value['type']): ?> value="<?php echo e($project['price']); ?>" <?php endif; ?> data-request="focus-input">
		                                </div>
		                            </div>
								</div>
							</div>
						</li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
				</ul>
			</div>
		</div>
		<input type="hidden" name="id_project" value="<?php echo e($project['id_project']); ?>">
		<input type="text" class="hide" name="talent_id" value="<?php echo e($talent_id); ?>">
		<input type="text" class="hide" name="action" value="<?php echo e($action); ?>">
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
						<?php echo e(trans('website.W0659')); ?>

					</button>
				</div>
			</div>
		</div>
	</div>
</form>
<?php $__env->startPush('inlinecss'); ?>
	<style type="text/css">
        .price-range .form-control{
            padding-left: 28px;
        }
        .price-range .form-control.text-left{
        	padding:0;
        	width:100%;
        }
        .price-range .form-control::before{
        	top:4px;
            content: "<?php echo e(___cache('currencies')[\Session::get('site_currency')]); ?>";
        }
        .leftLabel.form-control {
		    width: 200px;
		}
    </style>
<?php $__env->stopPush(); ?>