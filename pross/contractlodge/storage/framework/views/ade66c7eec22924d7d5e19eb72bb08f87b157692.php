<?php $__env->startSection('content'); ?>
<hotels :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        <a href="<?php echo e(route('hotels.create')); ?>" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-hotel" dusk="add-hotel" data-offline="disabled">
                            <i class="fa fa-plus"></i> <?php echo e(__('Add Hotel')); ?>

                        </a>

                        <?php if(isset($showArchived)): ?>
                            <a href="<?php echo e(route('hotels.index')); ?>" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> <?php echo e(__('View Active Hotels')); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('hotels.archived')); ?>" class="btn btn-secondary btn-sm float-right mr-3" role="button" dusk="view-hotel-archive">
                                <i class="fa fa-archive mr-2"></i> <?php echo e(__('View Archived Hotels')); ?>

                            </a>
                        <?php endif; ?>

                        <?php if(isset($showArchived)): ?>
                            <?php echo e(__('Archived')); ?>

                        <?php else: ?>
                            <?php echo e(__('Active')); ?>

                        <?php endif; ?>

                        <?php echo e(__('Hotels')); ?>

                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <?php if(! $hotels->count()): ?>
                                <?php if(isset($showArchived)): ?>
                                    <div class="col-sm-12"><p>There are currently no archived hotels.</p></div>
                                <?php else: ?>
                                    <div class="col-sm-12"><p>No hotels found. <a href="<?php echo e(route('hotels.create')); ?>">Click here</a> to add a hotel.</p></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Hotel</th>
                                                <th>City</th>
                                                <th>State/Region</th>
                                                <th>Country</th>
                                                <th class="text-right">Num Races</th>
                                                <th class="text-right">Balance</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <?php if(isset($showArchived)): ?>
                                                            <?php echo e($hotel->name); ?>

                                                        <?php else: ?>
                                                            <a href="<?php echo e(route('hotels.show', ['hotel' => $hotel->id])); ?>"><?php echo e($hotel->name); ?></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($hotel->city); ?></td>
                                                    <td><?php echo e($hotel->region); ?></td>
                                                    <td><?php echo e($hotel->country->name); ?></td>
                                                    <td class="text-right"><?php echo e($hotel->races->count()); ?></td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if(isset($showArchived)): ?>
                                                            <form action="<?php echo e(route('hotels.unarchive', ['hotel_id' => $hotel->id])); ?>" method="POST">
                                                                <?php echo e(method_field('PUT')); ?>

                                                                <?php echo e(csrf_field()); ?>

                                                                <button onclick="return confirm('Are you sure you want to unarchive this hotel?');"
                                                                    class="btn btn-success btn-sm" dusk="hotel-unarchive">
                                                                    <i class="fa fa-undo mr-2"></i> Unarchive
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form action="<?php echo e(route('hotels.destroy', ['hotel' => $hotel->id])); ?>" method="POST">
                                                                <?php echo e(method_field('DELETE')); ?>

                                                                <?php echo e(csrf_field()); ?>

                                                                <button onclick="return confirm('Are you sure you want to archive this hotel?');"
                                                                    class="btn btn-secondary btn-sm" dusk="hotel-archive">
                                                                    <i class="fa fa-archive mr-2"></i> Archive
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</hotels>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>