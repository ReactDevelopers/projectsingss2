<div class="row mainContentWrapper">
    <?php if ($__env->exists('employer.jobdetail.includes.sidebar')) echo $__env->make('employer.jobdetail.includes.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="col-md-8 col-sm-8 right-sidebar">
        <div>
            <ul class="user-profile-links">
                <li class="active">
                    <a href="<?php echo e(url('employer/project/details?job_id='.___encrypt($project->id_project))); ?>">
                        <?php echo e(trans('website.W0678')); ?>

                    </a>
                </li>
                <?php if(!empty($project->proposal)): ?>
                    <li class="resp-tab-item">
                        <a href="<?php echo e(url('employer/project/proposals/talent?proposal_id='.___encrypt($project->proposal->id_proposal).'&project_id='.___encrypt($project->id_project))); ?>">
                            <?php echo e(trans('website.W0722')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if($project->reviews_count > 0): ?>
                    <li class="resp-tab-item">
                        <a href="<?php echo e(url('employer/project/submit/reviews?job_id='.___encrypt($project->id_project))); ?>">
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

                        <div class="row">
                            <div class="col-md-6">
                                <?php if($project->awarded === DEFAULT_NO_VALUE): ?>
                                    <span class="pull-right m-t-15">
                                        <a href="<?php echo e(url(sprintf('employer/hire/talent/edit/one?job_id=%s',___encrypt($project->id_project)))); ?>" title="<?php echo e(trans('website.W0785')); ?>"><img src="<?php echo e(asset('images/edit-icon.png')); ?>" /></a>&nbsp;&nbsp;
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="<?php echo e(url(sprintf('employer/project/delete-job?job_id=%s',___encrypt($project->id_project)))); ?>" data-ask="<?php echo e(trans('website.delete_job_confimation')); ?>" data-title="<?php echo e(trans('website.W0551')); ?>" title="<?php echo e(trans('website.W0784')); ?>"><img src="<?php echo e(asset('images/delete-icon.png')); ?>" /></a>
                                    </span>
                                <?php endif; ?>

                                <?php if($project->project_status === 'pending' && $project->awarded === DEFAULT_YES_VALUE && $project->is_cancelable == DEFAULT_YES_VALUE && $project->is_cancelled == DEFAULT_NO_VALUE): ?>
                                    <span class="pull-right m-t-15">
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="<?php echo e(url(sprintf('employer/project/cancel-job?job_id=%s',___encrypt($project->id_project)))); ?>" data-ask="<?php echo e(sprintf(trans('website.cancel_job_confimation_amt'),$cancellation_amount)); ?>" data-title="<?php echo e(trans('website.W0551')); ?>" title="<?php echo e(trans('website.W0786')); ?>"><img width="20" src="<?php echo e(asset('images/cancel-icon.png')); ?>" /></a>
                                    </span>
                                <?php endif; ?>
                                
                            </div>
                            <div class="col-md-6 social_listing">
                                
                                <div class="dropdown pull-right socialShareDropdown m-t-15">
                                    <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false"><?php echo e(trans('website.W0908')); ?></a>
                                    <ul class="dropdown-menu">
                                        <?php 
                                            /*This is the public link for Job detail*/
                                            $job_detail_url = url('project/show-details/category/'.$job_category.'/job_id/'.___encrypt($project->id_project));
                                         ?>
                                        <li>
                                            <a href="javascript:void(0);" class="linkdin_icon">
                                                <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                                <script type="IN/Share" data-url="<?php echo e($job_detail_url); ?>"></script>
                                                <img src="<?php echo e(asset('images/linkedin.png')); ?>">
                                            </a>
                                        </li>
                                        <li>
                                            <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($job_detail_url); ?>" target="_blank">
                                                <img src="<?php echo e(asset('images/facebook.png')); ?>">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://web.whatsapp.com/send?text=<?php echo e($job_detail_url); ?>" target="_blank
                                            " id="whatsapp_link" data-action="share/whatsapp/share" >
                                            <img src="<?php echo e(asset('images/whatsapp-logo.png')); ?>">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                
                            </div>
                        </div>
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
                        <?php if(!empty($project->proposals)): ?>
                            <div class="item-list">
                                <span class="item-heading m-b-10 clearfix"><?php echo e(trans('website.W0845')); ?></span>
                                <a class="image-tags-wrapper" href="<?php echo e(url(sprintf('%s/project/proposals/detail?id_project=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))); ?>">
                                    <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->proposals),true),'talent'),'company_logo'),'<img src="%s" class="image-tags" />','',false,NULL,5); ?>

                                </a>
                            </div>
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
                            
                            <?php if(!empty($project->industries->count())): ?>
                                <div class="item-list">
                                    <span class="item-heading clearfix"><?php echo e(trans('website.W0655')); ?></span>
                                    <span class="item-description">
                                        <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>',''); ?>

                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($project->skills->count())): ?>
                                <div class="item-list">
                                    <span class="item-heading clearfix"><?php echo e(trans('website.W0206')); ?></span>
                                    <span class="item-description">
                                        <?php echo ___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>',''); ?>

                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($project->subindustries->count())): ?>
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
                                        <span class="f-b"><?php echo e($project->other_perks); ?> <?php echo e(trans('website.W0669')); ?></span>
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
        </div>
        <div class="row form-group button-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <?php if($project->is_cancelled == DEFAULT_NO_VALUE): ?>
                    <div class="row form-btn-set">
                        <?php if(!empty($project->proposal) && ($project->proposal->payment == 'pending' || !empty($project->dispute)) && $project->project_status != 'pending' && $project->project_status != 'closed'): ?>
                            <?php if(empty($project->dispute)): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="<?php echo e(trans('website.W0409')); ?>"><?php echo e(trans('website.W0409')); ?></a>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <a href='<?php echo e(url(sprintf("%s/project/dispute/details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))); ?>' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if($project->project_status !== 'closed' && 0): ?>
                            <?php if(!empty($project->proposal)): ?>
                                <?php if(!empty($project->chat) && in_array($project->chat->chat_initiated,['employer','employer-accepted'])): ?>
                                    <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                        <a href="javascript:void(0);" class="btn btn-secondary" data-request="chat-initiate" data-user="<?php echo e($project->proposal->user_id); ?>" data-url="<?php echo e(url(sprintf('%s/chat',EMPLOYER_ROLE_TYPE))); ?>">
                                           <?php echo e(trans('job.J00126')); ?>

                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                        <a href="javascript:void(0);" class="btn btn-secondary" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->company_id),___encrypt($project->proposal->user_id),___encrypt($project->id_project)))); ?>">
                                           <?php echo e(trans('job.J00127')); ?>

                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if($project->project_status == 'completed'): ?>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button type="button" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/project/status/close?project_id=%s',EMPLOYER_ROLE_TYPE,___decrypt($project->id_project)))); ?>" class="button bottom-margin-10px" title="<?php echo e(trans('job.J00132')); ?>"><?php echo e(trans('job.J00132')); ?></button>
                            </div>
                        <?php elseif($project->project_status == 'closed'): ?>
                            <?php if($project->reviews_count == 0 && $project->awarded != DEFAULT_NO_VALUE): ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <a href="<?php echo e(url(sprintf('%s/project/submit/reviews?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))); ?>" class="button" title="<?php echo e(trans('website.W0719')); ?>"><?php echo e(trans('website.W0719')); ?></a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
        });

        $(document).ready(function(){
            // console.log("readyyyyyyyy");
            // console.log("link is- "+'<?php echo e($job_detail_url); ?>');
            //Change Whatsapp link according to Web or Mobile
            var isMobile1 = window.orientation > -1;
            isMobile1 = isMobile1 ? 'Mobile' : 'Not mobile';

            if(isMobile1 == 'Mobile'){
                //Whatsapp Mobile link share
                $('#whatsapp_link').attr('href','whatsapp://send?text=<?php echo e($job_detail_url); ?>');
            }else{
                //Whatsapp Web link share
                $('#whatsapp_link').attr('href','https://web.whatsapp.com/send?text=<?php echo e($job_detail_url); ?>');
            }
        });
    </script>
<?php $__env->stopPush(); ?>