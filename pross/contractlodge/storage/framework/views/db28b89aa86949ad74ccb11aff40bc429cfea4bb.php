<?php $__env->startSection('content'); ?>
<reports-confirmation :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        Reports /
                        <?php echo e(__('Outstanding Confirmations')); ?>

                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <?php if(empty($confirmations)): ?>
                                <div class="col-sm-12"><p>There are currently no outstanding confirmaitons.</p></div>
                            <?php else: ?>
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Confirmation Nº</th>
                                                <th>Race</th>
                                                <th>Client</th>
                                                <th>Sent on</th>
                                                <th>Expires on</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-center">Rooms/Nts</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $confirmations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $confirmation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo e(route('races.hotels.clients.confirmations.show', [
                                                            'race' => $confirmation->race_hotel->race->id,
                                                            'hotel' => $confirmation->race_hotel->hotel->id,
                                                            'client' => $confirmation->client->id,
                                                            'confirmation' => $confirmation->id
                                                            ])); ?>">
                                                            <?php echo e($confirmation->race_hotel->race->race_code); ?>-<?php echo e($confirmation->id); ?>

                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php echo e($confirmation->race_hotel->race->name); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($confirmation->client->name); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($confirmation->friendly_sent_on); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($confirmation->friendly_expires_on); ?>

                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo (get_confirmation_total_amount($confirmation) < 0) ? '-' . $confirmation->currency->symbol . number_format(abs(get_confirmation_total_amount($confirmation)), 2) : $confirmation->currency->symbol . number_format(get_confirmation_total_amount($confirmation), 2); ?>
                                                        (<?php echo e($confirmation->currency->name); ?>)
                                                    </td>
                                                    <td class="text-center">
                                                        <?php echo e(get_confirmation_total_rooms($confirmation)); ?>

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
</reports-confirmation>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>