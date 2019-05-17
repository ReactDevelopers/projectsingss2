<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="_token" content="<?php echo e(csrf_token()); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
        <link href="<?php echo e(asset('favicon.ico')); ?>" rel="icon">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title><?php echo e(!empty($title) ? $title.' | '.SITE_TITLE : SITE_TITLE); ?></title>
        <!-- Google Font -->
        <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
        <?php echo $__env->yieldContent('requirecss'); ?>    
        <link href="<?php echo e(asset('css/dataTables.bootstrap.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
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
        <?php echo \Cache::get('configuration')['google_analytics_code']; ?>

        <?php echo $__env->yieldContent('inlinecss'); ?>
    </head>

    <body>
        <div class="wrapper">
            <?php echo $__env->yieldContent('content'); ?>
        </div>        
        <script src="<?php echo e(asset('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/bootstrap.min.js')); ?>"></script>
        <script type="text/javascript">
            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
            });
        </script>
        <?php echo $__env->yieldContent('requirejs'); ?>
        <?php echo $__env->yieldContent('inlinejs'); ?>
        <?php echo $__env->yieldPushContent('inlinescript'); ?>
    </body>
</html>
