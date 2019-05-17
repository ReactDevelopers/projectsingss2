    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery-ui.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/moment.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery-ui.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/easyResponsiveTabs.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery.nstSlider.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/custom.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                        <h4><?php echo e($title); ?></h4>
                    </div>                    
                </div>
            </div>
        </div>

        <?php if($is_payment_already_captured == 1): ?>
        	<div class="contentWrapper" style="text-align: center;">
	            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
	            	<span class="plan-main-heading"><?php echo e(trans('general.M0502')); ?></span>
	            </div>
	        </div>
        <?php else: ?>
	        <div class="contentWrapper" style="text-align: center;">
	            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
	            		<span class="plan-main-heading"><?php echo e(trans('website.W0363')); ?></span>
	                	<span><?php echo e(___format($quoted_price,true,true)); ?></span>
	                	<br/>
	                	<br/>
	                    <span class="total-plan-heading"><?php echo e(trans('website.W0362')); ?></span>
	                    <span class="total-plan-heading"><?php echo e(___formatDefault( ($transaction_total),true,true)); ?></span>
	                    <input type="hidden" name="transaction_id" value="<?php echo e($transaction_id); ?>">
	                    <input type="hidden" name="project_id" value="<?php echo e($project_id); ?>">
	                    <input type="hidden" name="proposal_id" value="<?php echo e($proposal_id); ?>">
	                    <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
	                    <br/>
	                    <br/>
	                    <div id="paypal-button"></div>
	            </div>
	        </div>
        <?php endif; ?>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinescript'); ?>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script>
        paypal.Button.render({
        env: '<?php echo e(env('PAYPAL_ENV')); ?>', // 'sandbox' or 'production'
        style: {
            layout: 'vertical',  // horizontal | vertical
            size:   'medium',    // medium | large | responsive
            shape:  'rect',      // pill | rect
            color:  'gold'       // gold | blue | silver | black
        },
        commit:true,
        // Set up the payment:
        // 1. Add a payment callback
        payment: function(data, actions) {
        // 2. Make a request to your server
            return paypal.request({
                method: 'post',
                url: "<?php echo e(url('/payment/mobile-create-payment')); ?>",
                headers: {
                    'x-csrf-token': "<?php echo e(csrf_token()); ?>",
                },
                'data':{
                		'transaction_id': $('input[name="transaction_id"]').val(),
                		'user_id'       : $('input[name="user_id"]').val() 
                		}
            }).then(function(data) {
                return data.id;
            });


        },
        // Execute the payment:
        // 1. Add an onAuthorize callback
        onAuthorize: function(data, actions) {
          // 2. Make a request to your server
            return paypal.request({
                method: 'post',
                url: "<?php echo e(url('/payment/mobile-execute-payment')); ?>",
                headers: {
                    'x-csrf-token': "<?php echo e(csrf_token()); ?>",
                },
                'data':{
                		'paymentID'		 : data.paymentID,
                		'payerID'		 : data.payerID, 
                		'transaction_id' : $('input[name="transaction_id"]').val(), 
                		'project_id'	 : $('input[name="project_id"]').val(), 
                		'proposal_id'	 : $('input[name="proposal_id"]').val(),
                		'user_id'        : $('input[name="user_id"]').val() 
                		}
            }).then(function(response) {

                if(response.status == true){
                	window.location.href = response.redirect_url;
                }else{
                	window.location.href = response.redirect_url;
                }
                
            });

        }
      }, '#paypal-button');
    </script> 
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.blank', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>