    
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
        <script type="text/javascript">
            /*$(document).on('click','[data-request="follow-question"]',function(){
                $('#popup').show(); 
                var $this = $(this);
                var $url    = $this.data('url');
                $.ajax({
                    url: $url, 
                    cache: false, 
                    contentType: false, 
                    processData: false, 
                    type: 'get',
                    success: function($response){
                        $('#popup').hide();
                        if($this.hasClass('active')){
                            $this.removeClass('active');
                            $this.html($response.data);
                            $('.follow_user_'+$response.user_id).removeClass('active');
                            $('.follow_user_'+$response.user_id).html($response.data);
                        }else{
                            $this.addClass('active');
                            $this.html($response.data);
                            $('.follow_user_'+$response.user_id).addClass('active');
                            $('.follow_user_'+$response.user_id).html($response.data);
                        }
                    },error: function(error){
                        $('#popup').hide();
                    }
                });
            });*/
        </script>
    <?php $__env->stopSection(); ?>
    
    <?php $__env->startSection('content'); ?>
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4><?php echo e($title); ?></h4>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                <?php if ($__env->exists($view)) echo $__env->make($view, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make($extends, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>