<form class="form-horizontal" role="employer_step_two" action="<?php echo e(url(sprintf('%s/profile/process/one?redirect=%s',EMPLOYER_ROLE_TYPE,\Request::get('redirect')))); ?>" method="post" accept-charset="utf-8">
    <div class="inner-profile-section">                        
        <div class="login-inner-wrapper edit-inner-wrapper">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="step_type" value="edit">
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0492')); ?></label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?php $__currentLoopData = company_profile(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <div class="radio radio-inline">
                            <input data-request="show-hide" data-condition="company" data-target="[name='company_profile']" data-true-condition=".company-section" data-false-condition=".normal-section" name="company_profile" value="<?php echo e($item['level']); ?>" type="radio" id="<?php echo e($item['level']); ?>" <?php if(old('company_profile',$user['company_profile']) == $item['level']): ?> <?php echo e('checked="checked"'); ?> <?php endif; ?>>
                            <label for="<?php echo e($item['level']); ?>"> <?php echo e($item['level_name']); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0096')); ?></label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" name="company_name" placeholder="<?php echo e(trans('website.W0097')); ?>" class="form-control" value="<?php echo e(old('company_name',$user['company_name'])); ?>"/>
                </div>
            </div>        
            <div class="company-section">                       
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0248')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="contact_person_name" placeholder="<?php echo e(trans('website.W0493')); ?>" class="form-control"  value="<?php echo e(old('contact_person_name',$user['contact_person_name'])); ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0249')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="company_website" placeholder="<?php echo e(trans('website.W0494')); ?>" class="form-control"  value="<?php echo e(old('company_website',$user['company_website'])); ?>"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0924')); ?></label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="custom-dropdown">
                        <select class="form-control" name="company_work_field">
                            <?php echo ___dropdown_options(___cache('skills'), trans('website.W0924'),$user['company_work_field'],false); ?>

                        </select>
                    </div>
                </div>
            </div>
            <?php if(0): ?>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0788')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="skills-filter">
                            <div class="custom-dropdown">
                                <select id="certificates" name="certificates[]" class="filter form-control" data-request="tags-true" multiple="true" data-placeholder="<?php echo e(trans('website.W0080')); ?>">
                                    <?php echo ___dropdown_options(___cache('certificates'),'',$user['certificates'],false); ?>

                                </select>
                                <div class="js-example-tags-container white-tags"></div>
                            </div>
                        </div>           
                    </div>           
    
                </div>
            <?php endif; ?>
            <div class="company-section">
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0252')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <textarea name="company_biography" class="form-control" placeholder="<?php echo e(trans('website.W0497')); ?>"><?php echo e(old('company_biography',$user['company_biography'])); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>                    
    <div class="row form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">                                    
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <button type="button" class="button" value="Submit" data-request="ajax-submit" data-target='[role="employer_step_two"]'><?php echo e(trans('website.W0058')); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">.modal-backdrop{display: none;}#SGCreator-modal{background: rgba(216, 216, 216, 0.7);}</style>

    <script type="text/javascript" src="https://dev.doctoranywhere.com/js/webcam.js"></script>
 
    
<?php $__env->stopPush(); ?>
