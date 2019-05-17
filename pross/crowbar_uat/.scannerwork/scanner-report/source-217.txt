<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <link href="<?php echo e(asset('favicon.ico')); ?>" rel="icon">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="<?php echo e(asset("/backend/bootstrap/css/bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset("/backend/dist/css/admin.min.css")); ?>" rel="stylesheet" type="text/css" />
        <?php echo $__env->yieldContent('requirecss'); ?>
        <?php echo $__env->yieldContent('inlinecss'); ?>
        <script>
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
    </head>
    <body class="hold-transition login-page">
        <div id="app">
            <div class="container">
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">    
                            <ul class="dropdown-menu" role="menu">
                                <li>        
                                    <form id="logout-form" action="<?php echo e(url('/logout')); ?>" method="POST" style="display: none;">
                                        <?php echo e(csrf_field()); ?>

                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <script src="<?php echo e(asset ("/backend/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
        <script src="<?php echo e(asset ("/backend/bootstrap/js/bootstrap.min.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset ("/backend/dist/js/app.js")); ?>" type="text/javascript"></script>
        <?php echo $__env->yieldContent('requirejs'); ?>
        <?php echo $__env->yieldContent('inlinejs'); ?>
    </body>
</html>
