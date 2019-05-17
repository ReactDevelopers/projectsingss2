<div>
    <ul class="user-profile-links">
        <li class="resp-tab-item">
            <a href="<?php echo e(url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0678')); ?>

            </a>
        </li>
        <li class="active">
            <a href="<?php echo e(url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0679')); ?>

            </a>
        </li>
        <li>
            <a href="<?php echo e(url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))); ?>">
                <?php echo e(trans('website.W0680')); ?>

            </a>
        </li>
        <?php if(!empty($project->reviews_count)): ?>
            <li class="resp-tab-item">
                <a href="<?php echo e(url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))); ?>">
                    <?php echo e(trans('website.W0721')); ?>

                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="shift-up-5px">
            <div>
                <div class="no-table datatable-listing">
                    <?php echo $html->table(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper thead').remove();
            $('#dataTableBuilder_wrapper .row:first').remove();
            $('#dataTableBuilder').next('.row').remove();
        });
    </script>
<?php $__env->stopPush(); ?>