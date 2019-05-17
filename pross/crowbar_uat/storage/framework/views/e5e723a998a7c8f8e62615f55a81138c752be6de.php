<?php $__env->startSection('requirejs'); ?>
<script type="text/javascript">

</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content">
    <div class="row">
        <div class="col-md-12 margin-bottom ">
            <button class=" btn btn-info" id="export-excel" title="Export">
                <i class="fa fa-download" aria-hidden="true"></i>
            </button>
        </div>
    </div>
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
    <input type="hidden" id="delete-job-url" value="<?php echo e(url('administrator/project/delete')); ?>" />
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">
    function deleteJob(id_project){
        var url = $('#delete-job-url').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_project > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_project: id_project}
            })
            .done(function(data) {
                LaravelDataTables["dataTableBuilder"].draw();
                swal({
                    title: '',
                    html: data.message,
                    showLoaderOnConfirm: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick:false,
                    confirmButtonText: 'Okay',
                    cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                    confirmButtonColor: '#0FA1A8',
                    cancelButtonColor: '#CFCFCF'
                });
            });
        }
    }
        $('#export-excel').click(function(){
            var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
            params.download = 'csv';
            params.length = <?php echo e($count); ?>;
            var paramsQuery = $.param(params);
            window.location.href= '<?php echo url('administrator/project/listing'); ?>'+'?'+paramsQuery;          
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>