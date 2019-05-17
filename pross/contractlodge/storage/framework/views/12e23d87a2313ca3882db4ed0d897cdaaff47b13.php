<?php $__env->startSection('content'); ?>
<races :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        <a href="<?php echo e(route('races.create')); ?>" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-race" data-offline="disabled">
                            <i class="fa fa-plus"></i> <?php echo e(__('Add Race')); ?>

                        </a>

                        <?php if(isset($showArchived)): ?>
                            <a href="<?php echo e(route('races.index')); ?>" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> <?php echo e(__('View Active Races')); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('races.archived')); ?>" class="btn btn-secondary btn-sm float-right mr-3" role="button" dusk="view-race-archive">
                                <i class="fa fa-archive mr-2"></i> <?php echo e(__('View Archived Races')); ?>

                            </a>
                        <?php endif; ?>

                        <?php if(isset($showArchived)): ?>
                            <?php echo e(__('Archived')); ?>

                        <?php else: ?>
                            <?php echo e(__('Active')); ?>

                        <?php endif; ?>

                        <?php echo e(__('Races')); ?>

                    </div>

                    <div class="card-body">
                        <?php if(! $races->count()): ?>
                            <?php if(isset($showArchived)): ?>
                                <div class="col-sm-12"><p>There are currently no archived races.</p></div>
                            <?php else: ?>
                                <div class="col-sm-12"><p>No races found. <a href="<?php echo e(route('races.create')); ?>">Click here</a> to add a race.</p></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <table class="table table-sm table-striped override-table">
                                <thead>
                                    <tr>
                                        <th>Race</th>
                                        <th>Start On</th>
                                        <th>End On</th>
                                        <th>Default Currency</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $races; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $race): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php if(isset($showArchived)): ?>
                                                    <?php echo e($race->full_name); ?>

                                                <?php else: ?>
                                                    <a href="<?php echo e(route('races.show', ['race' => $race->id])); ?>" dusk="race-<?php echo e($race->id); ?>"><?php echo e($race->full_name); ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($race->friendly_start_on); ?></td>
                                            <td><?php echo e($race->friendly_end_on); ?></td>
                                            <td><?php echo e($race->currency->name); ?></td>
                                            <td class="text-center">
                                                <?php if(isset($showArchived)): ?>
                                                    <form action="<?php echo e(route('races.unarchive', ['race_id' => $race->id])); ?>" method="POST">
                                                        <?php echo e(method_field('PUT')); ?>

                                                        <?php echo e(csrf_field()); ?>

                                                        <button onclick="return confirm('Are you sure you want to unarchive this race?');"
                                                            class="btn btn-success btn-sm" dusk="race-unarchive">
                                                            <i class="fa fa-undo mr-2"></i> Unarchive
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?php echo e(route('races.destroy', ['race' => $race->id])); ?>" method="POST">
                                                        <?php echo e(method_field('DELETE')); ?>

                                                        <?php echo e(csrf_field()); ?>

                                                        <button onclick="return confirm('Are you sure you want to archive this race?');"
                                                            class="btn btn-secondary btn-sm" dusk="race-archive">
                                                            <i class="fa fa-archive mr-2"></i> Archive
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</races>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>