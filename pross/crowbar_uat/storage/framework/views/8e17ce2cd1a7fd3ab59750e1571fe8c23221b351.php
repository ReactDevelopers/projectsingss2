<div class="modal-dialog" role="document">
    <div class="modal-content">
        <h3 class="form-heading m-b-10px no-padding"><?php echo e(trans('website.W0650')); ?></h3>
        <div class="hireTalent">
            <ul>
                <li>
                    <a href="<?php echo e(url(sprintf('%s/hire/talent/one?talent_id=%s',EMPLOYER_ROLE_TYPE,$talent_id))); ?>"></span>
                        <span class="hireIcon creatJobIcon"></span>
                        <span class="hiretitle"><?php echo e(trans('website.W0812')); ?></span>
                    </a>
                </li>
                <li>
                    
                    <a data-target="#hire-me" href="javascript:void(0);" data-request="ajax-modal" data-request="inline-ajax" data-url="<?php echo e(url(sprintf('%s/prepare_message?talent_id=%s',EMPLOYER_ROLE_TYPE,$talent_id))); ?>" data-user="<?php echo e($talent_id); ?>">
                        <span class="hireIcon sendmessageIcon"></span>
                        <span class="hiretitle"><?php echo e(trans('website.W0816')); ?></span>
                    </a>
                </li>
                <li>
                    <a data-target="#hire-me" data-request="ajax-modal" data-url="<?php echo e(url(sprintf('%s/hire-talent?talent_id=%s&page=%s',EMPLOYER_ROLE_TYPE,$talent_id,'existingjob'))); ?>" href="javascript:void(0);">
                        <span class="hireIcon existingJobIcon"></span>
                        <span class="hiretitle"><?php echo e(trans('website.W0814')); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>