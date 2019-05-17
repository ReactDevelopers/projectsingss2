    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/moment.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/easyResponsiveTabs.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery.nstSlider.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/custom.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
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
                <div class="container">
                    <div class="row mainContentWrapper">
                        <?php if ($__env->exists('talent.proposals.includes.sidebar')) echo $__env->make('talent.proposals.includes.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <div class="col-md-8 col-sm-8 right-sidebar">
                            <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinescript'); ?>
        <script type="text/javascript">
            setTimeout(function(){
                if($('.right-sidebar .content-box').length){
                    if($('.left-sidebar').height() > $('.right-sidebar').height()){
                        $('.right-sidebar .content-box').height($('.left-sidebar').height()-98);
                    }
                }
            },0);
        </script>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.talent.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>