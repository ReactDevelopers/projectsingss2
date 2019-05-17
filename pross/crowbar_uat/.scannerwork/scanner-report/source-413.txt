<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo e(!empty($title) ? $title.' | '.SITE_TITLE : SITE_TITLE); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="_token" content="<?php echo e(csrf_token()); ?>">
        <link href="<?php echo e(asset('favicon.ico')); ?>" rel="icon">
        <link rel="stylesheet" href="<?php echo e(asset('backend/css/loader.css')); ?>">
        <link href="<?php echo e(asset("/backend/bootstrap/css/bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('/bower_components/sweetalert2/dist/sweetalert2.css')); ?>" rel="stylesheet">
        <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo e(asset("/backend/dist/css/admin.min.css")); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset("/backend/dist/css/skins/skin-black-light.min.css")); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset("/backend/dist/css/custom.css")); ?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo e(asset("/backend/plugins/datatables/dataTables.bootstrap.css")); ?>">
        <?php echo $__env->yieldContent('requirecss'); ?>
        <?php echo $__env->yieldContent('inlinecss'); ?>
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript"> 
            <?php  $agent = new Jenssegers\Agent\Agent;  ?>
            window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token(),]); ?>;
            var $is_mobile_device   = '<?php echo e((!empty($agent->isMobile())?DEFAULT_YES_VALUE:DEFAULT_NO_VALUE)); ?>';
            var $alert_message_text     = '<?php echo e(trans("website.W0548")); ?>';
            var $confirm_botton_text    = '<?php echo e(trans("website.W0551")); ?>';
            var $close_botton_text      = '<?php echo e(trans("website.W0549")); ?>';
            var $no_thanks_botton_text  = '<?php echo e(trans("website.W0552")); ?>';
            var $cancel_botton_text     = '<?php echo e(trans("website.W0550")); ?>';
            
            var month = [
                "<?php echo e(trans('general.M0451')); ?>",
                "<?php echo e(trans('general.M0452')); ?>",
                "<?php echo e(trans('general.M0453')); ?>",
                "<?php echo e(trans('general.M0454')); ?>",
                "<?php echo e(trans('general.M0455')); ?>",
                "<?php echo e(trans('general.M0456')); ?>",
                "<?php echo e(trans('general.M0457')); ?>",
                "<?php echo e(trans('general.M0458')); ?>",
                "<?php echo e(trans('general.M0459')); ?>",
                "<?php echo e(trans('general.M0460')); ?>",
                "<?php echo e(trans('general.M0461')); ?>",
                "<?php echo e(trans('general.M0462')); ?>",
            ];
            
            var weekday = [
                "<?php echo e(trans('general.M0463')); ?>",
                "<?php echo e(trans('general.M0464')); ?>",
                "<?php echo e(trans('general.M0465')); ?>",
                "<?php echo e(trans('general.M0466')); ?>",
                "<?php echo e(trans('general.M0467')); ?>",
                "<?php echo e(trans('general.M0468')); ?>",
                "<?php echo e(trans('general.M0469')); ?>",
            ];
            
            var base_url                = "<?php echo e(url('/')); ?>";
            var asset_url               = "<?php echo e(asset('/')); ?>";
            var $image_upload_text      = "<?php echo e(trans('website.W0623')); ?>";
            var $image_upload_select    = "<?php echo e(trans('website.W0624')); ?>";
        </script>
        <?php echo $__env->yieldContent('inlinejs-top'); ?>
    </head>
    
    <body class="hold-transition skin-black-light sidebar-mini">
        <div class="wrapper">
            <?php echo $__env->make('backend.includes.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('backend.includes.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div id="content-wrapper" class="content-wrapper" style="min-height: 578px;">
                <?php if(!empty($top_buttons)): ?> 
                    <section class="content-header">
                        <span class="pull-right">
                            <?php echo e($top_buttons); ?>

                        </span>
                        <div class="clearfix"><br></div>
                    </section>
                 <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
            <?php echo $__env->make('backend.includes.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
        <script src="<?php echo e(asset ("/backend/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
        <script src="<?php echo e(asset ("/backend/plugins/jQueryUI/jquery-ui.min.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset ("/backend/bootstrap/js/bootstrap.min.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset ("/backend/plugins/datatables/jquery.dataTables.min.js")); ?>"></script>
        <script src="<?php echo e(asset ("/backend/plugins/datatables/dataTables.bootstrap.min.js")); ?>"></script>
        <script src="<?php echo e(asset ("/backend/dist/js/app.min.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset ("/script/common.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('/bower_components/sweetalert2/dist/sweetalert2.min.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script src="<?php echo e(asset ("/js/api.js")); ?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(function () { 
                $.ajaxSetup({ 
                    headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                    isLocal: false
                });
            });</script>
        <?php echo $__env->yieldContent('requirejs'); ?>

        <?php echo $__env->yieldContent('inlinejs'); ?>
        <?php echo $__env->yieldPushContent('inlinescript'); ?>
        <script src="<?php echo e(asset ("/script/backend.js")); ?>" type="text/javascript"></script>
        <div id="popup" class="popup">
            <div class="loading">
                <div class="logo-wrapper"></div>
                <div class="spinning"></div>
            </div>
            <div class="popup_align"></div>
        </div>
    </body>
</html>
