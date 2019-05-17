<?php $__env->startSection('content'); ?>
	<section class="content">
	    <div class="row">
	        <div class="col-md-12 margin-bottom">
	            <span class="pull-right">
	                <a href="<?php echo e($add_url); ?>" class="btn btn-app" style="height: 40px; padding: 10px; margin: 0px;">
	                    <i class="fa fa-plus-circle pull-left"></i> Add New
	                </a>
	            </span>
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
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('requirejs'); ?>    
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>


    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>