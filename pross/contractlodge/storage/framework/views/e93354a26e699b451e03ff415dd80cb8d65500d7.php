<?php $__env->startSection('content'); ?>
<races-create :user="user" :start-date="'<?php echo e(format_input_date_to_system(old('start_on', @$race->start_on))); ?>'"
    :end-date="'<?php echo e(format_input_date_to_system(old('end_on', @$race->end_on))); ?>'" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">

                <form enctype="multipart/form-data" method="POST" role="form" action="<?php echo e(route('races.store')); ?>">
                    <?php echo e(csrf_field()); ?>


                    <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="<?php echo e(route('races.index')); ?>"><?php echo e(__('Races')); ?></a> /
                            <?php echo e(__('Create New Race')); ?>

                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5><?php echo e(__('Create New Race')); ?></h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-12">
                                    <?php echo $__env->make('partials.races.form', ['set_default_end_date' => true], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <a href="<?php echo e(route('races.index')); ?>" class="btn btn-default ml-2" dusk="race-cancel">
                                        <?php echo e(__('Cancel')); ?>

                                    </a>
                                    <button type="submit" class="btn btn-primary ml-2" dusk="race-submit">
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
</races-create>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>