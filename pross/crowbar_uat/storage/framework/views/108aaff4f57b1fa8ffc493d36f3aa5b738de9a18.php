    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('backend/plugins/iCheck/square/square.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('backend/plugins/iCheck/icheck.min.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinejs'); ?>
        <link href="<?php echo e(asset('css/hidePassword.css')); ?>" rel="stylesheet">
        <script type="text/javascript" src="<?php echo e(asset('js/hideShowPassword.js')); ?>"></script>
        <script type="text/javascript">$('[name="password"]').hidePassword(true);</script>    
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
                <?php if($link_status !== 'expired'): ?>
                    <form autocomplete="off" role="form" method="POST" action="<?php echo e(url(sprintf('/%s/%s',ADMIN_FOLDER,'reset-password?token='.$token))); ?>">
                        <?php echo e(csrf_field()); ?>

                        <?php echo e(___alert((!empty($alert))?$alert:'')); ?>

                        <div class="form-group has-feedback<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <input type="password" name="password" class="form-control" value="" placeholder="Password" autocomplete="off">
                            <span class="glyphicon form-control-feedback<?php echo e($errors->has('password') ? ' text-red' : ''); ?>"></span>
                            <?php if($errors->has('password')): ?>
                                <span class="help-block"><?php echo e($errors->first('password')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-default btn-flat btn-block">Reset Password</button>
                            </div>
                        </div>
                    </form>
                    <a href="<?php echo e(url('administrator/login')); ?>">Or sign in as a different user</a>
                    <br>
                <?php else: ?>
                    <div class="text-center">
                        <h4 class="form-heading blue-text">
                            <?php echo $message; ?>

                        </h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php $__env->stopSection(); ?>  

<?php echo $__env->make('layouts.backend.default', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>