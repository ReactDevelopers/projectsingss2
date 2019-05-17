<?php if(!empty($education_list)): ?>
    <?php $__currentLoopData = $education_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <div class="col-md-6 col-sm-12 col-xs-12" id="box-<?php echo e($item['id_education']); ?>"> 
            <div class="educationEditSec"> 
                <span class="company-profile-tag">
                    <?php if(!empty($item['logo'])): ?>
                        <img src="<?php echo e($item['logo']); ?>">
                    <?php else: ?>
                        <img src="<?php echo e(asset('/images/ge-icon.png')); ?>">
                    <?php endif; ?>
                </span>
                <div class="addIcons"> 
                    <a href="javascript:void(0);" class="edit-icon" title="Edit" data-url="<?php echo e(sprintf(url('ajax/%s?id_education=%s'), EDIT_TALENT_EDUCATION, $item['id_education'] )); ?>" data-request="edit" data-education_id="<?php echo e($item['id_education']); ?>" data-edit-id="education_id">
                        <img src="<?php echo e(asset('/images/edit-icon.png')); ?>">
                    </a> 
                    <a href="javascript:void(0);" title="Delete" data-url="<?php echo e(sprintf(url('ajax/%s?id_education=%s'), DELETE_TALENT_EDUCATION, $item['id_education'] )); ?>" data-request="delete" data-education_id="<?php echo e($item['id_education']); ?>" data-edit-id="education_id" data-toremove="box" data-ask="Do you realy want to delete your education?">
                        <img src="<?php echo e(asset('/images/delete-icon.png')); ?>">
                    </a>
                </div> 
                <ul>
                    <li>
                        <span><?php echo e(trans('website.W0082')); ?></span>
                        <span><?php echo e($item['college']); ?></span>
                    </li>
                    <li>
                        <span><?php echo e(trans('website.W0086')); ?></span>
                        <span><?php echo e($item['passing_year']); ?></span>
                    </li>
                    <li>
                        <span><?php echo e(trans('website.W0084')); ?></span>
                        <span><?php echo e(___cache('degree_name')[$item['degree']]); ?> (<?php echo e($item['area_of_study']); ?>)</span>
                    </li>
                </ul>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
<?php else: ?>
    <div class="col-md-12"><?php echo e(N_A); ?></div>
<?php endif; ?>