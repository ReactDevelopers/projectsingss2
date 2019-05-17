    
    <?php $__env->startSection('requirecss'); ?>

    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>

    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <!-- Main Content -->
        <div class="contentWrapper">  
            <section class="login-section">
                <div class="container top-margin-20px">
                    <div class="login-inner-wrapper top-margin-20px">                    
                        <div class="row has-vr">
                            <?php if(!empty($email)): ?>
                                <div class="col-md-7 col-sm-8 col-xs-12">                            
                                    <h4 class="form-heading blue-text"><?php echo e(trans('website.W0168')); ?></h4>
                                    <div class="verifiedAccount">
                                        <p><?php echo e(str_replace('\n',' ',sprintf(trans('general.M0021'),$email))); ?></p>
                                        <a href="<?php echo e(sprintf('%s?token=%s',url('/'),\Request::get('token'))); ?>"><?php echo e(trans('website.W0169')); ?></a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <h4 class="form-heading blue-text">
                                        <?php echo str_replace("×","",strip_tags($alert,'<br>')); ?>

                                    </h4>
                                    <div class="col-md-12">
                                        <?php if($agent->isMobile()): ?>
                                            <a href="crowbar://">&larr; <?php echo e(trans('website.W0164')); ?></a>
                                        <?php else: ?>
                                            <a href="<?php echo e(url('/')); ?>">&larr; <?php echo e(trans('website.W0164')); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>