<?php echo $__env->make('employer.job.includes.talent-profile-menu',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="payment-tabs job-related-tabs shift-up-5px">
    <div class="datatable-listing"> 
        <div class="no-table"> 
            <?php echo $html->table(); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    
    <script type="text/javascript">
        $(function(){
            $('.no-table thead').remove();
            $('#dataTableBuilder_wrapper > .row:first').remove();
            $('#dataTableBuilder_wrapper thead').remove();
        });
    </script>
<?php $__env->stopPush(); ?>
