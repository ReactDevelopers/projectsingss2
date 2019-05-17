<?php $__env->startSection('content'); ?>
<races-edit :user="user" :start-date="'<?php echo e(format_input_date_to_system(old('start_on', @$race->start_on))); ?>'"
    :end-date="'<?php echo e(format_input_date_to_system(old('end_on', @$race->end_on))); ?>'" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form enctype="multipart/form-data" method="POST" role="form"
                    action="<?php echo e(route('races.update', ['race' => $race->id])); ?>">

                    <?php echo method_field('PUT'); ?>
                    <?php echo e(csrf_field()); ?>



                    <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="<?php echo e(route('races.index')); ?>" title="<?php echo e(__('Races')); ?>"><?php echo e(__('Races')); ?></a> /
                            <?php echo e(__('Edit Race')); ?>

                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5><?php echo e(__('Edit Race')); ?></h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-12">
                                    <?php echo $__env->make('partials.races.form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    <a href="<?php echo e(route('races.show', ['race' => $race->id])); ?>"
                                        class="btn btn-default ml-2" dusk="race-edit-cancel">
                                        <?php echo e(__('Cancel')); ?>

                                    </a>
                                    <button type="submit" class="btn btn-primary ml-2" dusk="race-edit-submit">
                                        <i class="fa fa-save mr-2"></i>
                                        <?php echo e(__('Save')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</races-edit>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>