<?php $__env->startSection('content'); ?>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                    <?php if(Session::has('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo e(Session::get('success')); ?>

                        </div>
                    <?php endif; ?>
                    <div class="table-responsive admin-table-wrapper">
                        <?php echo $html->table();; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
    <?php echo $html->scripts(); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>