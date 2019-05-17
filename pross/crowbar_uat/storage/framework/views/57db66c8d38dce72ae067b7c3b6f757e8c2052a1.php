<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="<?php echo e(url(sprintf('%s/payout/management/duplicate/%s',ADMIN_FOLDER,$country_id))); ?>">
                        <input type="hidden" name="_method" value="PUT">
                        <?php echo e(csrf_field()); ?>


                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Select Desired Country</label>
                                <div>
                                    <select class="form-control" style="max-width: 400px;" name="country" placeholder="Country">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name">Selected Country: </label> <span><?php echo e($country_name); ?></span>
                            </div>
                        </div>

                        <div class="add-payout-table">
                            <div class="form-group">
                                <label for="name">Selected Country Configuration:-</label>
                            </div>
                            <table style="width:100%;">
                                <tr>
                                    <th>Profession:</th>
                                    <th>Is Registered:</th>
                                    <th>Accept Escrow:</th>
                                    <th>Pay Commission(in %):</th> 
                                    <th>Ask for Identification Number:</th>
                                </tr>
                                <?php $__currentLoopData = $payout_det; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($value['industry_name']); ?></td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span><?php echo e($payout_det[$key]['is_registered_show']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span>Registered:</span>
                                                <span><?php echo e($value['accept_escrow']); ?></span>
                                            </div>
                                            <div>
                                                <span>Non Registered:</span>
                                                <span><?php echo e($value['non_reg_accept_escrow']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span><?php echo e($value['pay_commision_percent']); ?>%</span>
                                            </div>
                                        </div>
                                    </td> 
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span><?php echo e($value['identification_number']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </table>
                        </div>

                        <div class="panel-footer">
                            <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">
    var old_country_id = "<?php echo e($country_id); ?>";
    setTimeout(function(){
        $('[name="country"]').select2({
            formatLoadMore   : function() {return 'Loading more...'},
            ajax: {
                url: base_url+'/countries',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                }
            },
            placeholder: function(){
                $(this).find('option[value!=""]:first').html();
            }
        }).on('change',function(){
            if($(this).val() == old_country_id){
                alert("You selected same country. Please select some other country.");
            }
        });
    },1000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('inlinecss'); ?>
    <style type="text/css">
        .select2-results__option.select2-results__option--load-more{
            display: none;    
        }
        .add-payout-table{
            padding:15px;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>