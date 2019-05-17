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
</div>