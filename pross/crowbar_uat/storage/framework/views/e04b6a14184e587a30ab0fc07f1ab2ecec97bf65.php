<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('<?php echo e($project->employer->company_logo); ?>') no-repeat center center;background-size:100% 100%"></div>
                </div>
                <?php if(!empty($project->chat) && in_array($project->chat->chat_initiated,['employer','employer-accepted'])): ?>
                    <a href="javascript:void(0);" class="profile-chat-link"  data-request="chat-initiate" data-user="<?php echo e($project->chat->id_chat_request); ?>" data-url="<?php echo e(url(sprintf('%s/chat',TALENT_ROLE_TYPE))); ?>">
                       <img src="<?php echo e(asset('images/profile-chat.png')); ?>">
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-right no-padding">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            <?php echo ___ratingstar($project->employer->rating); ?>

                        </span>
                        <a href="<?php echo e(url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))); ?>" class="reviews-block underline"><?php echo e($project->employer->reviews_count); ?> <?php echo e(trans('website.W0213')); ?></a>
                    </div>
                </div>

                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0096')); ?></span>
                    <span class="item-description">
                        <?php if(!empty($project->employer->company_name)): ?>
                            <?php echo e($project->employer->company_name); ?>

                        <?php else: ?>
                            <?php echo e(N_A); ?>

                        <?php endif; ?>
                    </span>
                </div>

                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0201')); ?></span>
                    <span class="item-description">
                        <?php if(!empty($project->employer->country_name)): ?>
                            <?php echo e($project->employer->country_name); ?>

                        <?php else: ?>
                            <?php echo e(N_A); ?>

                        <?php endif; ?>
                    </span>
                </div>

                <?php if(0): ?>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0226')); ?></span>
                        <span class="item-description">
                            <?php if(!empty($project->employer->projects_count)): ?>
                                <?php echo e($project->employer->projects_count); ?>

                            <?php else: ?>
                                <?php echo e(N_A); ?>

                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0228')); ?> <?php echo e(trans('website.W0229')); ?></span>
                        <span class="item-description">
                            <?php if(!empty($project->employer->hirings_count)): ?>
                                <?php echo e($project->employer->hirings_count); ?>

                            <?php else: ?>
                                <?php echo e(N_A); ?>

                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0668')); ?></span>
                        <span class="item-description">
                            <?php if(!empty($project->employer->transaction)): ?>
                                <?php echo e(___format($project->employer->transaction->total_paid_by_employer,true,true,true)); ?>

                            <?php else: ?>
                                <?php echo e(N_A); ?>

                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>        
        </div>
        <div class="profile-completion-block profile-completion-list">
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p><a href="<?php echo e(url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))); ?>"><?php echo e($project->employer->name); ?></a></p>
                <small class="small-info"><?php echo e(trans('website.W0439')); ?> <?php echo e($project->employer->member_since); ?></small>
            </div>
            <?php if(0): ?>
            <div class="profile-expertise-column">
                <?php if($project->employer->is_saved === DEFAULT_YES_VALUE): ?>
                    <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="<?php echo e(url('talent/save/employer?employer_id='.$project->employer->id_user)); ?>"></a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="<?php echo e(url('talent/save/employer?employer_id='.$project->employer->id_user)); ?>"></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(!empty($project->proposal)): ?>
        <br>
        <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
            <h2 class="form-heading small-heading"><?php echo e(trans('website.W0809')); ?></h2>
            <ul class="completion-list-group">
                <li <?php if(!empty($project->proposal)): ?> class="completed" <?php endif; ?>><?php echo e(trans('website.W0803')); ?></li>
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
    <?php endif; ?>
    
    <?php if(!empty($project->otherjobs) && !empty($project->otherjobs->count())): ?>
        <div class="talent-profile-section completed-jobs-list">
            <h2 class="form-heading small-heading"><?php echo e(trans('website.W0676')); ?></h2>
            <div class="view-information" id="personal-infomation">
                <div class="no-table datatable-listing shift-up-5px">
                    <?php $__currentLoopData = $project->otherjobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <?php echo get_project_small_template($item,'talent'); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!empty($project->otherjobs) && !empty($project->similarjobs->count())): ?>
        <div class="talent-profile-section completed-jobs-list">
            <h2 class="form-heading small-heading"><?php echo e(trans('website.W0677')); ?></h2>
            <div class="view-information" id="personal-infomation">
                <div class="no-table datatable-listing shift-up-5px">
                    <?php $__currentLoopData = $project->similarjobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <?php echo get_project_small_template($item,'talent'); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>