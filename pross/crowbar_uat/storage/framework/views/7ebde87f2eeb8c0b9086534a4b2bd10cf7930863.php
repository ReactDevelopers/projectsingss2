<?php echo $__env->make('employer.job.includes.talent-profile-menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="login-inner-wrapper profile-info-details">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0030')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty(array_column($talent['industry'],'name'))): ?>
                    <?php echo ___tags(array_column($talent['industry'],'name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0206')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty($talent['skills'])): ?>
                    <?php echo ___tags(array_column($talent['skills'],'skill_name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0207')); ?> 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                <?php if(!empty(array_column($talent['subindustry'],'name'))): ?>
                    <?php echo ___tags(array_column($talent['subindustry'],'name'),'<span class="small-tags">%s</span>',''); ?>

                <?php else: ?>
                    <?php echo e(N_A); ?>

                <?php endif; ?>
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            <?php echo e(trans('website.W0663')); ?> 
        </h2>
        <div class="form-group clearfix">
            <?php if(!empty($talent['certificate_attachments'])): ?>
                <?php $__currentLoopData = $talent['certificate_attachments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <?php if ($__env->exists('talent.jobdetail.includes.attachment',['file' => $item])) echo $__env->make('talent.jobdetail.includes.attachment',['file' => $item], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
            <?php else: ?>
                <?php echo e(N_A); ?>

            <?php endif; ?>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">
                <?php echo e(trans('website.W0032')); ?>

            </h2>
            <div class="form-group clearfix">
                <div class="work-experience-box row">
                    <?php if ($__env->exists('talent.profile.includes.workexperience',['work_experience_list' => $talent['work_experiences']])) echo $__env->make('talent.profile.includes.workexperience',['work_experience_list' => $talent['work_experiences']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading"><?php echo e(trans('website.W0172')); ?></h2>
            <div class="form-group clearfix">
                <div class="education-box row">
                    <?php if ($__env->exists('talent.profile.includes.education', ['education_list' => $talent['educations']])) echo $__env->make('talent.profile.includes.education', ['education_list' => $talent['educations']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>     
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript">$('[data-request="delete"]').remove();$('.delete-attachment').remove();$('.edit-icon').remove();</script>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>


    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper .row:first').remove();
            $('#dataTableBuilder').next('.row').remove();
            
            setTimeout(function(){
                if($('.dataTables_empty').length > 0){
                    $('.completed-jobs-list').remove();
                }else{
                    $('.completed-jobs-list').show();
                }
            },2000);

            $(document).on('keyup click','[name="search"],#search-list',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.filter = $('[name="search"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
<?php $__env->stopPush(); ?>