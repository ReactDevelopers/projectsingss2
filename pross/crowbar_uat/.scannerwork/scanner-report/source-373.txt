    

    
    <?php $__env->startSection('requirecss'); ?>
        <!-- <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet"> -->
        <link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/cropper.min.css')); ?>" rel="stylesheet">
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
        <script src="<?php echo e(asset('js/cropper.min.js')); ?>" type="text/javascript"></script>
    <?php $__env->stopSection(); ?>
    
    
    <?php $__env->startSection('inlinejs'); ?>
        <script type="text/javascript">
            $(".cropper").SGCropper({
                viewMode: 1,
                aspectRatio: "2/3",
                cropBoxResizable: false,
                formContainer:{
                    actionURL:"<?php echo e(url(sprintf('ajax/crop?imagename=image&user_id=%s',Auth::user()->id_user))); ?>",
                    modelTitle:"<?php echo e(trans('website.W0261')); ?>",
                    modelSuggestion:"<?php echo e(trans('website.W0263')); ?>",
                    modelDescription:"<?php echo e(trans('website.W0264')); ?>",
                    modelSeperator:"<?php echo e(trans('website.W0265')); ?>",
                    uploadLabel:"<?php echo e(trans('website.W0267')); ?>",
                    fieldLabel:"",
                    fieldName: "image",
                    btnText:"<?php echo e(trans('website.W0262')); ?>",
                    defaultImage: "../images/product_sample.jpg",
                    loaderImage: "../../../../images/loader.gif",
                }
            });
        </script>
        
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
                        <?php if ($__env->exists('employer.profile.includes.sidebar')) echo $__env->make('employer.profile.includes.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <div class="col-md-8 col-sm-8 right-sidebar">
                            <div class="navigation-bar">
                                <ul>
                                    <li class="<?php if(in_array('one',$steps)): ?> <?php echo e('selected'); ?> <?php endif; ?>">
                                        <span class="navigation-stepCount">1</span>
                                        <a href="<?php echo e(url(sprintf('%s/profile/edit/one',EMPLOYER_ROLE_TYPE))); ?>" title="<?php echo e(trans('website.W0020')); ?>" class="navigation-dot"></a>
                                    </li>
                                    <li class="<?php if(in_array('two',$steps)): ?> <?php echo e('selected'); ?> <?php endif; ?>">
                                        <span class="navigation-stepCount">2</span>
                                        <a href="<?php echo e(url(sprintf('%s/profile/edit/two',EMPLOYER_ROLE_TYPE))); ?>" title="<?php echo e(trans('website.W0021')); ?>" class="navigation-dot"></a>
                                    </li>
                                </ul>
                            </div>
                            <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employer.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>