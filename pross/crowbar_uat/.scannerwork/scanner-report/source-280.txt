<?php $__env->startSection('requirecss'); ?>
    <link href="<?php echo e(asset('css/cropper.min.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>



<?php $__env->startSection('inlinecss'); ?>
    
<?php $__env->stopSection(); ?>



<?php $__env->startSection('requirejs'); ?>
    <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('js/article-cropper.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/custom.js')); ?>" type="text/javascript"></script>   
    <script src="<?php echo e(asset('js/article.js')); ?>" type="text/javascript"></script>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('inlinejs'); ?>
    <script type="text/javascript">

    </script>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Banner Section -->
    <?php if(Request::get('stream') != 'mobile'): ?>
        <div class="static-heading-sec article-heading">
            <div class="container-fluid">
                <div class="static Heading">                    
                    <h1><?php echo e(!empty($page_title) ? $page_title : trans('website.W0964')); ?></h1>                        
                </div>                    
            </div>
        </div>
    <?php endif; ?>
    <!-- /Banner Section -->
    <!-- Main Content -->
    <div class="contentWrapper">
        <section class="aboutSection questions-listing">
            <div class="container">
                <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </section> 
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make($extends, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>