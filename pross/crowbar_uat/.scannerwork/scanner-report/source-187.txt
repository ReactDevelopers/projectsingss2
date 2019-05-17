    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('backend/plugins/iCheck/square/square.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('backend/plugins/iCheck/icheck.min.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square',
                    radioClass: 'iradio_square',
                    increaseArea: '20%'
                }); 
            });
        </script>
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="login-box">
            <div class="login-logo">
                <a href="<?php echo e(url('/'.ADMIN_FOLDER.'/login')); ?>"><img src="<?php echo e(asset('images/splashLogo.png')); ?>"></a>
            </div>
            <div class="login-box-body">
                <form autocomplete="off" role="form" method="POST" action="<?php echo e(url(sprintf('/%s/%s',ADMIN_FOLDER,'authenticate'))); ?>">
                    <?php echo e(csrf_field()); ?>

                    <?php echo e(___alert((!empty($alert))?$alert:'')); ?>

                    <div class="form-group has-feedback<?php echo e($errors->has(LOGIN_EMAIL) ? ' has-error' : ''); ?>">
                        <input type="text" class="form-control" name="<?php echo e(LOGIN_EMAIL); ?>" value="<?php echo e(old(LOGIN_EMAIL,${LOGIN_EMAIL})); ?>" placeholder="Email Address" autocomplete="off">
                        <span class="glyphicon glyphicon-envelope form-control-feedback<?php echo e($errors->has(LOGIN_EMAIL) ? ' text-red' : ''); ?>"></span>
                        <?php if($errors->has(LOGIN_EMAIL)): ?>
                            <span class="help-block"><?php echo e($errors->first(LOGIN_EMAIL)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group has-feedback<?php echo e($errors->has(LOGIN_PASSWORD) ? ' has-error' : ''); ?>">
                        <input type="password" name="<?php echo e(LOGIN_PASSWORD); ?>" class="form-control" value="<?php echo e(old(LOGIN_PASSWORD,${LOGIN_PASSWORD})); ?>" placeholder="Password" autocomplete="off">
                        <span class="glyphicon glyphicon-lock form-control-feedback<?php echo e($errors->has(LOGIN_PASSWORD) ? ' text-red' : ''); ?>"></span>
                        <?php if($errors->has(LOGIN_PASSWORD)): ?>
                            <span class="help-block"><?php echo e($errors->first(LOGIN_PASSWORD)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" name="<?php echo e(LOGIN_REMEMBER); ?>" value="1" <?php if(!empty(${LOGIN_PASSWORD})): ?> checked <?php endif; ?>> Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-default btn-block">Sign In</button>
                        </div>
                    </div>
                </form>    
                <a href="<?php echo e(url('administrator/forgot-password')); ?>">I forgot my password</a>
                <br>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.default', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>