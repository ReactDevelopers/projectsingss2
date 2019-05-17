<?php 
    $imageArray = [
        asset('images/about-icon_001.png'),
        asset('images/about-icon_002.png'),
        asset('images/about-icon_003.png')
    ];
 ?>
<div class="about-tabs-content">
    <ul>
        <?php $__currentLoopData = $banner['talent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
            <li>
                <div class="about-tab-image" style="background-image:url(<?php echo e(asset("uploads/banner/$item->banner_image")); ?>)">
                </div>
                <div class="about-tab-desc">
                    <span class="about-tab-icon">
                        <img src="<?php echo e($imageArray[$key]); ?>">                        
                    </span>
                    <h5><?php echo e($item->banner_title); ?></h5>
                    <p><?php echo nl2br($item->banner_text); ?></p>
                </div>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
    </ul>
</div>