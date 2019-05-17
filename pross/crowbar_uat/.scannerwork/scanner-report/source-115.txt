<?php if(!empty($work_experience_list)): ?>
    <?php $__currentLoopData = $work_experience_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <div class="col-md-6 col-sm-12 col-xs-12 experience-white-box" id="experience-<?php echo e($item['id_experience']); ?>">
            <div class="educationEditSec">
                <span class="company-profile-tag">
                    <?php if(!empty($item['logo'])): ?>
                        <img src="<?php echo e($item['logo']); ?>">
                    <?php else: ?>
                        <img src="<?php echo e(asset('/images/ge-icon.png')); ?>">
                    <?php endif; ?>
                </span>
				
                <ul>
                    <li>
                        <span><?php echo e(trans('website.W0094')); ?></span>
                        <span><?php echo e($item['jobtitle']); ?></span>
                    </li>
                    <li>
                        <span><?php echo e(trans('website.W0096')); ?></span>
                        <span><?php echo e($item['company_name']); ?></span>
                    </li>
                    <li>
                        <span><?php echo e(trans('website.W0368')); ?></span>
                        <span>
                            <?php echo e(date('F',strtotime(sprintf("%s-%s-%s",'2017',$item['joining_month'],'01')))); ?>, <?php echo e($item['joining_year']); ?>

                            <?php if($item['is_currently_working'] == 'yes'): ?>
                                <?php echo e(trans('website.W0670')); ?><?php echo e(trans('website.W0230')); ?>

                            <?php else: ?>
                                <?php echo e(trans('website.W0670')); ?><?php echo e(date('F',strtotime(sprintf("%s-%s-%s",'2017',$item['relieving_month'],'01')))); ?>, <?php echo e($item['relieving_year']); ?>

                            <?php endif; ?>
                            (<?php echo e(employment_types('talent_curriculum_vitae',$item['job_type'])); ?>)
                        </span>
                    </li>
                    <li>
                        <span><?php echo e(trans('website.W0201')); ?></span>
                        <span>
                            <?php echo e($item['country_name']); ?><?php if($item['state_name'] != N_A): ?>, <?php echo e($item['state_name']); ?> <?php endif; ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<?php else: ?>
    <div class="col-md-12"><?php echo e(N_A); ?></div>
<?php endif; ?>