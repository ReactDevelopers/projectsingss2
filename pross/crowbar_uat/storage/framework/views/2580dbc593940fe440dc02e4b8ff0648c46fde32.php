<?php $__env->startSection('content'); ?>
<section class="content">
    <?php if($add_user): ?>
        <div class="row">
            <div class="col-md-12 margin-bottom ">
                <button class=" btn btn-info" id="export-excel" title="Export">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </button>
                <span class="pull-right">
                    <a href="<?php echo e(url(sprintf('%s/users/%s/add',ADMIN_FOLDER,$page))); ?>" class="btn btn-app" style="height: 40px; padding: 10px; margin: 0px;">
                        <i class="fa fa-plus-circle pull-left"></i> Add New
                    </a>
                </span>
            </div>
        </div>
    <?php endif; ?>
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
                    
                    <div class="table-responsive">
                        <?php echo $html->table();; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal" id="hire-me" >
    <div >
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
    <?php echo $html->scripts(); ?>


    <script type="text/javascript">
        $('#export-excel').click(function(){
            var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
            params.download = 'csv';
            params.length = <?php echo e($userscount); ?>;
            var paramsQuery = $.param(params);
            window.location.href= '<?php echo url('administrator/users/'.$page); ?>'+'?'+paramsQuery;          
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>