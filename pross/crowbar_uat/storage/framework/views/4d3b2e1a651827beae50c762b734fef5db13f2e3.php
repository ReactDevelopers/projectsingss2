    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery-ui.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/moment.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery-ui.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/easyResponsiveTabs.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery.nstSlider.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/custom.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4><?php echo e($title); ?></h4>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                

                <div class="modal fade upload-modal-box add-payment-cards" id="add-select-type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"></div>
                <button type="button" id="btn-select-type" class="button-line" data-target="#add-select-type" data-request="ajax-modal" data-url="<?php echo e(url('talent/accept-reject-transfer-modal/'.$user_id)); ?>" style="display: none;"></button>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinescript'); ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('[data-request="ajax-modal"]').trigger('click');
                setTimeout(function(){
                    $('[data-request="ajax-modal"]').trigger('click');
                },500);
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>