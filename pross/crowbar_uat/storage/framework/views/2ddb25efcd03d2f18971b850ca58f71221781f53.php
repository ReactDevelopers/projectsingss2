<?php $__env->startSection('requirejs'); ?>
<script type="text/javascript">
    var dataTableInstance = '';
    var page = '<?php echo $page; ?>';
    $(function(){
        dataTableInstance = $('#customer-table').DataTable({
            paging: true,
            searching: true,
            processing: true,
            serverSide: true,
            ajax: "<?php echo url(sprintf('%s/ajax/messages/"+page+"',$url)); ?>",
            columns : [
                { data: null,"className": 'sno',"orderable": false,"defaultContent": '',"searchable": false},
                { data: 'message_subject', name: 'message_subject' },
                { data: 'created', name: 'created' },
                { data: 'message_status', name: 'message_status' },
                { data: null,"className": 'action',"orderable": false,"defaultContent": '',"searchable": false}
            ],
            order:[
                [1, "ASC"]
            ],
            "columnDefs": [{
                "targets": 0,
                "data": null,
                "render": function (data, type, full, meta) {
                    return parseFloat(meta.row) + parseFloat(1) + parseFloat(meta.settings._iDisplayStart);
                }
            },{
                "targets": 4,
                "data": null,
                "render": function (data) {
                    return '<a href="<?php echo e($url); ?>/messages/detail/'+data.id_message+'" class="badge bg-light-blue">View</a>';
                }
            }],
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if(Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo e(Session::get('success')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Folders</h3>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li<?php echo e($page == 'inbox' ? ' class="active"' : ''); ?>><a href="<?php echo e(url('administrator/messages/inbox')); ?>"><i class="fa fa-inbox"></i> Inbox</a>
                        <li<?php echo e($page == 'closed' ? ' class="active"' : ''); ?>><a href="<?php echo e(url('administrator/messages/closed')); ?>"><i class="fa fa-power-off"></i> Closed</a></li>
                        <li<?php echo e($page == 'trashed' ? ' class="active"' : ''); ?>><a href="<?php echo e(url('administrator/messages/trashed')); ?>"><i class="fa fa-trash-o"></i> Trash</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="customer-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Message Subject</th>
                                    <th>Message Date</th>
                                    <th>Message Status</th>
                                    <th width="10">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>