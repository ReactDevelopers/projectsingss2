<div class="panel-body">
    
    <div class="company-name-wrapper">
        <?php if(!empty($connected_user)): ?>
            <?php $__currentLoopData = $connected_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
            <div class="info-row row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <label class="company-label"><?php echo e($v['user']['name']); ?></label>
                    </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">

</script>
<?php $__env->stopPush(); ?>
