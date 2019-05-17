<?php if(isset($rooming_list_guests) && ! $rooming_list_guests->isEmpty()): ?>
    <hr class="my-5">

    <div class="form-row mb-1">
        <div class="col-sm-12">
            <h5 class="mb-3"><?php echo e(__('Rooming List')); ?></h5>
        </div>
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-5 pt-1"><label for="list_sent_on">List Sent On:</label></div>
                <div class="col-sm-7 mb-2"><?php echo e($meta->friendly_rooming_list_sent); ?></div>
            </div>
            <div class="row">
                <div class="col-sm-5 pt-1"><label for="list_confirmed_on">List Confirmed On:</label></div>
                <div class="col-sm-7 mb-2"><?php echo e($meta->friendly_rooming_list_confirmed); ?></div>
            </div>
        </div>

        <?php echo $__env->make('partials.common.rooming-list-room-type-breakdown', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <div class="col-sm-4 text-right">
            <div class="row">
                <div class="col-md-12">
                    <a href="<?php echo e(route('races.hotels.reservations', [
                        'race' => $race->id,
                        'hotel' => $hotel->id,
                        ])); ?>" class="btn btn-primary btn-sm ml-3 float-right">
                        <i class="fa fa-edit mr-2"></i> <?php echo e(__('Edit Rooming List')); ?>

                    </a>
                    <a href="<?php echo e(route('races.hotels.reservations.export', [
                        'race' => $race->id,
                        'hotel' => $hotel->id
                        ])); ?>"
                        class="btn btn-primary btn-sm ml-5 float-right">
                        <i class="fa fa-download mr-2"></i> <?php echo e(__('Export')); ?>

                    </a>
                </div>
                <div class="col-md-12 mt-4">
                    <button v-on:click="resetSearchTable('rooming_list')" 
                        class="btn btn-primary btn-sm ml-2 float-right">
                        Reset
                    </button>
                    <input type="text" name="q" id="q" value="" 
                        v-on:keyup="searchTable('rooming_list')" 
                        placeholder="Search for guest name" 
                        class="form-control col-sm-12 col-md-8 col-lg-4 float-right">
                </div>
            </div>
        </div>
        <div class="col-sm-12 mb-3">
            <label for="">Notes to Hotel: </label>
            <?php echo e($meta->rooming_list_notes); ?>

        </div>

        <?php echo $__env->make('partials.common.rooming-list-legend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>

    
    <?php echo $__env->make('partials.common.rooming-listing', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
