<?php echo $__env->make('employer.job.includes.talent-profile-menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="inner-profile-section talent-profile-section">
    <div class="white-wrapper view-information m-b-5" id="personal-infomation">
        <div class="pager text-center"><img src="<?php echo e(asset('images/loader.gif')); ?>"></div>
        <div id="avaibility-calendar" class="no-padding"></div>
        <div data-request="profile-calendar" data-target="#avaibility-calendar" data-url="<?php echo e(url(sprintf('%s/get-talents-availability?talent_id=%s',EMPLOYER_ROLE_TYPE,Request::get('talent_id')))); ?>">
        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">
        .fc-scroller.fc-day-grid-container {
            overflow-y: auto !important;
        }
    </style>
    <!-- <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.print.css" rel="stylesheet"> -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('/script/calendar.js')); ?>" type="text/javascript"></script>
<?php $__env->stopPush(); ?>
