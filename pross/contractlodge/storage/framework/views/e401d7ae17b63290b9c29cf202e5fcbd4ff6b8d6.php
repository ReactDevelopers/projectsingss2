<?php if(request()->query('message') && request()->query('level')): ?>
    <div class="alert
                alert-<?php echo e(request()->query('level')); ?>

                <?php echo e(request()->query('important') ? 'alert-important' : ''); ?>"
                role="alert"
    >
        <?php if(request()->query('important')): ?>
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true"
            >&times;</button>
        <?php endif; ?>

        <?php echo request()->query('message'); ?>

    </div>
<?php endif; ?>
