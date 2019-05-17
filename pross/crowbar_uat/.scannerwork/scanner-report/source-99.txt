<form role="settings" method="POST" action="<?php echo e(url(sprintf('%s/connect-with-talent',TALENT_ROLE_TYPE))); ?>" class="form-horizontal" autocomplete="off">
    <?php echo e(csrf_field()); ?>

    <div class="login-inner-wrapper setting-wrapper">
        
        <div class="message">
            <?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

        </div>
        <div class="forgot-password-inner">
            <div class="form-group has-feedback toggle-social<?php echo e($errors->has('invite_code') ? ' has-error' : ''); ?>">
                <label class="control-label col-md-8 col-sm-12 col-xs-12"><?php echo e(trans('website.W0987')); ?></label>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <input name="invite_code" value="<?php echo e(old('invite_code')); ?>" type="text" class="form-control" placeholder="<?php echo e(trans('website.W0987')); ?>" />
                    <?php if($errors->has('invite_code')): ?>
                        <span class="help-block"><?php echo e($errors->first('invite_code')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">
                <div class="col-md-5 col-sm-5 col-xs-6">
                    <button type="button" data-request="ajax-submit" data-target='[role="settings"]' class="btn btn-sm redShedBtn pull-right"><?php echo e(trans('website.W0058')); ?></button>
                </div>
            </div>      
        </div>
    </div>
</form>