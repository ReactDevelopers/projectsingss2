    
    <?php $__env->startSection('requirecss'); ?>

    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('toprequirejs'); ?>
        <script src="<?php echo e(asset('js/chat/socket.io')); ?>.js"></script>
        <script src="<?php echo e(asset('js/chat/slimscroll.js')); ?>"></script>
        <script src="<?php echo e(asset('js/chat/moment.js')); ?>"></script>
        <script src="<?php echo e(asset('js/chat/moment-timezone.js')); ?>"></script>
        <script src="<?php echo e(asset('js/chat/moment-timezone-with-data-2012-2022.js')); ?>"></script>
        <script src="<?php echo e(asset('js/chat/livestamp.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/moment-with-locales.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/moment-timezone-with-data.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/chat/chat.js')); ?>"></script>
        <script src="<?php echo e(asset('js/chat/notification.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <?php echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.chat.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>