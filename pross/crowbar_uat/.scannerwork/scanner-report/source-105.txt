<div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
    <div class="profile-left">
        <div class="user-profile-image">
            <div class="user-display-details">
                <a href="<?php echo e(url('employer/project/proposals/talent?proposal_id='.___encrypt($proposal['proposal_id']).'&project_id='.___encrypt($proposal['project_id']))); ?>"" class="user-display-image" style="background: url('<?php echo e($proposal['company_logo']); ?>') no-repeat center center;background-size:100% 100%; display: block;"></a>
            </div>
            <?php if(!empty($proposal->project_status != 'closed')): ?>
                <?php if(!empty($proposal->chat) && in_array($proposal->chat->chat_initiated,['employer','employer-accepted'])): ?>
                    <a href="javascript:void(0);" class="profile-chat-link" data-request="chat-initiate" data-user="<?php echo e($proposal->chat->id_chat_request); ?>" data-url="<?php echo e(url(sprintf('%s/chat',EMPLOYER_ROLE_TYPE))); ?>">
                       <img src="<?php echo e(asset('images/profile-chat.png')); ?>">
                    </a>
                <?php elseif($proposal->proposal_status != 'rejected'): ?>
                    <a href="javascript:void(0);" class="profile-chat-link" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal['company_id']),___encrypt($proposal['talent_id']),___encrypt($proposal['project_id'])))); ?>">
                       <img src="<?php echo e(asset('images/profile-chat.png')); ?>">
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="profile-right no-padding">
        <div class="user-profile-details">
            <div class="item-list">
                <div class="rating-review">
                    <span class="rating-block">
                        <?php echo ___ratingstar($proposal['rating']); ?>

                    </span>
                    <a href="<?php echo e(url(sprintf('%s/profile/reviews',TALENT_ROLE_TYPE))); ?>" class="reviews-block underline"><?php echo e($proposal['total_review']); ?> <?php echo e(trans('website.W0213')); ?></a>
                </div>
            </div>
            <div class="item-list">
                <span class="item-heading"><?php echo e(trans('website.W0660')); ?></span>
                <span class="item-description">
                    <?php if($proposal['employment'] != 'fixed'): ?>
                        <?php echo sprintf('%s/%s',___format($proposal['quoted_price'],true,true),substr($proposal['employment'],0,-2)).'<br>'; ?>

                    <?php else: ?>
                        <span class="label-green color-grey"><?php echo e($proposal['employment']); ?></span>
                    <?php endif; ?>
                </span>
            </div>
            <?php if($proposal['country_name']): ?>
                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0201')); ?></span>
                    <span class="item-description"><?php echo e($proposal['country_name']); ?></span>
                </div>
            <?php endif; ?>
            <div class="item-list">
                <span class="label-green color-grey"><?php echo e($proposal->current_proposals_status); ?></span>
            </div>            
        </div>        
    </div>
    <div class="view-profile-name">
        <div class="user-name-info">
            <p><a href="<?php echo e(url('employer/project/proposals/talent?proposal_id='.___encrypt($proposal['proposal_id']).'&project_id='.___encrypt($proposal['project_id']))); ?>"><?php echo e($proposal['name']); ?></p>
            <small class="small-info"><?php echo e(trans('website.W0439')); ?> <?php echo e($proposal['member_since']); ?></small>
        </div>
        <div class="profile-expertise-column">
            <?php if(!empty($proposal['last_viewed'])): ?>
                <span class="last-viewed-icon active"></span>
            <?php endif; ?>

            <?php if($proposal['is_saved'] == DEFAULT_YES_VALUE): ?>
                <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="<?php echo e(url(sprintf('%s/save?talent_id=%s',EMPLOYER_ROLE_TYPE,$proposal['id_user']))); ?>"></a>
            <?php else: ?>
                <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="<?php echo e(url(sprintf('%s/save?talent_id=%s',EMPLOYER_ROLE_TYPE,$proposal['id_user']))); ?>"></a>
            <?php endif; ?>

            <?php if(!empty($proposal['expertise'])): ?>
                <span class="label-green color-grey"><?php echo e(expertise_levels($proposal['expertise'])); ?></span>
            <?php endif; ?>
            <?php if(!empty($proposal['experience'])): ?>
                <span class="experience"><?php echo e(sprintf("%s %s",$proposal['experience'],trans('website.W0669'))); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>