<form class="form-horizontal" role="employer_step_two" action="<?php echo e(url(sprintf('%s/hire/talent/process/two',EMPLOYER_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		<?php echo e(csrf_field()); ?>

		<div class="form-group">
			<div class="col-md-12">
				<h4 class="form-sub-heading"><?php echo e(sprintf(trans('website.W0655'),'')); ?></h4>
				<div class="custom-dropdown single-tag-selection">
					<select name="industry[]" style="max-width: 400px;" class="form-control" data-request="single-tags" data-placeholder="<?php echo e(trans('website.W0644')); ?>">
						<?php echo ___dropdown_options(___cache('industries_name'),sprintf(trans('website.W0060'),trans('website.W0068')),array_column(array_column($project['industries'], 'industries'), 'id_industry'),false); ?>

					</select>
					<div class="js-example-tags-container white-tags"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="login-inner-wrapper">
		<div class="form-group">
			<div class="col-md-12">
				<h4 class="form-sub-heading"><?php echo e(trans('website.W0421')); ?></h4>
				<div class="skills-filter">
					<div class="custom-dropdown">
						<select id="skills" name="required_skills[]" style="max-width: 400px;" class="filter form-control" data-request="tags" multiple="true" data-placeholder="<?php echo e(trans('website.W0798')); ?>">
							<?php echo ___dropdown_options(___cache('skills'),'',array_column(
								array_column(
									json_decode(
										json_encode(
											$project['skills']
										),true
									),'skills'
								),'skill_name'
							),false); ?>

						</select>
						<div class="js-example-tags-container white-tags"></div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_project" value="<?php echo e($project['id_project']); ?>">
		<input type="text" class="hide" name="talent_id" value="<?php echo e($talent_id); ?>">
		<input type="text" class="hide" name="action" value="<?php echo e($action); ?>">
	</div>                  
	<div class="form-group button-group">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-5 col-sm-5 col-xs-6">
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