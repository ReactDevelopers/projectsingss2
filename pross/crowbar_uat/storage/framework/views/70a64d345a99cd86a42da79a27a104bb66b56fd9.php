<?php echo $__env->make('employer.job.includes.talent-profile-menu',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="payment-tabs job-related-tabs shift-up-5px">
    <div class="datatable-listing find-talent-portfolio">
        <div class="no-table">
            <?php echo $html->table();; ?>

        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('/js/jquery.fancybox.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper > .row:first').remove();
            $('#dataTableBuilder_wrapper thead').remove();
        });
        $('.fancybox').fancybox({
            openEffect  : 'elastic',
            closeEffect : 'elastic',
            closeBtn    : true,
            helpers : {
                title : {type : 'inside'},
                buttons : {},
                overlay : {closeClick: false}
            }
        });
    </script>
<?php $__env->stopPush(); ?>
