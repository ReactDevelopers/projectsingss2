<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="<?php echo e(url(sprintf('%s/group/update/%s',ADMIN_FOLDER,___encrypt($group_detail['id'])))); ?>">
                        <input type="hidden" name="_method" value="PUT">
                        <?php echo e(csrf_field()); ?>


                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Group Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Group Name" value="<?php echo e(old('name',$group_detail['name'])); ?>">
                            </div>
                            <input type="hidden" class="form-control" name="hidden_name" placeholder="Group Name" value="<?php echo e($group_detail['name']); ?>">
                            <div class="form-group">
                                <div>
                                    <label for="name">Select Group Members</label>
                                    <select class="form-control" name="talent_id[]" placeholder="Select Talent" multiple="true">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div>
                                    <label for="name">File Upload</label>
                                    <input class="form-control" type="file" value="" name="file">
                                </div>
                            </div>
                            <a href="<?php echo e(asset('sample.xls')); ?>" >Download Sample File</a>
                        </div>
                        <div class="panel-footer">
                            <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>
                    </form>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <?php echo $html->table();; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" name="id_group" id="id_group" value="<?php echo e($id_group); ?>" />
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
<script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
<script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
<?php echo $html->scripts(); ?>

<script type="text/javascript">
    var id_group = $('#id_group').val();
    setTimeout(function(){
        $('[name*="talent_id"]').select2({
            formatLoadMore   : function() {return 'Loading more...'},
            multiple:true,
            ajax: {
                url: base_url+'/talents_members',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        is_edit: 'Y' ,
                        id_group: id_group 
                    }
                    return query;
                }
            },
            placeholder: function(){
                $(this).find('option[value!=""]:first').html();
            }
        }).on('change',function(){
            
        });
    },1000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('inlinecss'); ?>
    <style type="text/css">
        .select2-results__option.select2-results__option--load-more{
            display: none;    
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>