    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/owl.carousel.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/owl.theme.default.min.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="welcome-section">
            <div class="container">
                <div class="tile-sec">
                    <div class="tile-registeration-form">
                        <form method="POST" role="signup" action="<?php echo e(url('/signup/none/process')); ?>" class="form-horizontal login-form" autocomplete="off">
                            <?php echo e(csrf_field()); ?>

                            <input type="hidden" value="<?php echo e(Request::get('token')); ?>" name="remember_token" />
                            <?php if(empty($social)): ?>
                                <h2 class="light-heading white-color"><?php echo e(trans('website.W0837')); ?></h2>
                            <?php else: ?>
                                <h2 class="light-heading white-color"><?php echo e(trans('website.W0838')); ?></h2>
                            <?php endif; ?>
                            <div class="form-group has-feedback<?php echo e($errors->has('first_name') ? ' has-error' : ''); ?>">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0142')); ?><?php if($errors->has('first_name') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('first_name')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="first_name" value="<?php echo e(old('first_name',(!empty($social['first_name'])?$social['first_name']:''))); ?>" type="text" class="form-control" data-toggle="tooltip" title="<?php echo e($errors->first('first_name')); ?>">
                                </div>
                            </div>
                            <div class="form-group has-feedback<?php echo e($errors->has('last_name') ? ' has-error' : ''); ?>">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0143')); ?><?php if($errors->has('last_name') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('last_name')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="last_name" value="<?php echo e(old('last_name',(!empty($social['last_name'])?$social['last_name']:''))); ?>" type="text" class="form-control" data-toggle="tooltip" title="<?php echo e($errors->first('last_name')); ?>">
                                </div>
                            </div>
                            <?php if(0): ?>
                                <div class="form-group has-feedback<?php echo e($errors->has('company_name') ? ' has-error' : ''); ?>" id="company_name" style="display: none;">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0096')); ?><?php if($errors->has('company_name') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('company_name')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="company_name" value="<?php echo e(old('company_name')); ?>" type="text" class="form-control" data-toggle="tooltip" title="<?php echo e($errors->first('company_name')); ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group has-feedback<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0144')); ?><?php if($errors->has('email') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('email')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="email" value="<?php echo e(old('email',(!empty($social['social_email'])?$social['social_email']:''))); ?>" type="text" class="form-control" data-toggle="tooltip" title="<?php echo e($errors->first('email')); ?>">
                                </div>
                            </div>
                            <?php if(empty($social['social_id'])): ?>
                                <div class="form-group has-feedback toggle-social<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0145')); ?> <?php if($errors->has('password') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('password')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="password" type="password" class="form-control" autocomplete="off"  data-toggle="tooltip" title="<?php echo e($errors->first('password')); ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="login-type-radio">
                                        <div class="grouped-radio">
                                            <label class="get-hired">
                                                <input type="radio" name="work_type" value="individual" <?php echo e(!empty(old('work_type')) ? (old('work_type') == "individual"? "checked='checked'": '' ) : "checked='checked'"); ?> data-request="show-hide" data-true-condition=".normal-section" data-false-condition=".company-name-section" data-condition="individual">
                                                <span><?php echo e(trans('website.W0942')); ?></span>
                                            </label>                                        
                                        </div>
                                        <div class="grouped-radio">
                                            <label class="hire-talent">
                                                <input type="radio" name="work_type" value="company" <?php echo e(old('work_type') == "company" ? "checked='checked'" : ''); ?> data-request="show-hide" data-true-condition=".company-name-section" data-false-condition=".normal-section" data-condition="company">
                                                <span><?php echo e(trans('website.W0943')); ?></span>
                                            </label>                                        
                                        </div>
                                    </div>                                
                                </div>
                            </div>  

                            <div class="company-name-section">
                                <div class="form-group has-feedback<?php echo e($errors->has('company_name') ? ' has-error' : ''); ?>">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label"><?php echo e(trans('website.W0944')); ?> <?php if($errors->has('company_name') ): ?><span class="error-help" data-toggle="tooltip" title="<?php echo e($errors->first('company_name')); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></span><?php endif; ?></label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="company_name" value="<?php echo e(old('company_name',(!empty($social['company_name'])?$social['company_name']:''))); ?>" type="text" class="form-control" data-toggle="tooltip" title="<?php echo e($errors->first('company_name')); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <p class="policy-text"><?php echo trans('website.W0662'); ?></p>
                                </div>
                            </div>                                
                            <div class="form-group submit-form-btn text-center">
                                <div class="col-sm-12 col-xs-12">
                                    <input type="hidden" name="social_key" value="<?php echo e((!empty($social['social_key']))?$social['social_key']:""); ?>" />
                                    <input type="hidden" name="social_id" value="<?php echo e((!empty($social['social_id']))?$social['social_id']:""); ?>" />
                                    <input type="hidden" name="name" value="<?php echo e((!empty($social['social_name']))?$social['social_name']:""); ?>" />
                                    <input type="hidden" name="picture" value="<?php echo e((!empty($social['social_picture']))?$social['social_picture']:""); ?>" />
                                    <input type="hidden" name="country" value="<?php echo e((!empty($social['social_country']))?$social['social_country']:""); ?>" />
                                    <input type="hidden" name="gender" value="<?php echo e((!empty($social['social_gender']))?$social['social_gender']:""); ?>" />
                                    <button type="submit" class="btn btn-sm redShedBtn"><?php echo e(trans('website.W0839')); ?></button>
                                </div>
                            </div>                     
                        </form>
                        <a class="hide" data-target="#select-type" data-request="ajax-modal" data-url="<?php echo e(url('social/signup')); ?>" href="javascript:void(0);">Select Type</a>
                    </div>
                    
                </div>
            </div>   
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinecss'); ?>
        <link href="<?php echo e(asset('css/hidePassword.css')); ?>" rel="stylesheet">
        <style type="text/css">
            .tile-registeration-form{
                width: 35%;
                background: #1b262f;
                margin: 40px auto;
                float: none;
            }
            .tile-registeration-form form{
                width: 330px;
                margin: 0 auto;
            }
            .tile-registeration-form .submit-form-btn button{
                margin-top: 5px;
            }
        </style>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('inlinescript'); ?>
        <script type="text/javascript" src="<?php echo e(asset('js/hideShowPassword.js')); ?>"></script>
        <script type="text/javascript">
            $('[name="password"]').hidePassword(true);
            $(document).ready(function(){
                <?php if(old('type') == "employer"): ?>
                    $('[data-request="show-target"]').trigger('change');
                <?php endif; ?>
            });
            $(document).on('change','[data-request="show-target"]',function(){
                var $this           = $(this);
                var $form_action    = $this.data('form_action');
                var $target         = $this.data('target');
                var $show           = $this.data('show');
                var $form_role      = $this.data('form_role');
                var $value          = $this.val();

                /*if($show == true){
                    $($target).show();
                    $this.closest('[role="signup"]').find('.signup-Options').hide();
                    $('.tile-registeration-form .submit-form-btn button').css('margin-top','26px');
                }else{
                    $($target).hide();
                    $this.closest('[role="signup"]').find('.signup-Options').show();
                    $('.tile-registeration-form .submit-form-btn button').css('margin-top','35px');
                }*/
                $($form_role).attr('action',$form_action);
            });
           
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>