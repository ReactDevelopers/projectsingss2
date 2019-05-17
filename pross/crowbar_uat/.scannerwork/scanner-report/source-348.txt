    
    <?php $__env->startSection('requirecss'); ?>
        <style>.education-box .edit-icon, .work-experience-box .edit-icon, [data-request="delete"]{display: none!important;}</style>
        <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(asset('css/jquery.fancybox.css')); ?>" type="text/css" media="screen" />
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
                        <h4>
                            <span class="hire-me-title"><?php echo e($title); ?></span>
                            <a class="hire-me" class="manage-cards" data-target="#hire-me" data-request="ajax-modal" data-url="<?php echo e(url(sprintf('%s/hire-talent?talent_id=%s',EMPLOYER_ROLE_TYPE,$talent_id))); ?>" href="javascript:void(0);"><img src="<?php echo e(asset('images/add-white.png')); ?>"><?php echo e(trans('website.W0137')); ?></a>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                <div class="container">
                    <div class="row mainContentWrapper">
                        <?php if ($__env->exists('employer.talent.includes.sidebar')) echo $__env->make('employer.talent.includes.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <div class="col-md-8 col-sm-8 right-sidebar">
                            <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade upload-modal-box add-payment-cards" id="hire-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.employer.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>