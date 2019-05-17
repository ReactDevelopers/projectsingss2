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
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic,greek,latin-ext" rel="stylesheet">
        <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
        <?php echo $__env->yieldContent('requirecss'); ?>    
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo e(asset('/bower_components/sweetalert2/dist/sweetalert2.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/dataTables.bootstrap.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('css/slick.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('css/loader.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/responsive.css')); ?>" rel="stylesheet">
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
            var $proposal_botton_text   = '<?php echo e(trans("website.W0753")); ?>';
            var $cancel_botton_text     = '<?php echo e(trans("website.W0550")); ?>';
            var $userID                 = '0';

            <?php if(auth()->guard('web')->check()): ?>
                var $userID = '<?php echo e(auth()->user()->id_user); ?>';
            <?php endif; ?>
            
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
            var $valid_email_note       = "<?php echo e(trans('website.W0848')); ?>";
        </script>
        <script src="https://use.fontawesome.com/e26fdfdad2.js"></script>
        <?php echo \Cache::get('configuration')['google_analytics_code']; ?>

        <?php echo $__env->yieldPushContent('inlinecss'); ?>
        <?php echo $__env->yieldContent('inlinecss'); ?>
    </head>

    <body>
        <div class="wrapper">
            <?php echo $__env->make(sprintf('%s.includes.%s',FRONT_FOLDER,$header), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->yieldContent('content'); ?>

            <?php echo $__env->yieldContent('temp-popup'); ?>
            <?php echo $__env->make('footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            
        </div>
        <div id="popup" class="popup">
            <div class="loader">
                <div class="spinning">
                    <img src="<?php echo e(asset('images/loading.png')); ?>" style="border-radius: 30px;"/>
                    <span class="loader-text"><?php echo trans('website.W0672'); ?></span>
                </div>
            </div>
            <div class="popup_align"></div>
        </div>
        <script src="<?php echo e(asset('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/bootstrap.min.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script src="<?php echo e(asset('/bower_components/sweetalert2/dist/sweetalert2.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/share.js')); ?>"></script>
        <script src="<?php echo e(asset ("/script/common.js")); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('/js/slick.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/chat/notification.js')); ?>"></script>
        <script type="text/javascript">
            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },isLocal: false
                });
            });
        </script>
        <?php echo $__env->yieldContent('requirejs'); ?>
        <?php echo $__env->yieldContent('inlinejs'); ?>
        <?php echo $__env->yieldPushContent('inlinescript'); ?>
        <script src="<?php echo e(asset('/script/app.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/app.js')); ?>"></script>
        <div class="modal fade upload-modal-box" id="select-type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    </body>

</html>
