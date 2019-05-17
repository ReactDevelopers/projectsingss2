<div class="col-md-12">
    <div class="approved-proposals no-padding">
        <div class="datatable-listing">
            <?php echo $html->table();; ?>

        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">
        .view-profile-name .last-viewed-icon {
            position: absolute;
            right: 24px;
            margin-top: 8px;
        }
    </style>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

<?php $__env->stopPush(); ?>
