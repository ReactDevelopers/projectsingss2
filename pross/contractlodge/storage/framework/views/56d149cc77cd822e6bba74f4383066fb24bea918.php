<?php $__env->startSection('content'); ?>
<hotels-show :user="user" :race-hotel-id="<?php echo e(isset($meta->id) ? $meta->id : 'null'); ?>" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                
                <?php echo $__env->make('partials.common.flash-url-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <?php if(isset($race->id)): ?>
                                    <a href="<?php echo e(route('races.index')); ?>"><?php echo e(__('Races')); ?></a> /
                                    <a href="<?php echo e(route('races.show', ['race' => $race->id])); ?>"><?php echo e($race->full_name); ?></a> /
                                <?php else: ?>
                                    <a href="<?php echo e(route('hotels.index')); ?>"><?php echo e(__('Hotels')); ?></a> /
                                <?php endif; ?>
                                <?php echo e($hotel->name); ?>

                            </div>
                            <?php if(isset($race->id)): ?>
                                <div class="col-sm-6 text-right">
                                    <?php if(isset($rooming_list_guests) && ! $rooming_list_guests->isEmpty()): ?>
                                        <a href="<?php echo e(route('races.hotels.reconcile', ['race' => $race->id, 'hotel' => $hotel->id])); ?>"
                                            class="btn btn-primary btn-sm ml-3 float-right">
                                            <i class="fa fa-check-circle mr-2"></i> Reconcile
                                        </a>
                                    <?php endif; ?>
                                    <form class="rtl float-right ml-3" method="POST"
                                        action="<?php echo e(route('races.hotels.destroy', ['race' => $race->id, 'hotel' => $hotel->id])); ?>">
                                        <?php echo e(method_field('DELETE')); ?>

                                        <?php echo e(csrf_field()); ?>

                                        <button onclick="return confirm('This will be a permanent action. Data will not be recoverable. Are you sure?');"
                                            class="btn btn-secondary btn-sm" dusk="race-archive">
                                            <i class="fa fa-close mr-2"></i> Remove Hotel from Race
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-row mb-4">
                            <div class="col-sm-6">
                                <?php if(isset($race->id)): ?>
                                    <h5><a href="<?php echo e(route('hotels.show', ['hotel' => $hotel->id])); ?>"><?php echo e($hotel->name); ?></a></h5>
                                <?php else: ?>
                                    <h5><?php echo e($hotel->name); ?></h5>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?php if(! isset($race->id)): ?>
                                    <a href="<?php echo e(route('hotels.edit', ['hotel' => $hotel->id])); ?>"
                                        class="btn btn-primary btn-sm" dusk="hotel-edit" data-offline="disabled">
                                        <i class="fa fa-edit mr-2"></i> Edit Hotel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="col-sm-3">
                                <?php echo $__env->make('partials.hotels.header-block', ['contact_hotel' => $contact], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                            <div class="col-sm-9">
                                <?php if(isset($hotel->notes)): ?>
                                    <label><strong>Notes on Hotel</strong></label> <br>
                                    <?php echo e($hotel->notes); ?>

                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if(isset($race->id)): ?>
                            <?php echo $__env->make('partials.hotels.show.with-race.room-types-and-rates', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('partials.hotels.show.with-race.client-breakdown', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('partials.hotels.show.with-race.hotel-payments', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            
                            <?php echo $__env->make('partials.hotels.show.with-race.rooming-list', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php else: ?>
                            <?php echo $__env->make('partials.hotels.show.without-race', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</hotels-show>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>