<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('<?php echo e($user['picture']); ?>') no-repeat center center;background-size:100% 100%"></div>
                </div>
            </div>
            <div class="user-name-info">
                <p><?php echo e(sprintf("%s %s",$user['first_name'],$user['last_name'])); ?></p>
                <small class="small-info"><?php echo e(trans('website.W0439')); ?> <?php echo e(date('Y', strtotime($user['created']))); ?></small>
            </div>
        </div>
        <div class="profile-right">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            <?php echo ___ratingstar($user['rating']); ?>

                        </span>
                        <a href="<?php echo e(url(sprintf('%s/profile/reviews',EMPLOYER_ROLE_TYPE))); ?>" class="reviews-block underline"><?php echo e($user['review']); ?> <?php echo e(trans('website.W0213')); ?></a>
                    </div>
                </div>
                <?php if(!empty($user['company_name'])): ?>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0096')); ?></span>
                        <span class="item-description"><?php echo e($user['company_name']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if(!empty($user['country_name'])): ?>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0201')); ?></span>
                        <span class="item-description"><?php echo e($user['country_name']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if(!empty($user['job_posted'])): ?>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0378')); ?></span>
                        <span class="item-description"><?php echo e($user['job_posted']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if(!empty($user['paid_till_date'])): ?>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0668')); ?></span>
                        <span class="item-description"><?php echo e($user['paid_till_date']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if(!empty($project->proposal)): ?>
        <br>
        <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
            <div class="content-box-header clearfix">
                <img src="<?php echo e(str_replace('profile/', 'profile/thumbnail/', $project->proposal->talent->company_logo)); ?>" alt="profile" class="job-profile-image">
                <div class="contentbox-header-title m-l-50">
                    <?php if($project->project_status !== 'closed'): ?>
                        <?php if(!empty($project->proposal)): ?>
                            <?php if(!empty($project->chat) && in_array($project->chat->chat_initiated,['employer','employer-accepted'])): ?>
                                <a href="javascript:void(0);" class="chat-link" data-request="chat-initiate" data-user="<?php echo e($project->id_project); ?>" data-url="<?php echo e(url(sprintf('%s/chat',EMPLOYER_ROLE_TYPE))); ?>">
                                   <img src="<?php echo e(asset('images/chat-icon.png')); ?>" width="25"/>
                                </a>
                            <?php else: ?>
                                <a href="javascript:void(0);" class="chat-link" data-user="<?php echo e($project->id_project); ?>" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->company_id),___encrypt($project->proposal->user_id),___encrypt($project->id_project)))); ?>">
                                   <img src="<?php echo e(asset('images/chat-icon.png')); ?>" width="25"/>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <h3>
                        <a href="<?php echo e(url("employer/find-talents/profile?talent_id=".___encrypt($project->proposal->talent->id_user))); ?>">
                            <?php echo e($project->proposal->talent->name); ?>

                        </a>
                    </h3>
                    <div class="rating-review">
                        <span class="rating-block">
                            <?php echo ___ratingstar($project->proposal->talent->rating); ?>

                        </span>
                        <a href="<?php echo e(url(sprintf('%s/find-talents/reviews?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->proposal->talent->id_user)))); ?>" class="reviews-block" style="color:#444444;">
                            <?php echo e($project->proposal->talent->total_review); ?> <?php echo e(trans('website.W0213')); ?>

                        </a>
                    </div>
                    <div class="profile-skill-tags m-t-5px">
                        <a href="<?php echo e(url("employer/find-talents/profile?talent_id=".___encrypt($project->proposal->talent->id_user))); ?>"><?php echo ___tags(array_column(array_column(json_decode(json_encode($project->proposal->talent->subindustries),true),'subindustries'),'name'),'<span class="small-tags">%s</span>','',false,NULL,2); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <br>
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <h2 class="form-heading small-heading"><?php echo e(trans('website.W0809')); ?></h2>
        <ul class="completion-list-group">
            <li class="completed"><?php echo e(trans('website.W0811')); ?></li>
            <li <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted'): ?> class="completed" <?php endif; ?>><?php echo e(trans('website.W0804')); ?></li>
            <li <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted' && ($project->project_status == 'initiated' || $project->project_status == 'completed' || $project->project_status == 'closed')): ?> class="completed" <?php endif; ?>><?php echo e(trans('website.W0805')); ?></li>
            <li <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted' && ($project->project_status == 'completed' || $project->project_status == 'closed')): ?> class="completed" <?php endif; ?>><?php echo e(trans('website.W0806')); ?></li>
            <li <?php if(!empty($project->proposal) && $project->proposal->status == 'accepted' && ($project->project_status == 'closed')): ?> class="completed" <?php endif; ?>><?php echo e(trans('website.W0807')); ?></li>
            <li <?php if(!empty($project->proposal) && 
                    $project->proposal->status == 'accepted' && 
                    !empty($project->transaction) && 
                    $project->transaction->transaction_user_type == 'talent' || 
                    ($project->dispute && $project->project_status =='closed') || (!empty($project->proposal) && $project->proposal->payment =='confirmed') 
                    ): ?> class="completed" <?php endif; ?>>
            <?php echo e(trans('website.W0808')); ?></li>
        </ul>
    </div>
</div>