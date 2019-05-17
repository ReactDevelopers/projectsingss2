<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('<?php echo e($user['picture']); ?>') no-repeat center center;background-size:100% 100%"></div>
                </div>
            </div>
        </div>
        <div class="profile-right no-padding">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            <?php echo ___ratingstar($user['rating']); ?>

                        </span>
                        <a href="<?php echo e(url(sprintf('%s/profile/reviews',TALENT_ROLE_TYPE))); ?>" class="reviews-block underline"><?php echo e($user['review']); ?> <?php echo e(trans('website.W0213')); ?></a>
                    </div>
                </div>
                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0696')); ?></span>
                    <span class="item-description"><?php echo e($user['jobdetails']['completed_count']); ?></span>
                </div>
                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0694')); ?></span>
                    <span class="item-description"><?php echo e($user['jobdetails']['awarded_count']); ?></span>
                </div>
                <div class="item-list">
                    <span class="item-heading"><?php echo e(trans('website.W0695')); ?></span>
                    <span class="item-description"><?php echo e($user['jobdetails']['applied_count']); ?></span>
                </div>
            </div>        
        </div>
        <div class="profile-completion-block profile-completion-list">
            <div class="clearfix"></div>
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p><?php echo e(sprintf("%s %s",$user['first_name'],$user['last_name'])); ?></p>
            </div>
            <div class="profile-expertise-column">
                <?php if(!empty($user['expertise'])): ?>
                    <span class="label-green color-grey"><?php echo e(expertise_levels($user['expertise'])); ?></span>
                <?php endif; ?>
                <?php if(!empty($user['expertise'])): ?>
                    <span class="experience"><?php echo e(!empty($user['experience']) ? sprintf("%s %s",$user['experience'],trans('website.W0669')) : ''); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>