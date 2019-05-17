<?php echo ___getmenu('employer-myjobs','<span class="hide">%s</span><ul class="user-profile-links">%s</ul>','active',true,true); ?>

<div class="shift-up-5px">
    <div>
        <div class="no-table datatable-listing">
            <?php echo $html->table();; ?>

        </div>   
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">$(function(){$('#dataTableBuilder_wrapper .row:first').remove();});</script>
<?php $__env->stopPush(); ?>
