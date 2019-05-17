<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="<?php echo e(url(sprintf('%s/payout/management/update/%s',ADMIN_FOLDER,$country_id))); ?>">
                        <input type="hidden" name="_method" value="PUT">
                        <?php echo e(csrf_field()); ?>

                        <div class="panel-body">
	                        <div class="form-group">
	                        	<label for="name">Country: <?php echo e($country_name); ?></label>
					            <input type="hidden" name="country" value="<?php echo e($country_name); ?>">
							</div>
						</div>

						<div class="add-payout-table">
					        <table style="width:100%;">
							  	<tr>
							    	<th>Profession:</th>
							    	<th>Registration Exists?:</th>
							    	<th>Accept Escrow:</th>
							    	<th>Pay Commission(in %):</th> 
							    	<th>Ask for Identification Number:</th>
							  	</tr>
				            	<?php $__currentLoopData = ___cache('industries_name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
					            	<tr>
					            		<input type="hidden" name="payout_id_<?php echo e($key); ?>" value="<?php echo e($payout_det[$key]['id']); ?>">
					            		<th><?php echo e($value); ?></th>
					            		<th>
					            			<div class="form-group">
						            			<div>
													<input type="radio" name="is_registered_<?php echo e($key); ?>" value="yes" <?php if($payout_det[$key]['is_registered_show']=='yes'): ?> checked="checked" <?php endif; ?>> Yes
			                            			<input type="radio" name="is_registered_<?php echo e($key); ?>" value="no" <?php if($payout_det[$key]['is_registered_show']=='no'): ?> checked="checked" <?php endif; ?>> No
			                            		</div>
			                        		</div>
					            		</th>
								    	<th>
								    		<div class="form-group">
						            			<div>
						            				<label>Registered</label>
													<input type="radio" name="accept_escrow_<?php echo e($key); ?>" value="yes" 
													<?php if($payout_det[$key]['accept_escrow']=='yes'): ?> checked="checked" <?php endif; ?>> Yes
			                            			<input type="radio" name="accept_escrow_<?php echo e($key); ?>" value="no" <?php if($payout_det[$key]['accept_escrow']=='no'): ?> checked="checked" <?php endif; ?>> No
			                            		</div>
			                            		<div>
			                            			<label>Non Registered</label>
													<input type="radio" name="non_reg_accept_escrow_<?php echo e($key); ?>" value="yes" <?php if($payout_det[$key]['non_reg_accept_escrow']=='yes'): ?> checked="checked" <?php endif; ?>> Yes
			                            			<input type="radio" name="non_reg_accept_escrow_<?php echo e($key); ?>" value="no" <?php if($payout_det[$key]['non_reg_accept_escrow']=='no'): ?> checked="checked" <?php endif; ?>> No
			                            		</div>
			                        		</div>
								    	</th>
								    	<th>
								    		<div class="form-group">
						            			<div>
			                            			<input type="text" class="pay_commision_val" name="pay_commision_percent_<?php echo e($key); ?>" value="<?php echo e($payout_det[$key]['pay_commision_percent']); ?>" style="width:65px" placeholder="Enter %">
			                            		</div>
			                        		</div>
								    	</th> 
								    	<th>
								    		<div class="form-group">
						            			<div>
													<input type="radio" name="identification_no_<?php echo e($key); ?>" value="yes" <?php if($payout_det[$key]['identification_number']=='yes'): ?> checked="checked" <?php endif; ?>> Yes
			                            			<input type="radio" name="identification_no_<?php echo e($key); ?>" value="no" <?php if($payout_det[$key]['identification_number']=='no'): ?> checked="checked" <?php endif; ?>> No
			                            		</div>
			                        		</div>
								    	</th>
								    </tr>
				            	<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
							</table>
						</div>

                        <div class="panel-footer">
                            <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">
	$(".pay_commision_val").bind('keypress', function(e) {    
        var k = e.which; 
        var ok = k >= 48 && k <= 57 || //0-9 
            k == 46 //.
        if (!ok){
            e.preventDefault();
        }
    });
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