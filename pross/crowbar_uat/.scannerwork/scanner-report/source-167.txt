<div>
    <ul class="user-profile-links">
        <li class="resp-tab-item">
            <a href="<?php echo e(url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0678')); ?>

            </a>
        </li>
        <li>
            <a href="<?php echo e(url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0679')); ?>

            </a>
        </li>
        <li class="active">
            <a href="<?php echo e(url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0680')); ?>

            </a>
        </li>
        <?php if(!empty($project->reviews_count)): ?>
            <li class="resp-tab-item">
                <a href="<?php echo e(url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))); ?>">
                    <?php echo e(trans('website.W0721')); ?>

                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="inner-profile-section">
            <div class="form-group clearfix">
                <label class="control-label"><?php echo e(trans('website.W0248')); ?></label>
                    <?php if(!empty($project->employer->contact_person_name)): ?>
                        <br><?php echo e($project->employer->contact_person_name); ?>

                    <?php else: ?>
                        <br><?php echo e(N_A); ?>

                    <?php endif; ?>
            </div>
            <div class="form-group clearfix">
                <label class="control-label"><?php echo e(trans('website.W0249')); ?></label>
                <?php if(!empty($project->employer->company_website)): ?>
                    <br><a target="_blank"><?php echo e($project->employer->company_website); ?></a>
                <?php else: ?>
                    <br><?php echo e(N_A); ?>

                <?php endif; ?>
            </div>
            <div class="form-group clearfix">
                <label class="control-label"><?php echo e(trans('website.W0787')); ?></label>
                <?php if(!empty($project->employer->company_work_field)): ?>
                    <br><?php echo e(___cache("work_fields",$project->employer->company_work_field)); ?>

                <?php else: ?>
                    <br><?php echo e(N_A); ?>

                <?php endif; ?>
            </div>            
            <div class="form-group clearfix">
                <label class="control-label"><?php echo e(trans('website.W0800')); ?></label>
                <?php if(!empty($project->employer->company_biography)): ?>
                    <br><?php echo e(nl2br($project->employer->company_biography)); ?>

                <?php else: ?>
                    <br><?php echo e(N_A); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>