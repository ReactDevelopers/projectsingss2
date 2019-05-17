<div>
    <ul class="user-profile-links">
        <li class="active">
            <a href="<?php echo e(url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0678')); ?>

            </a>
        </li>
        <li class="resp-tab-item">
            <a href="<?php echo e(url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0679')); ?>

            </a>
        </li>
        <li>
            <a href="<?php echo e(url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0680')); ?>

            </a>
        </li>
        <?php if(!empty($project->reviews_count)): ?>
            <li class="resp-tab-item">
                <a href="<?php echo e(url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))); ?>">
                    <?php echo e(trans('website.W0721')); ?>

                </a>
            </li>
        <?php endif; ?>
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
                    <?php if(0): ?>
                        <?php if(!empty($project->is_saved == DEFAULT_YES_VALUE)): ?>
                            <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="<?php echo e(url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))); ?>"></a>
                        <?php else: ?>
                            <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="<?php echo e(url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))); ?>"></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <br>
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
            </div>
            <div class="clearfix"></div>
            <hr class="job-detail-separator">
            <div>
                <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted' && $project->project_status == 'initiated' && 0): ?>
                    <div class="m-t-10px">
                        <?php if(0): ?>
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
                        <?php endif; ?>
                        <div class="jobtimer white-wrapper m-b-10">
                            <div class="submit-timesheet">
                                <form role="working-hours-<?php echo e($project->id_project); ?>" method="post" action="<?php echo e(url('talent/save/working/hours?project_id='.$project->id_project)); ?>" autocomplete="off" >
                                    <div class="row">
                                        <div class="col-md-3 col-xs-3 col-sm-3">
                                            <label class="control-label m-t-5px"><?php echo e(trans('website.W0700')); ?></label>
                                        </div>
                                        <div class="col-md-6 col-xs-6 col-sm-6">
                                            <div class="form-group no-margin-bottom">
                                                <input type="text" name="working_hours_<?php echo e($project->id_project); ?>" autocomplete="off" class="form-control" placeholder="<?php echo e(trans('website.W0701')); ?>" />
                                            </div>
                                            <input type="text" class="hide"/>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3">
                                            <button class="btn btn-sm redShedBtn small-button" type="button" data-request="ajax-submit" data-target='[role="working-hours-<?php echo e($project->id_project); ?>"]' ><?php echo e(trans('website.W0013')); ?></button>
                                        </div>                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php elseif($project->project_status == 'closed'): ?>
                    <div class="m-t-10px">
                        <?php if(0): ?>
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
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="content-box-description no-padding">
                    <?php if(!empty($project->industries->count())): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0655')); ?></span>
                            <span class="item-description">
                                <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>',''); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($project->subindustries->count())): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0206')); ?></span>
                            <span class="item-description">
                                <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>',''); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($project->skills->count())): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0207')); ?></span>
                            <span class="item-description">
                                <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->subindustries),true),'subindustries'),'name'),'<span class="small-tags">%s</span>',''); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($project->expertise)): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0208')); ?></span>
                            <span class="item-description">
                                <span class="f-b"><?php echo e(ucfirst($project->expertise)); ?></span>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($project->other_perks)): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0658')); ?></span>
                            <span class="item-description">
                                <span class="f-b"><?php echo e($project->other_perks); ?> <?php echo e(trans('website.W0751')); ?></span>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty(strtotime($project->startdate) && strtotime($project->enddate))): ?>
                        <div class="item-list">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0682')); ?></span>
                            <span class="item-description">
                                <span class="f-b"><?php echo e(___date_difference($project->startdate, $project->enddate)); ?></span>
                            </span>
                        </div>
                    <?php endif; ?>
                    <br/>
                    <div>
                        <span class="item-description">
                            <span class="f-b"><?php echo e(trans('website.W0925')); ?></span>
                        </span>
                    </div>
                    <?php echo ___e(($project->description)); ?>

                </div>
            </div>
        </div>
    </div>

    <?php if($project->is_cancelled == DEFAULT_NO_VALUE): ?>
        <?php if(empty($project->proposal) && $project->project_status != 'closed' && $project->awarded === DEFAULT_NO_VALUE): ?>
            <a href="<?php echo e(url(sprintf('%s/find-jobs/proposal?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>" class="button bottom-margin-10px" title="<?php echo e(trans('job.J0013')); ?>"><?php echo e(trans('job.J0013')); ?></a>
        <?php elseif($project->project_status != 'closed'): ?>
            <?php if(!empty($project->proposal) && $project->project_status != 'closed' && $project->awarded === DEFAULT_NO_VALUE && $project->status != 'trashed'): ?>
                <a href="<?php echo e(url(sprintf('%s/find-jobs/proposal?job_id=%s&action=edit',TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>" class="btn btn-secondary bottom-margin-10px pull-right" title="<?php echo e(trans('website.W0810')); ?>"><?php echo e(trans('website.W0810')); ?></a>
            <?php endif; ?>
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <?php if(!empty($project->dispute) && !empty($project->proposal)): ?>
                            <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                <?php if(empty($project->dispute)): ?>
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="<?php echo e(trans('website.W0409')); ?>"><?php echo e(trans('website.W0409')); ?></a>
                                <?php else: ?>
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($project->chat)): ?>
                            <?php if(empty($project->chat->chat_initiated) && !empty($project->proposal) && $project->proposal->status === 'accepted'): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <div class="send-chat-request">
                                        <button type="button" data-request="send-chat-request" data-receiver="<?php echo e($project->company_id); ?>" data-sender="<?php echo e($project->proposal->user_id); ?>" data-project="<?php echo e($project->id_project); ?>" data-target=".send-chat-request" data-url="<?php echo e(url(sprintf('%s/chat/initiate-chat-request',TALENT_ROLE_TYPE))); ?>" class="btn btn-secondary" title="<?php echo e(trans('job.J0062')); ?>"><?php echo e(trans('job.J0062')); ?></button>    
                                    </div>
                                </div>
                            <?php elseif($project->chat->chat_initiated === 'talent'): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <div class="send-chat-request">
                                        <button class="btn btn-secondary" title="<?php echo e(trans('job.J0063')); ?>"><?php echo e(trans('job.J0063')); ?></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted'): ?>
                            <?php if($project->project_status == 'pending'): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="button" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/project/status/start?project_id=%s',TALENT_ROLE_TYPE,___decrypt($project->id_project)))); ?>" class="button bottom-margin-10px" title="<?php echo e(trans('job.J0053')); ?>"><?php echo e(trans('job.J0053')); ?></button>
                                </div>
                            <?php elseif($project->project_status == 'initiated'): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="button" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/project/status/close?project_id=%s',TALENT_ROLE_TYPE,___decrypt($project->id_project)))); ?>" class="button bottom-margin-10px" title="<?php echo e(trans('job.J0054')); ?>"><?php echo e(trans('job.J0054')); ?></button>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <?php if(!empty($project->dispute) && !empty($project->proposal)): ?>
                            <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                <?php if(empty($project->dispute)): ?>
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="<?php echo e(trans('website.W0409')); ?>"><?php echo e(trans('website.W0409')); ?></a>
                                <?php else: ?>
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($project->project_status == 'closed' && $project->reviews_count == 0 && (!empty($project->proposal) && $project->proposal->talent->id_user == $user['id_user'])): ?>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <a href="<?php echo e(url(sprintf('%s/project/submit/reviews?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project->id_project)))); ?>" class="button bottom-margin-10px" title="<?php echo e(trans('website.W0719')); ?>"><?php echo e(trans('website.W0719')); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">
        .btn-green, .button {
            width: auto;
            display: inline-block;
            float: right;
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