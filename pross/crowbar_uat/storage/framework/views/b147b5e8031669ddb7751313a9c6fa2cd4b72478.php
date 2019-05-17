<?php if($if_added_member != 'rejected'): ?>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="member-info">
            <div class="member_profile <?php echo e(($if_added_member != 'accepted')? '' : 'red'); ?>">
                <img src="<?php echo e($picture); ?>">
            </div>
            <div class="member_detail">
                <h6><a href="<?php echo e(url(sprintf("%s/view/%s",TALENT_ROLE_TYPE,___encrypt($id_user)))); ?>"><?php echo e($name); ?></a></h6>
                <?php if($industry_name !='' && $country!=''): ?>
                    <p><?php echo e($industry_name); ?> <?php echo e('('.$country.')'); ?></p>
                <?php endif; ?>
                <span><?php echo e(trans('website.W0439')); ?> <?php echo e(date('jS F Y',strtotime($created))); ?></span>
                <br>

                <?php if($if_added_member == '' && $if_added_member2 == ''): ?>
                    <a class="hire-me" data-target="#add-member" data-request="ajax-modal" data-url="<?php echo e(url(sprintf('%s/add-to-circle?talent_id=%s&user_name=%s',TALENT_ROLE_TYPE,$id_user,$name))); ?>" href="javascript:void(0);"><img src="<?php echo e(asset('images/add.png')); ?>"><?php echo e(trans('website.W0899')); ?></a>
                <?php elseif($if_added_member == 'pending'): ?>
                    <a  href="javascript:void(0);">Request pending</a>
                <?php elseif($if_added_member2 == 'pending'): ?>
                    <a  href="javascript:void(0);">Request pending</a>
                <?php else: ?>
                    <a href="javascript:void(0);"><img src="<?php echo e(asset('images/member-added.png')); ?>"><?php echo e(trans('website.W0900')); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>