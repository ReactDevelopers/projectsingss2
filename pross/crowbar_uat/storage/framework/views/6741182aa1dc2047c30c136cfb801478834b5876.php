<?php echo $__env->make('talent.viewprofile.includes.sidebar-tabs',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>                    
<div class="login-inner-wrapper profile-info-details">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0030')); ?> 
            <a href="<?php echo e(url(sprintf('%s/profile/step/two',TALENT_ROLE_TYPE))); ?>" title="Edit">
                <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
            </a>
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
        <?php if(!empty($country) && $payout_mgmt_is_registered=='yes'): ?>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0485').' '. array_column($user['industry'],'name')[0]); ?> 
            <a href="<?php echo e(url(sprintf('%s/profile/step/two',TALENT_ROLE_TYPE))); ?>" title="Edit">
                <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
            </a>
        </h2>
            <div class="form-group clearfix m-b-15">
                <label class="info-label">
                    <?php 
                    $is_register = $user['is_register'] == 'Y' ? 'Yes' : 'No';
                     ?>
                    <?php if(!empty($user['is_register'])): ?>
                        <?php echo ___tags($is_register,'<span class="small-tags">%s</span>',''); ?>

                    <?php else: ?>
                        <?php echo e(N_A); ?>

                    <?php endif; ?>
                </label>
            </div>

            <?php if($is_register == 'Yes' && !empty($user['identification_no'])): ?>
                <h2 class="form-heading bold-heading">
                    <?php echo e(trans('website.W0941')); ?> 
                    <a href="<?php echo e(url(sprintf('%s/profile/step/two',TALENT_ROLE_TYPE))); ?>" title="Edit">
                        <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
                    </a>
                </h2>
                <div class="form-group clearfix m-b-15">
                    <label class="info-label">
                        <?php if(!empty($user['identification_no'])): ?>
                            <?php echo ___tags($user['identification_no'],'<span class="small-tags">%s</span>',''); ?>

                        <?php else: ?>
                            <?php echo e(N_A); ?>

                        <?php endif; ?>
                    </label>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0206')); ?> 
            <a href="<?php echo e(url(sprintf('%s/profile/step/two',TALENT_ROLE_TYPE))); ?>" title="Edit">
                <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
            </a>
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
            <a href="<?php echo e(url(sprintf('%s/profile/step/three',TALENT_ROLE_TYPE))); ?>" title="Edit">
                <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
            </a>
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
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0663')); ?> 
            <a href="<?php echo e(url(sprintf('%s/profile/step/three',TALENT_ROLE_TYPE))); ?>" title="Edit">
                <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
            </a>
        </h2>
        <div class="form-group clearfix">
            <?php 
                if(!empty($user['certificate_attachments'])){
                    foreach ($user['certificate_attachments'] as $item) {
                        $url_delete = sprintf(
                            url('ajax/%s?id_file=%s'),
                            DELETE_DOCUMENT,
                            $item['id_file']
                        );
                        echo sprintf(RESUME_TEMPLATE,
                            $item['id_file'],
                            url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                            asset('/'),
                            substr($item['filename'],0,3),
                            $item['size'],
                            $url_delete,
                            $item['id_file'],
                            asset('/')
                        );  
                    }
                }else{
                    echo N_A;
                }
             ?>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">
                <?php echo e(trans('website.W0032')); ?>

                <a href="<?php echo e(url(sprintf('%s/profile/step/five',TALENT_ROLE_TYPE))); ?>" title="Edit">
                    <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
                </a>
            </h2>
            <?php if(isset($user['talentCompany'])): ?>
                <h6 class="">Current Employment: <?php echo e(@$user['talentCompany']->company_name); ?> <?php echo e((@$user['notice_expired']!=null) ? '(On Notice Period - End on '.(@$user['notice_expired']).')': ''); ?></h6>
            <?php endif; ?>
            <div class="form-group clearfix">

                <div class="work-experience-box row">
                    <?php if ($__env->exists('talent.profile.includes.workexperience',['work_experience_list' => $user['work_experiences']])) echo $__env->make('talent.profile.includes.workexperience',['work_experience_list' => $user['work_experiences']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading"><?php echo e(trans('website.W0172')); ?>

                <a href="<?php echo e(url(sprintf('%s/profile/step/five',TALENT_ROLE_TYPE))); ?>" title="Edit">
                    <img height="12" src="<?php echo e(asset('images/edit-icon.png')); ?>" />
                </a>
            </h2>
            <div class="form-group clearfix">
                <div class="education-box row">
                    <?php if ($__env->exists('talent.profile.includes.education', ['education_list' => $user['educations']])) echo $__env->make('talent.profile.includes.education', ['education_list' => $user['educations']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>     
<?php $__env->startPush('inlinescript'); ?>
    <style>.education-box .edit-icon, .work-experience-box .edit-icon{display: none!important;}.m-t-35 .educationEditSec{margin-bottom: 15px;}</style>
<?php $__env->stopPush(); ?>