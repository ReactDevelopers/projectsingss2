<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>

    <!-- Fonts -->
    
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link href="<?php echo e(mix(Spark::usesRightToLeftTheme() ? 'css/app-rtl.css' : 'css/app.css')); ?>" rel="stylesheet">

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Global Spark Object -->
    <?php echo $__env->make('partials.common.global-spark-object', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</head>
<body>
    <div class="alert alert-warning offline-notification">
        <?php echo app('translator')->getFromJson('Not Connected! Offline Mode.'); ?>
    </div>
    <div id="spark-app" v-cloak>
        <!-- Navigation -->
        <?php if(Auth::check()): ?>
            <?php echo $__env->make('spark::nav.user', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('spark::nav.guest', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <!-- Application Level Modals -->
        <?php if(Auth::check()): ?>
            <?php echo $__env->make('spark::modals.notifications', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('spark::modals.support', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('spark::modals.session-expired', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
    </div>

    <!-- JavaScript -->
    <script src="<?php echo e(mix('js/app.js')); ?>"></script>
    <script src="/js/sweetalert.min.js"></script>
    <script src="https://cdn.logrocket.io/LogRocket.min.js" crossorigin="anonymous"></script>
    <!-- <script>window.LogRocket && window.LogRocket.init('rzptha/contrat-lodge');</script> -->

    <?php if(Auth::check()): ?>
        <!-- <script>
            LogRocket.identify('rzptha:contrat-lodge:Ae0gApAz3EnwWQMvm9o3', {
                name: '<?php echo Auth::user()->name; ?>',
                email: '<?php echo Auth::user()->email; ?>',
            });
        </script> -->
    <?php endif; ?>

</body>
</html>
