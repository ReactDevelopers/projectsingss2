<form role="form" method="post" enctype="multipart/form-data" action="<?php echo e(url($url.'/talent-users/'.$user['id_user'].'/update-education')); ?>">
    <input type="hidden" name="_method" value="PUT">
    <?php echo e(csrf_field()); ?>


    <div class="panel-body">
        <div class="pager text-center"><img src="<?php echo e(asset('images/loader.gif')); ?>"></div>
        <div id="avaibility-calendar" class="avaibility-calendar"></div>

        <div data-request="profile-calendar" data-user_id="<?php echo e($id_user); ?>" data-target="#avaibility-calendar" data-url="<?php echo e(url(sprintf('%s/get-availability',ADMIN_FOLDER))); ?>">
    </div>
</form>
<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('/js/moment.min.js')); ?>" type="text/javascript"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('/backend/js/calendar.js')); ?>" type="text/javascript"></script>
    <script>
        $(function(){
            $("[data-request='availability-date']").on("dp.change", function (e) {
                $("[data-request='deadline']").data("DateTimePicker").minDate(e.date);
            });
            $("[data-request='deadline']").on("dp.change", function (e) {
                $("[data-request='availability-date']").data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>
<?php $__env->stopPush(); ?>
