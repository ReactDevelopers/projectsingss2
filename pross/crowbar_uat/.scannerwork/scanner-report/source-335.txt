<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-skill" action="<?php echo e(url(sprintf("%s/%s",ADMIN_FOLDER,'skill/add'))); ?>" method="post">
                        <div class="panel-body">                       
                            <?php if(0): ?>
                                <div class="form-group">
                                    <label for="question">INDUSTRY</label>
                                    <div>
                                        <select class="form-control" name="industry_id">
                                            <?php echo ___dropdown_options($subindustries_name,trans("admin.A0018"),!empty($skill) ? $skill->industry_id : ''); ?>

                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="question">Skill</label>
                                <input type="text" class="form-control" name="skill_name" maxlength="<?php echo e(TAG_LENGTH); ?>" value="<?php echo e(!empty($skill) ? $skill->skill_name : ''); ?>" placeholder="<?php echo e(trans('admin.A0051')); ?>" style="width:100%;"/>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" name="id_skill" value="<?php echo e(!empty($skill) ? ___encrypt($skill->id_skill) : ''); ?>">
                            <input type="hidden" name="action" value="submit">
                            <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="form-add-skill"]' class="btn btn-default">Save</button>
                        </div>                                            
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>