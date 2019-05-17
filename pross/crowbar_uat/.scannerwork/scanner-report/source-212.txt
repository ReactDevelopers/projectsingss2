<div class="contentWrapper job-listing-section employer-job-listing-payment">
    <div class="trnasferAmountList">
        <div class="no-padding-left clearfix">
            <div class="col-md-10 col-sm-12 col-xs-12 full-width-element">
                <form action="<?php echo e(url(sprintf('%s/payment/initiate',EMPLOYER_ROLE_TYPE))); ?>" method="post">
                    <div class="top-margin-20px"><?php echo e(___alert(\Session::get('alert'))); ?></div>
                    <div class="login-inner-wrapper">
                        <?php echo e(csrf_field()); ?>

                        <h2 class="form-heading"><?php echo e(sprintf(trans('website.W0356'),$proposal['talent_name'])); ?></h2>
                        <small class="small-heading"></small>
                        
                        <ul>
                            <li>
                                <span class="plan-main-heading"><?php echo e(trans('website.W0363')); ?></span>
                                <span><?php echo e(___format($proposal['quoted_price'],true,true)); ?></span>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-md-7 col-sm-12 col-xs-12" id="payment-checkout">
                                        
                                    </div>
                                    <div class="col-md-5 col-sm-12 col-xs-12 plan-cost-block">
                                        <span class="total-plan-heading"><?php echo e(trans('website.W0362')); ?></span>
                                        <span class="total-plan-price"><?php echo e(___format( ($payment['transaction_total']),true,true)); ?></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <br/>
                    <?php if($checkPayoutMgmt): ?>
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5 pull-right">   
                                <span class="total-plan-heading"><?php echo e(trans('website.W0939')); ?></span>
                                <button type="button" data-request="inline-ajax-2" data-url="<?php echo e(url('project/payout/mgmt')); ?>" class="button bottom-margin-10px" title="Accept"><?php echo e(trans('website.W0940')); ?></button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div id="paypal-button" style="float:right"></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade upload-modal-box add-payment-cards" id="add-cards" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

<?php $__env->startPush('inlinescript'); ?>
    <style>.trnasferAmountList .right-sidebar{width: inherit;}</style>
    <script>
        function credit_card_number_format(value) {
            var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
            var matches = v.match(/\d{4,16}/g);
            var match = matches && matches[0] || ''
            var parts = []
            for (i=0, len=match.length; i<len; i+=4) {
                parts.push(match.substring(i, i+4))
            }
            
            if (parts.length) {
                return parts.join(' ')
            } else {
                return value
            }
        }

        $(document).on('keypress','#credit_card_number',function(){
            $(this).val(credit_card_number_format($(this).val()));
        });
    </script>
    
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
                url: "<?php echo e(url('/payment/create-payment')); ?>",
                headers: {
                    'x-csrf-token': "<?php echo e(csrf_token()); ?>",
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
                url: "<?php echo e(url('/payment/execute-payment')); ?>",
                headers: {
                    'x-csrf-token': "<?php echo e(csrf_token()); ?>",
                },
                'data':{'paymentID': data.paymentID,'payerID': data.payerID}
            }).then(function(response) {

                if(response.status == true){
                    swal({
                        title: 'Success',
                        html: response.message,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        customClass: 'swal-custom-class',
                        confirmButtonText: $close_botton_text,
                        cancelButtonText: $cancel_botton_text,
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {
                                    if(response.redirect_url){
                                        window.location = response.redirect_url;
                                    }              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }else{
                    swal({
                        title: 'Notification',
                        html: response.message,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        showCloseButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick:false,
                        customClass: 'swal-custom-class',
                        confirmButtonText: $close_botton_text,
                        cancelButtonText: $cancel_botton_text,
                        preConfirm: function (res) {
                            return new Promise(function (resolve, reject) {
                                if (res === true) {    
                                    location.reload();
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }
            });
        }
      }, '#paypal-button');
    </script> 
<?php $__env->stopPush(); ?>