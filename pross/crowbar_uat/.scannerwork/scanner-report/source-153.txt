<?php echo ___getmenu('talent-myjobs','<span class="hide">%s</span><ul class="user-profile-links">%s</ul>','active',true,true); ?>

<div class="shift-up-5px">
    <div>
        <div class="no-table datatable-listing">
            <?php echo $html->table();; ?>

        </div>   
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/bootstrap-timepicker.min.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">$(function(){$('#dataTableBuilder_wrapper .row:first').remove();});</script>
    <script type="text/javascript">
        $(function () {
            setTimeout(function(){
            	$(".timepicker").timepicker({
	                template: false,
	                showMeridian: false,
	                defaultTime: "00:00"
	            });
            },2000);
        })
    </script>
<?php $__env->stopPush(); ?>
