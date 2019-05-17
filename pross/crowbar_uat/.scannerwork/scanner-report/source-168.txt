    
    <?php $__env->startSection('requirecss'); ?>
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>

    <?php if(!empty($banner['how-it-works'][1])): ?>
        <section class="about-banner-section">
            <img src="<?php echo e(asset("uploads/banner/".$banner['how-it-works'][1]->banner_image)); ?>">
        </section>
    <?php endif; ?>

    <section class="red-bar-box">
        <div class="container">
            <a href="<?php echo e(url('/')); ?>" class="btn btn-white-border"><?php echo e(trans('website.W0151')); ?></a>
            <h3><?php echo e(trans('website.W0502')); ?></h3>
        </div>
    </section>
    
    <?php if(!empty($banner['how-it-works'][1])): ?>
        <section class="about-white-box">
            <div class="container">
                <div class="about-description">
                    <p><?php echo nl2br($banner['how-it-works'][1]->banner_text); ?></p>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <section class="about-tab-section">
        <ul class="about-tabs">
            <li>
                <a href="<?php echo e(url('/page/how-it-works?section=get-hired')); ?>" <?php if(\Request::get('section') == 'get-hired'): ?> class="active" <?php endif; ?> <?php if(empty(\Request::get('section'))): ?> class="active" <?php endif; ?> id="get-hired">Get Hired</a>
            </li>
            <li>
                <a href="<?php echo e(url('/page/how-it-works?section=hire-talent')); ?>" <?php if(\Request::get('section') == 'hire-talent'): ?> class="active" <?php endif; ?> id="hire-talent">Hire Talent</a>
            </li>
            <li>
                <a href="<?php echo e(url('/page/faq')); ?>">FAQs</a>
            </li>
        </ul>
        <?php if(\Request::get('section') == 'hire-talent'): ?>
            <?php if ($__env->exists('front.includes.hiretalent')) echo $__env->make('front.includes.hiretalent', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php else: ?>
            <?php if ($__env->exists('front.includes.gethired')) echo $__env->make('front.includes.gethired', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript">
        $(function(){
            <?php if(!empty(\Request::get("section"))): ?>
                $('html, body').animate({
                    scrollTop: ($('#<?php echo e(\Request::get("section")); ?>').offset().top)
                }, 500);
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>