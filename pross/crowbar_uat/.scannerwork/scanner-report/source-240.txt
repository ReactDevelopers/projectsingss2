<div class="proposals-listing-section">
    <?php echo ___getmenu('talent-proposal-menu','%s<ul class="user-profile-links">%s</ul>','active',true,false); ?> 
    <div class="clearfix"></div>
    <div class="currentJobOne-section accepted-proposals-listing">
        <div class="datatable-listing no-padding-cell shift-up-5px">
            <?php echo $html->table();; ?>

        </div>
    </div>
</div>   

<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>


    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper .row:first').remove();
    	});
    </script>
<?php $__env->stopPush(); ?>
