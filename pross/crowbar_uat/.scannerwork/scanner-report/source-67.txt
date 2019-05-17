<?php echo $__env->make('employer.viewprofile.includes.sidebar-tabs',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>                    
<div class="inner-profile-section">
    <div class="view-information no-padding">
        <h2><?php echo e(trans('website.W0269')); ?></h2>
    </div>
    <div class="view-information white-wrapper">
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0247')); ?></label>
            <?php if(!empty($user['company_profile'])): ?>
                <label class="info-label col-md-8"><?php echo e(ucfirst($user['company_profile'])); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0096')); ?></label>
            <?php if(!empty($user['company_name'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['company_name']); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <?php if($user['company_profile'] == 'individual'): ?>
            <div class="form-group clearfix">
                <label class="control-label col-md-4"><?php echo e(trans('website.W0200')); ?></label>
                <?php if(!empty($user['company_work_field'])): ?>
                    <label class="info-label col-md-8"><?php echo e(___cache("skills",$user['company_work_field'])); ?></label>
                <?php else: ?>
                    <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
                <?php endif; ?>
            </div>
            <?php if(0): ?>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4"><?php echo e(trans('website.W0251')); ?></label>
                    <div class="col-md-8 js-example-tags-container">
                        <?php if(!empty($user['certificates'])): ?>
                            <ul><?php echo ___tags($user['certificates'],'<li class="tag-selected"><a href="'.url(sprintf('%s/profile/edit/setup',EMPLOYER_ROLE_TYPE)).'" class="destroy-tag-selected">×</a>%s</li>',' '); ?></ul>
                        <?php else: ?>
                            <?php echo e(N_A); ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="form-group clearfix">
                <label class="control-label col-md-4"><?php echo e(trans('website.W0248')); ?></label>
                <?php if(!empty($user['contact_person_name'])): ?>
                    <label class="info-label col-md-8"><?php echo e($user['contact_person_name']); ?></label>
                <?php else: ?>
                    <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
                <?php endif; ?>
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-md-4"><?php echo e(trans('website.W0249')); ?></label>
                <?php if(!empty($user['company_website'])): ?>
                    <label class="info-label col-md-8"><?php echo e($user['company_website']); ?></label>
                <?php else: ?>
                    <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
                <?php endif; ?>
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-md-4"><?php echo e(trans('website.W0200')); ?></label>
                <?php if(!empty($user['company_work_field'])): ?>
                    <label class="info-label col-md-8"><?php echo e(___cache("skills",$user['company_work_field'])); ?></label>
                <?php else: ?>
                    <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
                <?php endif; ?>
            </div>            
            <div class="form-group clearfix">
                <label class="control-label col-md-4"><?php echo e(trans('website.W0252')); ?></label>
                <?php if(!empty($user['company_biography'])): ?>
                    <label class="info-label col-md-8"><?php echo e($user['company_biography']); ?></label>
                <?php else: ?>
                    <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="view-information">
        <h2><?php echo e(trans('website.W0197')); ?><a href="<?php echo e(url(sprintf('%s/profile/edit/general',EMPLOYER_ROLE_TYPE))); ?>" title="Edit" class="edit-me hide"><img src="<?php echo e(asset('images/edit-icon.png')); ?>" /></a></h2>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0122')); ?></label>
            <?php if(!empty($user['mobile'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['country_code'].' '.$user['mobile']); ?><?php if($user['is_mobile_verified'] == DEFAULT_YES_VALUE): ?><img src="<?php echo e(asset('images/completed-step.png')); ?>" alt="verified" /> <?php endif; ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0245')); ?></label>
            <?php if(!empty($user['other_mobile'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['other_country_code'].' '.$user['other_mobile']); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0246')); ?></label>
            <?php if(!empty($user['website'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['website']); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0054')); ?></label>
            <?php if(!empty($user['address'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['address']); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(sprintf(trans('website.W0055'),'')); ?></label>
            <?php if(!empty($user['country'])): ?>
                <label class="info-label col-md-8"><?php echo e(!empty(\Cache::get('countries')[$user['country']]) ? \Cache::get('countries')[$user['country']] : N_A); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(sprintf(trans('website.W0056'),'')); ?></label>
            <?php if(!empty($user['state'])): ?>
                <label class="info-label col-md-8"><?php echo e(\Cache::get('states')[$user['state']]); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4"><?php echo e(trans('website.W0057')); ?></label>
            <?php if(!empty($user['postal_code'])): ?>
                <label class="info-label col-md-8"><?php echo e($user['postal_code']); ?></label>
            <?php else: ?>
                <label class="info-label col-md-8"><?php echo e(N_A); ?></label>
            <?php endif; ?>
        </div>
    </div>
</div>