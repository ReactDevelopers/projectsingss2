    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/hidePassword.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script type="text/javascript" src="<?php echo e(asset('js/hideShowPassword.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <?php if ($__env->exists('front.includes.login')) echo $__env->make('front.includes.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinescript'); ?>
        <script type="text/javascript">$('[name="password"]').hidePassword(true); $('[name="crowbar_password"]').hidePassword(true);</script>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>