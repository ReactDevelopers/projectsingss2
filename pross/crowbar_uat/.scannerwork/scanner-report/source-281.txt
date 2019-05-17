    
    <?php $__env->startSection('requirecss'); ?>
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="contentWrapper">  
            <section class="login-section">
                <div class="container top-margin-20px">                    
                    <div class="login-inner-wrapper top-margin-20px">
                        <div class="row has-vr">
                            <div class="text-center">
                                <h2 class="form-heading">
                                    <?php echo $message; ?>

                                </h2>
                                <div class="col-md-12">
                                    <?php  $agent = new Jenssegers\Agent\Agent;  ?>
                                    <?php if($agent->isMobile()): ?>
                                        <a href="crowbar://">&larr; <?php echo e(trans('website.W0164')); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo e(url('/login')); ?>">&larr; <?php echo e(trans('website.W0164')); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>