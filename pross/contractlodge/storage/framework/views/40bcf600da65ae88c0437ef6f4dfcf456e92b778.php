<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-sm-6">
        <h5 class="mb-3">Client Breakdown</h5>
    </div>
    <div class="col-sm-6 text-right">
        <?php if(isset($meta->inventory_min_check_in) && ! empty($meta->inventory_min_check_in)): ?>
            <a href="<?php echo e(route('races.hotels.invoices.create', [
                'race' => $race->id,
                'hotel' => $hotel->id,
                'invoice_type' => 'confirmations'
                ])); ?>" class="btn btn-primary btn-sm ml-2" data-offline="disabled" dusk="add-room-confirmation">
                <i class="fa fa-plus mr-2"></i> Add Rooms Confirmation
            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('races.hotels.invoices.create', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'invoice_type' => 'extras'
            ])); ?>" class="btn btn-primary btn-sm ml-2" data-offline="disabled" dusk="add-extras-invoice">
            <i class="fa fa-plus mr-2"></i> Add Extras Invoice
        </a>
    </div>
</div>

<div class="form-row">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th class="text-center">Confirmed</th>
                    <th class="text-center">Rooms On Offer</th>
                    <th class="text-center">P&P Nts On Offer</th>
                    <?php
                        $signed_inventories = [];
                    ?>
                    <?php if(isset($inventories) && !empty($inventories)): ?>
                        <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0)): ?>
                                <?php $show_room_type = false; ?>
                                <?php $__currentLoopData = $inventory->confirmation_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $confirmation_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($confirmation_item->confirmation->signed_on) && ! empty($confirmation_item->confirmation->signed_on)): ?>
                                        <?php
                                            $show_room_type = true;
                                            $signed_inventories[$inventory->id] = $inventory->id;
                                            break;
                                        ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($show_room_type): ?>
                                    <th class="text-right">
                                        <?php echo e($inventory->room_name); ?>

                                    </th>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <th class="text-right">Extras</th>
                    <th class="text-right">Totals</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $all_clients_extras = 0;
                    $all_clients_total = 0;
                    $room_type_totals = [];
                    $all_clients_min_stays_sold = 0;
                    $all_clients_min_stays_on_offer = 0;
                    $all_clients_pre_post_nights_on_offer = 0;
                ?>
                <?php if(isset($clients)): ?>
                    <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $client_total = 0;
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('races.hotels.clients.show', [
                                    'race' => $race->id,
                                    'hotel' => $hotel->id,
                                    'client' => $client->id
                                    ])); ?>">
                                    <?php echo e($client->name); ?>

                                </a>
                            </td>
                            <td class="text-center">
                                <?php
                                    $client_total += $client->room_types->sum('amount');
                                ?>
                                <?php $all_clients_min_stays_sold += $client->min_stays_sold ?>
                                <?php echo e($client->min_stays_sold); ?>

                            </td>
                            <td class="text-center">
                                <?php echo e($client->min_stays_on_offer); ?>

                                <?php $all_clients_min_stays_on_offer += $client->min_stays_on_offer ?>
                            </td>
                            <td class="text-center">
                                <?php echo e($client->pre_post_nights_on_offer); ?>

                                <?php $all_clients_pre_post_nights_on_offer += $client->pre_post_nights_on_offer ?>
                            </td>
                            <?php $counter = 0; ?>
                            <?php if(isset($inventories) && !empty($inventories)): ?>
                                <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0) && in_array($inventory->id, $signed_inventories)): ?>
                                        <td class="text-right">
                                            <?php
                                                $room_type = $client->room_types->where('id', $inventory->id)->first();
                                                $amount = isset($room_type['amount']) ? $room_type['amount'] : 0;
                                                if (isset($room_type_totals[$counter])) {
                                                    $room_type_totals[$counter] += $amount;
                                                } else {
                                                    $room_type_totals[] = $amount;
                                                }
                                                $counter++;
                                            ?>
                                            <?php echo ($amount < 0) ? '-' . $meta->currency->symbol . number_format(abs($amount), 2) : $meta->currency->symbol . number_format($amount, 2); ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <td class="text-right">
                                <?php
                                    $client_total += $client->invoices->sum('amount');
                                    $all_clients_extras += $client->invoices->sum('amount');
                                ?>
                                <?php echo ($client->invoices->sum('amount') < 0) ? '-' . $meta->currency->symbol . number_format(abs($client->invoices->sum('amount')), 2) : $meta->currency->symbol . number_format($client->invoices->sum('amount'), 2); ?>
                            </td>
                            <td class="text-right">
                                <?php echo ($client_total < 0) ? '-' . $meta->currency->symbol . number_format(abs($client_total), 2) : $meta->currency->symbol . number_format($client_total, 2); ?>
                                <?php $all_clients_total += $client_total; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-center">
                        <strong>
                            <?php echo e($all_clients_min_stays_sold); ?>

                        </strong>
                    </td>
                    <td class="text-center"><strong><?php echo e($all_clients_min_stays_on_offer); ?></strong></td>
                    <td class="text-center"><strong><?php echo e($all_clients_pre_post_nights_on_offer); ?></strong></td>
                    <?php $counter = 0; ?>
                    <?php if(isset($inventories) && ! empty($inventories)): ?>
                        <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0) && in_array($inventory->id, $signed_inventories)): ?>
                                <th class="text-right">
                                    <?php if(isset($room_type_totals[$counter])): ?>
                                        <strong><?php echo ($room_type_totals[$counter] < 0) ? '-' . $meta->currency->symbol . number_format(abs($room_type_totals[$counter]), 2) : $meta->currency->symbol . number_format($room_type_totals[$counter], 2); ?></strong>
                                    <?php endif; ?>
                                </th>
                                <?php $counter++; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <td class="text-right"><strong><?php echo ($all_clients_extras < 0) ? '-' . $meta->currency->symbol . number_format(abs($all_clients_extras), 2) : $meta->currency->symbol . number_format($all_clients_extras, 2); ?></strong></td>
                    <td class="text-right"><strong><?php echo ($all_clients_total < 0) ? '-' . $meta->currency->symbol . number_format(abs($all_clients_total), 2) : $meta->currency->symbol . number_format($all_clients_total, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
