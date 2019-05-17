<?php echo $__env->make('talent.viewprofile.includes.sidebar',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="col-md-8 col-sm-8 right-sidebar m-t-10px">
    <form role="talent_step_four" class="form-horizontal calendar-form" action="<?php echo e(url(sprintf('%s/availability/save',TALENT_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
        <div class="login-inner-wrapper">
            <div class="message"></div>
            <!-- ALL AVAILABILITY -->
            <div class="availability-box">
                <?php echo ___availability_list($user['availability']); ?>

            </div>
            
            <input type="hidden" name="id_availability">
            <div class="calendar-box form-group">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="selected-date-box">
                            <span class="start-date"><?php echo e(trans('website.W0098')); ?></span>
                            <div class="selected-date">
                                <?php echo e(___d(date('Y-m-d',strtotime($selected_date)))); ?>

                            </div>
                        </div>                                    
                    </div>
                    <div class="col-md-8 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0175')); ?></label>
                            <div class="col-md-12">
                                <div class="custom-dropdown">
                                    <select name="year" class="form-control" data-request="calendar" data-url="<?php echo e(url('ajax/validate-calendar')); ?>">
                                        <?php echo ___dropdown_options(___range(range(date('Y'),date('Y')+5)),trans('website.W0103'),date('Y',strtotime($selected_date)),false); ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12"><?php echo e(trans('website.W0176')); ?></label>
                            <div class="col-md-12">
                                <div class="btn-group radio-btn-group month-btn-group" data-toggle="buttons">
                                    <?php $__currentLoopData = array_keys(months()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?><label class="btn <?php if(date('m',strtotime($item)) == date('m',strtotime($selected_date))): ?> active <?php endif; ?>"><input type="radio" data-request="calendar" data-url="<?php echo e(url('ajax/validate-calendar')); ?>" value="<?php echo e(date('m',strtotime($item))); ?>" name="month" id="month-<?php echo e($item); ?>" <?php if(date('m',strtotime($item)) == date('m',strtotime($selected_date))): ?> checked="checked" <?php endif; ?> autocomplete="off"><span class="input-value"><?php echo e($item); ?></span></label><?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12"><?php echo e(trans('website.W0177')); ?></label>
                            <div class="col-md-12">
                                <div class="date-section btn-group radio-btn-group day-btn-group" data-toggle="buttons">
                                    <?php $__currentLoopData = range(1,date('t')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?><label class="btn <?php if(sprintf('%\'.02d',$item) == date('d',strtotime($selected_date))): ?> active <?php endif; ?>"><input type="radio" name="day" data-request="calendar" data-url="<?php echo e(url('ajax/validate-calendar')); ?>" value="<?php echo e(sprintf('%\'.02d',$item)); ?>" id="day-<?php echo e(sprintf('%\'.02d',$item)); ?>" <?php if(sprintf('%\'.02d',$item) == date('d',strtotime($selected_date))): ?> checked="checked" <?php endif; ?> autocomplete="off"><span class="input-value"><?php echo e($item); ?></span></label><?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </div>
                            </div>
                        </div>    
                        <input type="hidden" name="availability_date">
                        <div class="availability-sub-details">                              
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo e(trans('website.W0178')); ?></label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-4 hours-select">
                                            <div class="custom-dropdown">
                                                <select name="from_time_hour" class="form-control">
                                                    <?php echo ___dropdown_options(___range(range(01, 12)),trans('website.W0187'),'13',true); ?>

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
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo e(trans('website.W0179')); ?></label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
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
                            <div class="form-group radio-wrapper">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo e(trans('website.W0180')); ?></label>
                                <div class="col-md-9">
                                    <?php $__currentLoopData = employment_types('talent_availability_2'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <div class="radio radio-inline">                
                                            <input name="repeat" data-request="show-hide" data-condition="weekly" data-target="[name='repeat']" data-true-condition=".weekly-availability-section" data-false-condition=".normal-section" type="radio" id="repeat-<?php echo e($item['type']); ?>" value="<?php echo e($item['type']); ?>" <?php if($item['type'] == 'daily'): ?> checked="checked" <?php endif; ?>>
                                            <label for="repeat-<?php echo e($item['type']); ?>"> <?php echo e($item['type_name']); ?></label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </div>
                            </div>
                            <div class="normal-section"></div>
                            <div class="form-group weekly-availability-section" style="display: none;">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">End Of Repeat</label>
                                <div class="col-md-9 col-sm-9 col-xs-12 message-group">
                                    <div class="btn-group radio-btn-group days-btn-group" data-toggle="buttons">
                                        <?php $__currentLoopData = array_keys(days()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?><label class="btn"><input type="checkbox" value="<?php echo e($item); ?>" name="availability_day[]" id="availability-day-<?php echo e($item); ?>" autocomplete="off"><span class="input-value"><?php echo e($item); ?></span></label><?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </div>
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
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo e(trans('website.W0184')); ?></label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <div class='input-group datepicker' id='datetimepicker'>
                                        <input type='text' class="form-control" name="deadline"/>
                                        <span class="input-group-addon"></span>
                                    </div>
                                </div>
                            </div>
                        </div>     
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group button-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row form-btn-set">
                    <div class="col-md-7 col-sm-7 col-xs-6">
                        <a href="<?php echo e($skip_url); ?>" class="greybutton-line">
                            <?php echo e(trans('website.W0355')); ?>

                        </a>
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-6">
                        <button type="button" class="button" data-box=".availability-box" data-request="multi-ajax" data-target='[role="talent_step_four"]' data-toremove="availability" data-box-id='[name="id_availability"]' data-message=".message">
                            <?php echo e(trans('website.W0185')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>