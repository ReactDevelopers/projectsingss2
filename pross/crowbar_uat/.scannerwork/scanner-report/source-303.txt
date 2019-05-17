<div class="mailbox-read-message">
    <div class="form-group">
        <b>Project Type</b><br>
        <span><?php echo e(employment_types('post_job',$project_detail['employment'])); ?></span> 
    </div>
    <div class="form-group">
        <b>Profession</b><br>
        <?php if(!empty(array_column(array_column($project_detail['industries'], 'industries'),'name'))): ?>
            <?php echo ___tags(array_column(array_column($project_detail['industries'], 'industries'),'name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </div>
    <b>Industry</b>
    <p>
        <?php if(!empty(array_column(array_column($project_detail['skills'], 'skills'),'skill_name'))): ?>
            <?php echo ___tags(array_column(array_column($project_detail['skills'], 'skills'),'skill_name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </p>
    <div class="form-group">
        <b>Specialisation</b><br>
        <?php if(!empty(array_column(array_column($project_detail['subindustries'], 'subindustries'),'name'))): ?>
            <?php echo ___tags(array_column(array_column($project_detail['subindustries'], 'subindustries'),'name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </div>
    <div class="form-group">
        <b>Expertise Level</b><br> 
        <span><?php echo e(!empty($project_detail['expertise']) ? expertise_levels($project_detail['expertise']) : N_A); ?></span>
    </div>
    <div class="form-group">
        <b>Timeline</b> <br>
        <span>
            <?php echo e(___date_difference($project_detail['startdate'],$project_detail['enddate'])); ?>

        </span>
    </div>
    <br>
    <b>Description</b>
    <p><?php echo nl2br($project_detail['description']); ?></p>                                    
    <br>
</div>

