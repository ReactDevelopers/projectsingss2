<?php $__env->startSection('content'); ?>
<races-show :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="<?php echo e(route('races.index')); ?>">
                                    <?php echo e(__('Races')); ?>

                                </a> / <?php echo e($race->full_name); ?>

                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="/races/<?php echo e($race->id); ?>/invoices/extras/create"
                                    class='btn btn-primary btn-sm ml-3'
                                    data-offline="disabled" dusk="add-invoice">
                                    <i class="fa fa-plus mr-2"></i> <?php echo e(__('Add Extras Invoice')); ?>

                                </a>
                                <a href="/races/<?php echo e($race->id); ?>/edit"
                                    class="btn btn-primary btn-sm ml-3"
                                    dusk="race-edit"
                                    data-offline="disabled">
                                    <i class="fa fa-edit mr-2"></i> <?php echo e(__('Edit Race')); ?>

                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <div class="col-sm-6">
                                <h5><?php echo e($race->full_name); ?></h5>
                            </div>
                        </div>

                        <div class="form-row mt-3 mb-4">
                            <div class="col-sm-1">
                                <label><strong>Year</strong></label> <br>
                                <?php echo e($race->year); ?>

                            </div>
                            <div class="col-sm-3">
                                <label><strong>Race</strong></label> <br>
                                <?php echo e($race->name); ?>

                            </div>
                            <div class="col-sm-2">
                                <label><strong>Start Date</strong></label> <br>
                                <?php echo e($race->friendly_start_on); ?>

                            </div>
                            <div class="col-sm-2">
                                <label><strong>End Date</strong></label> <br>
                                <?php echo e($race->friendly_end_on); ?>

                            </div>
                            <div class="col-sm-2">
                                <label><strong>Default Currency</strong></label> <br>
                                <?php echo e($race->currency->name); ?>

                            </div>
                            <div class="col-sm-2">
                                <p class="text-right mr-3">
                                    Total Rooms Booked:
                                    <strong class="ml-3"><?php echo e($inventory_stats['sum_row_total_min_stays_contracted']); ?></strong>
                                </p>
                                <p class="text-right mr-3">
                                    Total Rooms Sold:
                                    <strong class="ml-3"><?php echo e($inventory_stats['sum_row_total_min_stays_sold']); ?></strong>
                                </p>
                                <p class="text-right mr-3">
                                    Total Rooms Available:
                                    <strong class="ml-3"><?php echo e($inventory_stats['sum_available_total']); ?></strong>
                                </p>
                            </div>
                        </div>
                        <hr class="my-5">

                        <div class="form-row mt-4 mb-4">
                            <div class="col-sm-6">
                                <h5><?php echo e(__('Hotel Inventory')); ?></h5>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="<?php echo e(route('races.hotels.search', ['race' => $race->id])); ?>"
                                    class="btn btn-primary btn-sm"
                                    dusk="add-race-hotel"
                                    data-offline="disabled">
                                    <i class="fa fa-plus mr-2"></i> Add Hotel
                                </a>
                            </div>
                        </div>
                        <div class="form-row mt-3 mb-4">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped override-table">
                                    <?php $room_array = []; ?>
                                    <?php $__currentLoopData = $race->hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            if (isset($hotel->meta->id)
                                                && isset($inventory_stats['inventories'][$hotel->meta->id])
                                                && ! empty($inventory_stats['inventories'][$hotel->meta->id])
                                            ) {
                                                $inventories = $inventory_stats['inventories'][$hotel->meta->id];
                                            } else {
                                                $inventories = [];
                                            }
                                        ?>

                                        <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $room_array[$inventory->id] = $inventory->id;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php $__currentLoopData = $race->hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            if (isset($hotel->meta->id)
                                                && isset($inventory_stats['inventories'][$hotel->meta->id])
                                                && ! empty($inventory_stats['inventories'][$hotel->meta->id])
                                            ) {
                                                $inventories = $inventory_stats['inventories'][$hotel->meta->id];
                                            } else {
                                                $inventories = [];
                                            }
                                        ?>

                                        <tr class="header-row">
                                            <th>Name</th>
                                            <th>Status</th>
                                            <?php
                                                $room_count = 0;
                                            ?>
                                            <?php if(isset($inventories) && isset($room_array) && count($inventories) > 0 ): ?>
                                                <?php $__currentLoopData = $room_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($inventory->id == $room): ?>
                                                            <th class="text-center"><?php echo e($inventory->room_name); ?></th>
                                                            <?php
                                                                $room_count++;
                                                            ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            <?php $remaining_count = $inventory_stats['max_room_types'] - $room_count; ?>
                                            <?php if($remaining_count > 0): ?>
                                                <?php for($i = 1; $i<= $remaining_count; $i++): ?>
                                                    <th></th>
                                                <?php endfor; ?>
                                            <?php endif; ?>

                                            <th class="text-center">Total</th>
                                        </tr>

                                        <tr class="bg-white">
                                            <td>
                                                <a href="<?php echo e(route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id])); ?>">
                                                    <strong><?php echo e($hotel->name); ?></strong>
                                                </a>
                                            </td>
                                            <td>Rooms Booked</td>
                                            <?php
                                                $row_total_min_stays_contracted = 0;
                                            ?>
                                            <?php if(isset($inventories) && isset($room_array) && count($inventories) > 0 ): ?>
                                                <?php $__currentLoopData = $room_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($inventory->id == $room): ?>
                                                            <td class="text-center">
                                                                <?php echo e($inventory->min_stays_contracted); ?>

                                                                <?php
                                                                    $row_total_min_stays_contracted += $inventory->min_stays_contracted;
                                                                    continue 2;
                                                                ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($remaining_count > 0): ?>
                                                    <?php for($i = 1; $i<= $remaining_count; $i++): ?>
                                                        <td class="text-center"> </td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if(count($inventories) == 0): ?>
                                                <?php $__currentLoopData = $race->room_type_inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center"> </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <td class="text-center"><strong><?php echo e($row_total_min_stays_contracted); ?></strong></td>
                                        </tr>
                                        <tr class="bg-gray-100">
                                            <td></td>
                                            <td>Rooms Sold</td>
                                            <?php
                                                $row_total_min_stays_sold = 0;
                                            ?>

                                            <?php if(isset($inventories) && isset($room_array) && count($inventories) > 0 ): ?>
                                                <?php $__currentLoopData = $room_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($inventory->id == $room): ?>
                                                            <td class="text-center">
                                                                <?php echo e($inventory->min_stays_sold); ?>

                                                                <?php if(!isset($inventory->min_stays_sold)): ?>
                                                                    0
                                                                <?php endif; ?>
                                                                <?php
                                                                    $row_total_min_stays_sold += $inventory->min_stays_sold;
                                                                continue 2;
                                                                ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($remaining_count > 0): ?>
                                                    <?php for($i = 1; $i<= $remaining_count; $i++): ?>
                                                        <td class="text-center"> </td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if(count($inventories) == 0): ?>
                                                <?php $__currentLoopData = $race->room_type_inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center"> </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <td class="text-center"><strong><?php echo e($row_total_min_stays_sold); ?></strong></td>
                                        </tr>
                                        <tr class="bg-gray-200">
                                            <td></td>
                                            <td>Rooms Available</td>
                                            <?php
                                                $available = 0;
                                                $available_total = 0;
                                            ?>

                                            <?php if(isset($inventories) && isset($room_array) && count($inventories) > 0 ): ?>
                                                <?php $__currentLoopData = $room_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($inventory->id == $room): ?>
                                                            <td class="text-center">
                                                                <?php
                                                                    $available = $inventory->min_stays_contracted - $inventory->min_stays_sold;
                                                                    $available_total += $available;
                                                                ?>
                                                                <?php echo e($available); ?>

                                                                <?php
                                                                    continue 2;
                                                                ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($remaining_count > 0): ?>
                                                    <?php for($i = 1; $i<= $remaining_count; $i++): ?>
                                                        <td class="text-center"> </td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if(count($inventories) == 0): ?>
                                                <?php $__currentLoopData = $race->room_type_inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center"> </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <td class="text-center"><strong><?php echo e($available_total); ?></strong></td>
                                        </tr>
                                        <tr class="bg-gray-300">
                                            <td></td>
                                            <td>Rooms On Offer</td>
                                            <?php
                                                $row_total_min_stays_on_offer = 0;
                                            ?>

                                            <?php if(isset($inventories) && isset($room_array) && count($inventories) > 0 ): ?>
                                                <?php $__currentLoopData = $room_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($inventory->id == $room): ?>
                                                            <td class="text-center">
                                                                <?php echo e($inventory->min_stays_on_offer); ?>

                                                                <?php if(!isset($inventory->min_stays_on_offer)): ?>
                                                                    0
                                                                <?php endif; ?>
                                                                <?php
                                                                    $row_total_min_stays_on_offer += $inventory->min_stays_on_offer;
                                                                continue 2;
                                                                ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($remaining_count > 0): ?>
                                                    <?php for($i = 1; $i<= $remaining_count; $i++): ?>
                                                        <td class="text-center"> </td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if(count($inventories) == 0): ?>
                                                <?php $__currentLoopData = $race->room_type_inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center"> </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <td class="text-center"><strong><?php echo e($row_total_min_stays_on_offer); ?></strong></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</races-show>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>