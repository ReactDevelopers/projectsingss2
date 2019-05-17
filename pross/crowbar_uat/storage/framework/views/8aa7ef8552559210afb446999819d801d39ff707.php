<div class="col-md-12 col-sm-12 col-xs-12 top-margin-20px">
    <div class="pager text-center"><img src="<?php echo e(asset('images/loader.gif')); ?>"></div>
    <div id="avaibility-calendar" class="avaibility-calendar"></div>
    <div id="add-availability" class="add-availability">
        <a href="javascript:void(0);" data-request="close" data-target=".add-availability" class="close-popup-box"></a>                        
        <h2 class="add-availability-date"><b data-target="availability-day"></b><span data-target="availability-formated-date"></span></h2>
        <span class="availabilty-time-heading"><?php echo e(trans('job.J0060')); ?></span>
        <form role="talent_step_four" action="<?php echo e(url(sprintf('%s/_step_four_set_availability',TALENT_ROLE_TYPE))); ?>" method="POST" class="form-horizontal calander-form">
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo e(trans('website.W0178')); ?></label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-4 hours-select">
                            <div class="custom-dropdown">
                                <select name="from_time_hour" class="form-control">
                                    <?php echo ___dropdown_options(___range(range(00, 12)),trans('website.W0187'),'13',true); ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4 minutes-select">
                            <div class="custom-dropdown">
                                <select name="from_time_minute" class="form-control">
                                    <?php echo ___dropdown_options(___range(range(00, 59,5)),trans('website.W0188'),'60',true); ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4 meridian-select">
                            <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?php if(date('a',strtotime($selected_date)) == 'am'): ?> active <?php endif; ?>">
                                    <input type="radio" value="AM" name="from_time_meridian" <?php if(date('a',strtotime($selected_date)) == 'am'): ?> checked="checked" <?php endif; ?> id="from_time_am" autocomplete="off"> <?php echo e(trans('website.W0189')); ?>

                                </label>
                                <label class="btn btn-default <?php if(date('a',strtotime($selected_date)) == 'pm'): ?> active <?php endif; ?>">
                                    <input type="radio" value="PM" name="from_time_meridian" <?php if(date('a',strtotime($selected_date)) == 'pm'): ?> checked="checked" <?php endif; ?> id="from_time_pm" autocomplete="off"> <?php echo e(trans('website.W0190')); ?>

                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="hidden" name="from_time">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo e(trans('website.W0179')); ?></label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-4 hours-select">
                            <div class="custom-dropdown">
                                <select name="to_time_hour" class="form-control">
                                    <?php echo ___dropdown_options(___range(range(00, 12)),trans('website.W0187'),'13',true); ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4 minutes-select">
                            <div class="custom-dropdown">
                                <select name="to_time_minute" class="form-control">
                                    <?php echo ___dropdown_options(___range(range(00, 59,5)),trans('website.W0188'),'60',true); ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4 meridian-select">
                            <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?php if(date('a',strtotime($selected_date)) == 'am'): ?> active <?php endif; ?>">
                                    <input type="radio" value="AM" name="to_time_meridian" <?php if(date('a',strtotime($selected_date)) == 'am'): ?> checked="checked" <?php endif; ?> id="to_time_am" autocomplete="off"> <?php echo e(trans('website.W0189')); ?>

                                </label>
                                <label class="btn btn-default <?php if(date('a',strtotime($selected_date)) == 'pm'): ?> active <?php endif; ?>">
                                    <input type="radio" value="PM" name="to_time_meridian" <?php if(date('a',strtotime($selected_date)) == 'pm'): ?> checked="checked" <?php endif; ?> id="to_time_pm" autocomplete="off"> <?php echo e(trans('website.W0190')); ?>

                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="hidden" name="to_time">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo e(trans('website.W0180')); ?></label>
                <div class="col-md-9">
                    <?php $__currentLoopData = employment_types('talent_availability'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <div class="radio radio-inline">                
                            <input name="repeat" data-request="show-hide" data-condition="weekly" data-target="[name='repeat']" data-true-condition=".weekly-availability-section" data-false-condition=".normal-section" type="radio" id="repeat-<?php echo e($item['type']); ?>" value="<?php echo e($item['type']); ?>" <?php if($item['type'] == 'daily'): ?> checked="checked" <?php endif; ?>>
                            <label for="repeat-<?php echo e($item['type']); ?>"> <?php echo e($item['type_name']); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </div>
            </div>
            <div class="form-group radio-wrapper">
                <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo e(trans('website.W0612')); ?></label>
                <div class="col-md-9">
                    <?php $__currentLoopData = availablity_type(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <div class="radio radio-inline">
                            <input name="availability_type" data-request="show-hide" data-target="[name='availability_type']" data-false-condition=".normal-section" type="radio" id="repeat-<?php echo e($item['type']); ?>" value="<?php echo e($item['type']); ?>" <?php if($item['type'] == 'available'): ?> checked="checked" <?php endif; ?>>
                            <label for="repeat-<?php echo e($item['type']); ?>"> <?php echo e($item['type_name']); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </div>
            </div>
            <div class="normal-section"></div>
            <div class="form-group weekly-availability-section" style="display: none;">
                <label class="control-label col-md-3">Set Start Date</label>
                <div class="col-md-9 message-group">
                    <div class="btn-group radio-btn-group days-btn-group" data-toggle="buttons">
                        <?php $__currentLoopData = array_keys(days()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?><label class="btn"><input type="checkbox" value="<?php echo e($item); ?>" name="availability_day[]" id="availability-day-<?php echo e($item); ?>" autocomplete="off"><span class="input-value"><?php echo e($item); ?></span></label><?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo e(trans('website.W0271')); ?></label>
                <div class="col-md-9">
                    <div class='input-group datepicker' data-request="availability-date">
                        <input type='text' class="form-control" name="availability_date"/>
                        <span class="input-group-addon"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo e(trans('website.W0184')); ?></label>
                <div class="col-md-9">
                    <div class='input-group datepicker' data-request="deadline">
                        <input type='text' class="form-control" name="deadline"/>
                        <span class="input-group-addon"></span>
                    </div>
                </div>
            </div>
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-7 col-sm-7 col-xs-6">
                            <a href="javascript:void(0);" data-request="close" data-target=".add-availability" class="greybutton-line">
                                <?php echo e(trans('website.W0186')); ?>

                            </a>
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-6">
                            <a href="javascript:void(0);" data-box=".availability-box" data-request="multi-ajax-calendar" data-target='[role="talent_step_four"]' data-toremove="availability" data-box-id='[name="id_availability"]' data-message=".message" data-success="<?php echo e(trans('website.W0244')); ?>" class="button">
                                <?php echo e(trans('website.W0013')); ?>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <span class="popup-arrow"></span>
    </div>
    <div data-request="profile-calendar" data-target="#avaibility-calendar" data-url="<?php echo e(url(sprintf('%s/get-availability',TALENT_ROLE_TYPE))); ?>">
    </div>
</div>

<?php $__env->startPush('inlinescript'); ?>
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('/script/calendar.js')); ?>" type="text/javascript"></script>
    <script>
        $(function(){
            var startdate = $('[data-request="availability-date"]').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: new Date()
            });

            var enddate = $('[data-request="deadline"]').datetimepicker({
                format: 'DD/MM/YYYY',
                useCurrent: false,
                minDate: new Date()
            });

            $("[data-request='availability-date']").on("dp.change", function (e) {
                $("[data-request='deadline']").data("DateTimePicker").minDate(e.date);
            });
            $("[data-request='deadline']").on("dp.change", function (e) {
                $("[data-request='availability-date']").data("DateTimePicker").maxDate(e.date);
            });            
        });
    </script>
<?php $__env->stopPush(); ?>
