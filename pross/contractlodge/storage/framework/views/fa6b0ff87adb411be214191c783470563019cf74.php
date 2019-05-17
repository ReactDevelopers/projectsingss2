<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-md-4">
        <h5 class="mb-3">
            Room Types and Rates
        </h5>
        <?php if(isset($meta->currency) && isset($meta->currency->name)): ?>
            <h6>
                Currency: <?php echo e(@$meta->currency->name); ?>

            </h6>
        <?php endif; ?>
        <?php if(isset($meta->inventory_min_check_in) && isset($meta->inventory_min_check_out)): ?>
            <h6>
                Min Nights: <?php echo e($meta->friendly_min_check_in); ?> - <?php echo e($meta->friendly_min_check_out); ?> (<?php echo e($meta->num_min_nights); ?> nights)
            </h6>
        <?php endif; ?>
        <?php if(isset($meta->inventory_notes)): ?>
            <p>
                Notes: <?php echo e($meta->inventory_notes); ?>

            </p>
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <a href="<?php echo e(route('races.hotels.edit', ['hotel' => $hotel->id, 'race' => $race->id])); ?>"
            class="btn btn-primary btn-sm float-right"  data-offline="disabled" dusk="room-types-and-rates">
            <i class="fa fa-edit mr-2"></i> Edit Room Types and Rates
        </a>
        <?php echo $__env->make('partials.common.uploads-listings', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</div>

<div class="form-row">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th class="text-right">Min/Nt Hotel (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-right">Min/Nt Client (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-center">Rooms Booked</th>
                    <th class="text-center">Rooms Sold</th>
                    <th class="text-center">Rooms On Offer</th>
                    <th class="text-right">P&P/Nt Hotel (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-right">P&P/Nt Client (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-center">Nts Booked</th>
                    <th class="text-center">Nts Sold</th>
                    <th class="text-center">Nts On Offer</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($inventory->room_name); ?></td>
                        <td class="text-right"><?php echo ($inventory->min_night_hotel_rate < 0) ? '-' . $meta->currency->symbol . number_format(abs($inventory->min_night_hotel_rate), 2) : $meta->currency->symbol . number_format($inventory->min_night_hotel_rate, 2); ?></td>
                        <td class="text-right"><?php echo ($inventory->min_night_client_rate < 0) ? '-' . $meta->currency->symbol . number_format(abs($inventory->min_night_client_rate), 2) : $meta->currency->symbol . number_format($inventory->min_night_client_rate, 2); ?></td>
                        <td class="text-center"><?php echo e($inventory->min_stays_contracted); ?></td>
                        <td class="text-center"><?php echo e($inventory->min_stays_sold); ?></td>
                        <td class="text-center"><?php echo e($inventory->min_stays_on_offer); ?></td>
                        <td class="text-right"><?php echo ($inventory->pre_post_night_hotel_rate < 0) ? '-' . $meta->currency->symbol . number_format(abs($inventory->pre_post_night_hotel_rate), 2) : $meta->currency->symbol . number_format($inventory->pre_post_night_hotel_rate, 2); ?></td>
                        <td class="text-right"><?php echo ($inventory->pre_post_night_client_rate < 0) ? '-' . $meta->currency->symbol . number_format(abs($inventory->pre_post_night_client_rate), 2) : $meta->currency->symbol . number_format($inventory->pre_post_night_client_rate, 2); ?></td>
                        <td class="text-center"><?php echo e($inventory->pre_post_nights_contracted); ?></td>
                        <td class="text-center"><?php echo e($inventory->pre_post_nights_sold); ?></td>
                        <td class="text-center"><?php echo e($inventory->pre_post_nights_on_offer); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-right"><strong><?php echo ($totals['min_night_hotel_amount'] < 0) ? '-' . $meta->currency->symbol . number_format(abs($totals['min_night_hotel_amount']), 2) : $meta->currency->symbol . number_format($totals['min_night_hotel_amount'], 2); ?></strong></td>
                    <td class="text-right"><strong><?php echo ($totals['min_night_client_amount'] < 0) ? '-' . $meta->currency->symbol . number_format(abs($totals['min_night_client_amount']), 2) : $meta->currency->symbol . number_format($totals['min_night_client_amount'], 2); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['min_stays_contracted']); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['min_stays_sold']); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['min_stays_on_offer']); ?></strong></td>
                    <td class="text-right"><strong><?php echo ($totals['pre_post_night_hotel_amount'] < 0) ? '-' . $meta->currency->symbol . number_format(abs($totals['pre_post_night_hotel_amount']), 2) : $meta->currency->symbol . number_format($totals['pre_post_night_hotel_amount'], 2); ?></strong></td>
                    <td class="text-right"><strong><?php echo ($totals['pre_post_night_client_amount'] < 0) ? '-' . $meta->currency->symbol . number_format(abs($totals['pre_post_night_client_amount']), 2) : $meta->currency->symbol . number_format($totals['pre_post_night_client_amount'], 2); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['pre_post_nights_contracted']); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['pre_post_nights_sold']); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($totals['pre_post_nights_on_offer']); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
