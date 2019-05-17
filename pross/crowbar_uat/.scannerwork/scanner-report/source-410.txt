<form role="change-password" method="POST" action="<?php echo e(url(sprintf('%s/__change-password',TALENT_ROLE_TYPE))); ?>" class="form-horizontal" autocomplete="off">
    <?php echo e(csrf_field()); ?>

    <div class="login-inner-wrapper setting-wrapper">
        <p class="p-b-15"><?php echo e(trans('website.W0714')); ?></p>
        <div class="message">
            <?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

        </div>
        <div class="forgot-password-inner">
            <?php if($user['social_account'] !== DEFAULT_YES_VALUE): ?>
                <div class="form-group has-feedback toggle-social<?php echo e($errors->has('old_password') ? ' has-error' : ''); ?>">
                    <label class="control-label col-md-8 col-sm-12 col-xs-12"><?php echo e(trans('website.W0303')); ?></label>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <input name="old_password" value="<?php echo e(old('old_password')); ?>" type="password" class="form-control" placeholder="<?php echo e(trans('website.W0303')); ?>"/>
                        <?php if($errors->has('old_password')): ?>
                            <span class="help-block"><?php echo e($errors->first('old_password')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
                
            <div class="form-group has-feedback toggle-social<?php echo e($errors->has('new_password') ? ' has-error' : ''); ?>">
                <label class="control-label col-md-8 col-sm-12 col-xs-12"><?php echo e(trans('website.W0304')); ?></label>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <input name="new_password" value="<?php echo e(old('new_password')); ?>" type="password" class="form-control" placeholder="<?php echo e(trans('website.W0304')); ?>" />
                    <?php if($errors->has('new_password')): ?>
                        <span class="help-block"><?php echo e($errors->first('new_password')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">                                        
                <div class="col-md-5 col-sm-5 col-xs-6">
                    <button type="button" data-request="ajax-submit" data-target='[role="change-password"]' class="btn btn-sm redShedBtn pull-right"><?php echo e(trans('website.W0058')); ?></button>
                </div>
            </div>
        </div>
    </div>                                
</form>

<?php $__env->startPush('inlinescript'); ?>
    <link href="<?php echo e(asset('css/hidePassword.css')); ?>" rel="stylesheet">
    <script type="text/javascript" src="<?php echo e(asset('js/hideShowPassword.js')); ?>"></script>
    <script type="text/javascript">$('[name="new_password"]').hidePassword(true);</script>
<?php $__env->stopPush(); ?>