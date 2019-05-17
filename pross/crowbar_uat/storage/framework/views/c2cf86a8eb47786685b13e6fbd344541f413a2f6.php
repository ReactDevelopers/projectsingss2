    
    <?php $__env->startSection('requirecss'); ?>
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/owl.carousel.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
        <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
        <script type="text/javascript">
        </script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <!-- Banner Section -->
            <div class="static-heading-sec">
                <div class="container-fluid">
                    <div class="static Heading">                    
                        <h1>Updates</h1>                        
                    </div>                    
                </div>
            </div>
        <!-- /Banner Section -->
        <!-- Main Content -->
        <div class="contentWrapper">
            <section class="aboutSection questions-listing">
                <div class="container">
                	<?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </section> 
        </div>
        <div class="modal fade upload-modal-box add-payment-cards" id="add-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('inlinescript'); ?>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make($extends, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>