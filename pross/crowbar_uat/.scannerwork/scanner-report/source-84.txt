<form role="paypal" method="POST" action="<?php echo e(url(sprintf('%s/__payments',TALENT_ROLE_TYPE))); ?>" class="form-horizontal" autocomplete="off">
    <?php echo e(csrf_field()); ?>

    <div class="login-inner-wrapper setting-wrapper transfer-owner-ship">
        <h2><?php echo e(trans('website.W0975')); ?></h2>
        <div class="col-md-7 col-sm-12 col-xs-12">
            <div class="form-group">
                <a href="javascript:void(0);" class="button" data-target="#add-member" data-request="ajax-modal" data-url="<?php echo e(url(sprintf('%s/accept-transfer-ownership',TALENT_ROLE_TYPE))); ?>" href="javascript:void(0);">
                <?php echo e(trans('website.W0976')); ?>

                </a>
            </div>                                
        </div>               
    </div>                                
</form>
<div class="modal fade upload-modal-box add-payment-cards" id="add-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<?php $__env->startPush('inlinescript'); ?>
    <link href="<?php echo e(asset('css/hidePassword.css')); ?>" rel="stylesheet">
    <script type="text/javascript" src="<?php echo e(asset('js/hideShowPassword.js')); ?>"></script>
    <script type="text/javascript">$('[name="new_password"]').hidePassword(true);</script>

    <script type="text/javascript">
        $(document).ready(function(){
            var verified_paypal_email = false;
            verified_paypal_email = "<?php echo e($verified_paypal_email); ?>";

            var message = "<?php echo e($returnMessage); ?>";

            if(verified_paypal_email == true){
                swal({
                    title: 'Notification',
                    html: 'Your details have been saved. '+message,
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
                                window.location = "<?php echo e(url(sprintf('%s/settings/payments',TALENT_ROLE_TYPE))); ?>";
                                resolve();              
                            }
                        })
                    }
                }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
            }
        });
    </script>
    <script type="text/javascript">
        (function(d, s, id){
            var js, ref = d.getElementsByTagName(s)[0]; 
            if (!d.getElementById(id)){
                js = d.createElement(s); 
                js.id = id; 
                js.async = true;
                js.src = "https://www.sandbox.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                ref.parentNode.insertBefore(js, ref); }
        }(document, "script", "paypal-js"));
    </script>
<?php $__env->stopPush(); ?>