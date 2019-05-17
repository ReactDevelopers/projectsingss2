<div class="login-inner-wrapper profile-info-details margin-left-none margin-top-none">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0030')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty(array_column($user['industry'],'name'))): ?>
                    <?php echo ___tags(array_column($user['industry'],'name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0206')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty($user['skills'])): ?>
                    <?php echo ___tags(array_column($user['skills'], 'skill_name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0207')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty(array_column($user['subindustry'],'name'))): ?>
                    <?php echo ___tags(array_column($user['subindustry'],'name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">
                <?php echo e(trans('website.W0032')); ?>

            </h2>
            <div class="form-group clearfix">
                <div class="work-experience-box row">
                    <?php if ($__env->exists('talent.viewtalent.includes.workexperience',['work_experience_list' => $user['work_experiences']])) echo $__env->make('talent.viewtalent.includes.workexperience',['work_experience_list' => $user['work_experiences']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading"><?php echo e(trans('website.W0172')); ?>

            </h2>
            <div class="form-group clearfix">
                <div class="education-box row">
                    <?php if ($__env->exists('talent.viewtalent.includes.education', ['education_list' => $user['educations']])) echo $__env->make('talent.viewtalent.includes.education', ['education_list' => $user['educations']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>     
<?php $__env->startPush('inlinescript'); ?>
    <style>.education-box .edit-icon, .work-experience-box .edit-icon{display: none!important;}.m-t-35 .educationEditSec{margin-bottom: 15px;}</style>
<?php $__env->stopPush(); ?>