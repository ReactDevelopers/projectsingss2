    
    <?php $__env->startSection('requirecss'); ?>

    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>

    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <section class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4><?php echo $page['title']; ?></h4>
                    </div>
                </div>
            </div>
        </section>
        <div class="contentWrapper">
            <section class="staticSectionWrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="aboutContent">
                                <?php if(Request::segment(3) == 'privacy-policy'): ?>
                                    <div class="privacy-policy-img">
                                        <img src="<?php echo e(asset('images/trust_certificate.png')); ?>" / >
                                    </div>
                                <?php endif; ?>
                                <?php echo $page['content']; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>