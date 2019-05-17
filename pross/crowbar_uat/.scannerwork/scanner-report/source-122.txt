<div class="row mainContentWrapper">
    <div class="col-md-4 col-sm-4 left-sidebar clearfix">
        <?php if ($__env->exists('employer.jobdetail.talent')) echo $__env->make('employer.jobdetail.talent', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="col-md-8 col-sm-8 right-sidebar"> 
        <div>
            <div class="message">
                <?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

            </div>    
            <ul class="user-profile-links">
                <li class="resp-tab-item">
                    <a href="<?php echo e(url('employer/project/details?job_id='.___encrypt($project->id_project))); ?>">
                        <?php echo e(trans('website.W0678')); ?>

                    </a>
                </li>
                <li class="active">
                    <a href="<?php echo e(url('employer/project/proposals/talent?proposal_id='.___encrypt($project->proposal->id_proposal).'&project_id='.___encrypt($project->id_project))); ?>">
                        <?php if(!empty($project->proposals_count)): ?>
                            <?php echo e(trans('website.W0722')); ?>

                        <?php else: ?>
                            <?php echo e(trans('website.W0343')); ?>

                        <?php endif; ?>
                    </a>
                </li>
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
                    <div class="view-information no-padding">
                        <h2><?php echo e(trans('website.W0689')); ?> </h2>
                    </div>
                    <?php if(!empty($companydata)): ?>
                        <div class="company-name-wrapper">
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">Company Name</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info"><?php echo e($companydata->company_name); ?></span>
                                    </div>
                            </div>
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">Company Website</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info"><?php echo e($companydata->company_website); ?></span>
                                    </div>
                            </div>
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">About the Company</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info"><?php echo e($companydata->company_biography); ?></span>
                                    </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    <?php endif; ?>
                    <div class="col-md-7">
                        <div class="content-box-description m-b-20px">
                            <div class="view-information no-padding">
                                <h2><?php echo e(trans('website.W0989')); ?> </h2>
                            </div>
                            <?php if(!empty($project->proposal->quoted_price)): ?>
                                <div class="item-list">
                                    <span class="item-heading clearfix"><?php echo e(trans('website.W0363')); ?></span>
                                    <span class="item-description">
                                        <span class="small-tags"><?php echo e(___format($project->proposal->quoted_price,true,true,true)); ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if($project->employment == 'hourly'): ?>
                                <div class="item-list">
                                    <span class="item-heading clearfix"><?php echo e(trans('website.W0757')); ?></span>
                                    <span class="item-description">
                                        <?php if(!empty($project->proposal->working_hours)): ?>
                                            <span class="small-tags"><?php echo e(substr($project->proposal->working_hours, 0, -3)); ?> <?php echo e(trans('website.W0759')); ?></span>
                                        <?php else: ?>
                                            <span class="small-tags"><?php echo e('00:00'); ?> <?php echo e(trans('website.W0759')); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="m-b-20px">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0664')); ?></span>
                            <?php if(!empty($project->proposal->comments)): ?>
                                <?php echo ___e(nl2br($project->proposal->comments)); ?>

                            <?php else: ?>
                                <?php echo e(N_A); ?>

                            <?php endif; ?>
                        </div>
                        
                        <div class="m-b-10px">
                            <span class="item-heading clearfix"><?php echo e(trans('website.W0691')); ?></span>
                            <?php if(!empty($project->proposal->file)): ?>
                                <?php if ($__env->exists('talent.jobdetail.includes.attachment',['file' => json_decode(json_encode($project->proposal->  file),true)])) echo $__env->make('talent.jobdetail.includes.attachment',['file' => json_decode(json_encode($project->proposal->  file),true)], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php else: ?>
                                <?php echo e(N_A); ?>

                            <?php endif; ?>
                        </div>

                        <span class="review-time">
                            <?php echo e(trans('website.W0690')); ?> <?php echo e(___d($project->proposal->created)); ?>

                        </span>
                    </div>
                    <div class="col-md-5">
                        <div class="employer-detail-box">
                            <h2 class="heading-sm m-b-20px"><?php echo e(trans('website.W0652')); ?></h2>
                            <div class="form-group">
                                <h2 class="small-heading"><?php echo e(trans('website.W0094')); ?></h2>
                                <span><?php echo e($project->title); ?></span>
                            </div>
                            <div class="form-group price-list">
                                <h2 class="small-heading"><?php echo e(trans('website.W0846')); ?></h2>
                                <span>
                                    <?php echo e(___format($project->price,true,true)); ?> / 
                                    <?php if($project->employment == 'fixed'): ?>
                                        <?php echo e($project->employment); ?>

                                    <?php else: ?>
                                        <?php echo e(job_types_rates_postfix($project->employment)); ?>

                                    <?php endif; ?>
                                </span>
                            </div>  
                            <div class="form-group timeline">
                                <h2 class="small-heading"><?php echo e(trans('website.W0682')); ?></h2>
                                <span>
                                    <?php if(!empty(strtotime($project->startdate) && strtotime($project->enddate))): ?>
                                        <?php echo e(___date_difference($project->startdate, $project->enddate)); ?>

                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if($project->employment == 'hourly'): ?>
                                <div class="form-group">
                                    <h2 class="small-heading"><?php echo e(trans('website.W0793')); ?></h2>
                                    <span>
                                        <?php if(!empty(strtotime($project->expected_hour))): ?>
                                            <?php echo e(___hours($project->expected_hour)); ?>

                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <div class="form-group cancelation">
                                <h2 class="small-heading"><?php echo e(trans('website.W0930')); ?></h2>
                                <?php 
                                    $commission = ___cache('configuration')['cancellation_commission'];
                                    $commission_type = ___cache('configuration')['cancellation_commission_type'];

                                    if($commission_type == 'per'){
                                        $calculated_commission=___format(round(((($project->price*$commission)/100)),2)); 
                                    }else{
                                        $calculated_commission = ___format(round(($commission),2));
                                    }

                                    $refundable_amount = $project->price - $calculated_commission;
                                 ?>
                                <span>
                                <?php echo e(___format($refundable_amount,true,true)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php if(empty($project->proposal)): ?>
                    <div class="row form-group button-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row form-btn-set">
                                <div class="col-md-7 col-sm-7 col-xs-6">
                                    <a href="<?php echo e(url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))); ?>" class="greybutton-line"><?php echo e(trans('job.J0028')); ?></a>
                                </div>
                                <div class="col-md-5 col-sm-5 col-xs-6">
                                    <button id="doc-button" type="button" data-request="trigger-proposal" data-target="#proposal-form" data-copy-source='[name="documents[]"]' data-copy-destination='[name="proposal_docs"]' class="button" value="Submit">
                                            <?php echo e(trans('website.W0013')); ?>

                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if(empty($project->proposal_count) && $project->project_status == 'pending'): ?>
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">                                    
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="button" class="pull-right button-line" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/proposals/decline?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___decrypt($project->proposal->id_proposal),___decrypt($proposal->project_id)))); ?>"><?php echo e(trans('website.W0220')); ?></button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <a href="<?php echo e(url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project),___encrypt($project->proposal->id_proposal)))); ?>" class="button" title="<?php echo e(trans('website.W0221')); ?>"><?php echo e(trans('website.W0221')); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->startPush('inlinecss'); ?>
    <style>
        .education-box .edit-icon, .work-experience-box .edit-icon, [data-request="delete"]{display: none!important;}
        span.last-viewed-icon{
            position: relative;
            top: -8px;
        }
        .new-upload .uploaded-docx{max-width: 100%;}
    </style>
<?php $__env->stopPush(); ?>
