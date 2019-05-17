    
    <?php $__env->startSection('requirecss'); ?>
        <link href="<?php echo e(asset('css/jquery.easyselect.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/easy-responsive-tabs.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery.nstSlider.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/cropper.min.css')); ?>" rel="stylesheet">
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/moment.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/easyResponsiveTabs.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/jquery.nstSlider.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/custom.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('js/cropper.min.js')); ?>" type="text/javascript"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        <script type="text/javascript">
            $(".cropper").SGCropper({
                viewMode: 1,
                aspectRatio: "2/3",
                cropBoxResizable: false,
                formContainer:{
                    actionURL:"<?php echo e(url(sprintf('ajax/crop?imagename=image&user_id=%s',Auth::user()->id_user))); ?>",
                    modelTitle:"<?php echo e(trans('website.W0261')); ?>",
                    modelSuggestion:"<?php echo e(trans('website.W0263')); ?>",
                    modelDescription:"<?php echo e(trans('website.W0264')); ?>",
                    modelSeperator:"<?php echo e(trans('website.W0265')); ?>",
                    uploadLabel:"<?php echo e(trans('website.W0266')); ?>",
                    fieldLabel:"",
                    fieldName: "image",
                    btnText:"<?php echo e(trans('website.W0262')); ?>",
                    defaultImage: "../images/product_sample.jpg",
                    loaderImage: "../images/loader.gif",
                }
            });
        </script>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="greyBar-Heading invite-talent-heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4><?php echo e($title); ?></h4>
                    </div>                    
                </div>
            </div>
        </div>

        <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        
    <?php $__env->stopSection(); ?>

   
    <?php $__env->startPush('inlinescript'); ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url: '<?php echo e(url(TALENT_ROLE_TYPE.'/talent-connect/validate-talent/')); ?>', 
                    type: 'post', 
                    success: function($response){
                        if($response.message){
                            swal({
                            title: $alert_message_text,
                            html: $response.message,
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
                                        if($response.redirect){
                                            window.location = $response.redirect;
                                        }              
                                    }
                                    resolve();
                                })
                            }
                            }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                        }
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.talent.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>