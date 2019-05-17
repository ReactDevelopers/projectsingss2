<div class="col-md-8 col-sm-8 col-xs-12 no-padding-xs">
    <div class="login-inner-wrapper setting-wrapper social-connect">
        <p class="p-b-15"><?php echo e(trans('website.W0666')); ?></p>
        <div class="message">
            <?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

        </div>
        <div class="row form-group">
            <div class="col-md-7 col-sm-8 col-xs-12 social-link-wrapper">
                <label class="control-label">
                    <img src="<?php echo e(asset('images/instagram.png')); ?>" />&nbsp;&nbsp;
                    <span class="social-type-name">
                        <?php echo e(sprintf(trans('website.W0115'),trans('website.W0131'))); ?>

                    </span>
                </label>
            </div>
            <div class="col-md-5 col-sm-4 col-xs-12 social-btn-wrapper">
                <div class="checkbox pull-right bootstrap-toggle-button">
                    <label>
                        <input id="instagram" type="checkbox" <?php if(!empty($user['instagram_id'])): ?> checked <?php endif; ?> data-toggle="toggle" data-url="<?php echo e(asset('/login/instagram')); ?>" value="instagram_id" />
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row form-group">
            <div class="col-md-7 col-sm-8 col-xs-12 social-link-wrapper">
                <label class="control-label">
                    <img src="<?php echo e(asset('images/facebook.png')); ?>" />&nbsp;&nbsp;
                    <span class="social-type-name">
                        <?php echo e(sprintf(trans('website.W0115'),trans('website.W0116'))); ?>

                    </span>
                </label>
            </div>
            <div class="col-md-5 col-sm-4 col-xs-12 social-btn-wrapper">
                <div class="checkbox pull-right bootstrap-toggle-button">
                    <label>
                        <input id="facebook" type="checkbox" <?php if(!empty($user['facebook_id'])): ?> checked <?php endif; ?> data-toggle="toggle" data-url="<?php echo e(asset('/login/facebook')); ?>" value="facebook_id" />
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row form-group">
            <div class="col-md-7 col-sm-8 col-xs-12 social-link-wrapper">
                <label class="control-label">
                    <img src="<?php echo e(asset('images/t-w-i-t-t-e-r-small-icon.png')); ?>" />&nbsp;&nbsp;
                    <span class="social-type-name">
                        <?php echo e(sprintf(trans('website.W0115'),trans('website.W0119'))); ?>

                    </span>
                </label>
            </div>
            <div class="col-md-5 col-sm-4 col-xs-12 social-btn-wrapper">
                <div class="checkbox pull-right bootstrap-toggle-button">
                    <label>
                        <input id="twitter" type="checkbox" <?php if(!empty($user['twitter_id'])): ?> checked <?php endif; ?> data-toggle="toggle" data-url="<?php echo e(asset('/login/twitter')); ?>" value="twitter_id" />
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row form-group">
            <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
                <label class="control-label">
                    <img src="<?php echo e(asset('images/linkedin.png')); ?>" />&nbsp;&nbsp;
                    <span class="social-type-name">
                        <?php echo e(sprintf(trans('website.W0115'),trans('website.W0120'))); ?>

                    </span>
                </label>
            </div>
            <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
                <div class="checkbox pull-right bootstrap-toggle-button">
                    <label>
                        <input id="linkedin" type="checkbox" <?php if(!empty($user['linkedin_id'])): ?> checked <?php endif; ?> data-toggle="toggle" data-url="<?php echo e(asset('/login/linkedin')); ?>" value="linkedin_id" />
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row form-group">
            <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
                <label class="control-label">
                    <img src="<?php echo e(asset('images/gplus.png')); ?>" />&nbsp;&nbsp;
                    <span class="social-type-name">
                        <?php echo e(sprintf(trans('website.W0115'),trans('website.W0121'))); ?>

                    </span>
                </label>
            </div>
            <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
                <div class="checkbox pull-right bootstrap-toggle-button">
                    <label>
                        <input id="googleplus" type="checkbox" <?php if(!empty($user['googleplus_id'])): ?> checked <?php endif; ?> data-toggle="toggle" data-url="<?php echo e(asset('/login/googleplus')); ?>" value="googleplus_id" />
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php $__env->startPush('inlinescript'); ?>
    <link href="<?php echo e(asset('css/bootstrap-toggle.min.css')); ?>" rel="stylesheet">
    <script src="<?php echo e(asset('js/bootstrap-toggle.min.js')); ?>"></script>
    <script type="text/javascript">
        $(function() {
            $(document).on('change','#instagram, #facebook, #twitter, #linkedin, #googleplus',function(e) {
                var $this = $(this);
                var $isChecked = $this.prop('checked');

                if($isChecked === true){
                    window.location = $this.data('url');
                }else{
                    $.post('<?php echo e(url("employer/__socialsettings")); ?>?socialkey='+$this.val()); 
                }
            })
        });
    </script>
<?php $__env->stopPush(); ?>