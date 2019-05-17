<?php  
	$header = 'innerheader';
	$footer = 'footer';
	$settings = \Cache::get('configuration');
 ?>


<?php $__env->startSection('content'); ?>
<section class="error-section">
	<div class="container">
		<div class="error-wrapper">
			<h3 class="error-type">404</h3>
			<p class="error-type-msg">Page not found</p>
			<span>:(</span>
		</div>
	</div>	
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>