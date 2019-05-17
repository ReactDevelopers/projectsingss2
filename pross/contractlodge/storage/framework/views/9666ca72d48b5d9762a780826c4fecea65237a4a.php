<?php if(count($errors) > 0): ?>
    <div class="alert alert-danger">
        <strong><?php echo e(__('Whoops!')); ?></strong> <?php echo e(__('Something went wrong!')); ?>

        <br><br>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
