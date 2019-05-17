<?php  
    $header = 'innerheader';
    $footer = 'footer';
    $settings = \Cache::get('configuration');
 ?>

<?php $__env->startSection('content'); ?>
    <div class="container public-job-detail summary-container">
        <div class="row mainContentWrapper">
            <div class="col-md-12 col-sm-12 right-sidebar">
                <div>
                    <ul class="user-profile-links public-job-detail">
                        <li class="active">
                            <b><?php echo e(trans('website.W0678')); ?></b>                                
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    <div class="job-detail-final">
                        <div class="content-box find-job-listing clearfix">
                            <div class="find-job-left no-border">
                                <div class="content-box-header clearfix">
                                    <img src="<?php echo e($project->company_logo); ?>" alt="profile" class="job-profile-image">
                                    <div class="contentbox-header-title">
                                        <h3>
                                            <a><?php echo e($project->title); ?></a>
                                        </h3><br>
                                        <span class="small-tags m-t-5px m-b-5 f-12"><?php echo e($project->project_display_id); ?></span>
                                        <span class="company-name"><?php echo e($project->company_name); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="find-job-right b-l">
                                <div class="contentbox-price-range">
                                    <span>
                                        <?php echo e(___format($project->price,true,true)); ?>

                                        <br>
                                        <?php if($project->employment == 'fixed'): ?>
                                            <span class="label-green color-grey"><?php echo e($project->employment); ?></span>
                                        <?php else: ?>
                                            <span class="small-price-type"><?php echo e(job_types_rates_postfix($project->employment)); ?></span>
                                        <?php endif; ?>
                                    </span>
                                
                                    <?php if(!empty($project->last_viewed)): ?>
                                        <span class="last-viewed-icon active"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="contentbox-minutes clearfix">
                                    <div class="minutes-right">
                                        <?php if($project->is_cancelled == DEFAULT_YES_VALUE): ?>
                                            <span class="posted-time"><?php echo e(trans('general.M0578')); ?> <?php echo e(___ago($project->canceldate)); ?></span>
                                        <?php elseif($project->project_status == 'closed'): ?>
                                            <span class="posted-time"><?php echo e(trans('general.M0520')); ?> <?php echo e(___ago($project->closedate)); ?></span>
                                        <?php else: ?>
                                            <span class="posted-time"><?php echo e(trans('general.M0177')); ?> <?php echo e(___ago($project->created)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($project->project_status === 'pending' && $project->awarded === DEFAULT_YES_VALUE && $project->is_cancelable == DEFAULT_YES_VALUE && $project->is_cancelled == DEFAULT_NO_VALUE): ?>
                                    <span class="pull-right m-t-15">
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="<?php echo e(url(sprintf('employer/project/cancel-job?job_id=%s',___encrypt($project->id_project)))); ?>" data-ask="<?php echo e(trans('website.cancel_job_confimation')); ?>" data-title="<?php echo e(trans('website.W0551')); ?>" title="<?php echo e(trans('website.W0786')); ?>"><img width="20" src="<?php echo e(asset('images/cancel-icon.png')); ?>" /></a>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="job-detail-separator">
                            <div class="">
                                <?php if(!empty($project->proposal) && $project->project_status != 'pending' && 0): ?>
                                    <div class="m-t-10px">
                                        <?php if(!empty($project->projectlog)): ?>
                                            <div class="job-total-time  m-b-10">
                                                <span class="total-time-text"><?php echo e(trans('website.W0706')); ?></span>
                                                <span class="total-time" id="total_working_hours_<?php echo e($project->id_project); ?>"><?php echo e(___hours(substr($project->projectlog->total_working_hours, 0, -3))); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="job-total-time  m-b-10">
                                                <span class="total-time-text"><?php echo e(trans('website.W0706')); ?></span>
                                                <span class="total-time" id="total_working_hours_<?php echo e($project->id_project); ?>"><?php echo e(___hours('00:00')); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <br>
                                <?php endif; ?>
                                <div class="content-box-description">
                                    <?php if(0): ?>
                                        <div class="item-list">
                                            <span class="item-heading clearfix"><?php echo e(trans('website.jobid')); ?></span>
                                            <span class="item-description">
                                                <span class="small-tags"><?php echo e($project->project_display_id); ?></span>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if(!empty($project->industries->count())): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0655')); ?></span>
                                                        <span class="item-description">
                                                            <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>',''); ?>

                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if(!empty($project->expertise)): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0208')); ?></span>
                                                        <span class="item-description">
                                                            <span class="f-b"><?php echo e(ucfirst($project->expertise)); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if(!empty($project->skills->count())): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0206')); ?></span>
                                                        <span class="item-description">
                                                            <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>',''); ?>

                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if(!empty($project->other_perks)): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0658')); ?></span>
                                                        <span class="item-description">
                                                            <span class="f-b"><?php echo e($project->other_perks); ?> <?php echo e(trans('website.W0669')); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if(!empty($project->subindustries->count())): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0207')); ?></span>
                                                        <span class="item-description">
                                                            <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->subindustries),true),'subindustries'),'name'),'<span class="small-tags">%s</span>',''); ?>

                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if(!empty(strtotime($project->startdate) && strtotime($project->enddate))): ?>
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix"><?php echo e(trans('website.W0682')); ?></span>
                                                        <span class="item-description">
                                                            <span class="f-b"><?php echo e(___date_difference($project->startdate, $project->enddate)); ?></span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <?php echo ___e(($project->description)); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">
        .btn-green, .button {
            width: auto;
            display: inline-block;
            float: right;
        }
        .container.public-job-detail {
            clear: both;
        }
        .public-job-detail{
            margin-top: 30px;
        }   
        .user-profile-links li.active {
            background: #f7f7f7;
            display: block;
        }
        .draw-underline {
            border-bottom: 1px solid #d1d3d5!important;
            padding: 10px 0;
        }
    </style>
    <script type="text/javascript" src="<?php echo e(asset('js/bootstrap-timepicker.min.js')); ?>"></script>
    <script type="text/javascript">
        $(function () {
            $("[name=\"working_hours_<?php echo e($project->id_project); ?>\"]").timepicker({
                template: false,
                showMeridian: false,
                defaultTime: "00:00"
            });
        })
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>