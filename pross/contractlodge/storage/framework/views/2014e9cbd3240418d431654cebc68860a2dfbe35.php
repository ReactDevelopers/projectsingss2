<?php $__env->startSection('content'); ?>
<bills :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        

                        <?php if(isset($showPaid)): ?>
                            <a href="<?php echo e(route('bills.index')); ?>" class="btn btn-success btn-sm float-right mr-3"
                                role="button">
                                <i class="fa fa-bolt mr-2"></i> <?php echo e(__('View Unpaid Payments')); ?>

                            </a>
                        <?php else: ?>
                            
                        <?php endif; ?>

                        Reports /

                        <?php if(isset($showPaid)): ?>
                            <?php echo e(__('Paid')); ?>

                        <?php else: ?>
                            <?php echo e(__('Unpaid')); ?>

                        <?php endif; ?>

                        <?php echo e(__('Hotel Payments')); ?>

                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <?php if(! $bills->count()): ?>
                                <?php if(isset($showPaid)): ?>
                                    <div class="col-sm-12"><p>There are currently no paid hotel bills.</p></div>
                                <?php else: ?>
                                    <div class="col-sm-12"><p>No unpaid hotel bills found.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Race</th>
                                                <th>Hotel</th>
                                                <th>Description</th>
                                                <th class="text-right">Amount Due</th>
                                                <th class="text-right">Amount Paid</th>
                                                <th class="text-right">Outstanding</th>
                                                <th class="text-center">Due on</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $bill->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <?php if(isset($showPaid)): ?>
                                                                <?php echo e($bill->race_hotel->race->full_name); ?>

                                                            <?php else: ?>
                                                                <a href="<?php echo e(route('races.show', [
                                                                    'race' => $bill->race_hotel->race->id
                                                                    ])); ?>">
                                                                    <?php echo e($bill->race_hotel->race->full_name); ?>

                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if(isset($showPaid)): ?>
                                                            <?php else: ?>
                                                                <a href="<?php echo e(route('races.hotels.show', [
                                                                    'race' => $bill->race_hotel->race->id,
                                                                    'hotel' => $bill->race_hotel->hotel->id
                                                                ])); ?>"><?php echo e($bill->race_hotel->hotel->name); ?></a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo e(route('races.hotels.bills.edit', [
                                                                'race' => $bill->race_hotel->race->id,
                                                                'hotel' => $bill->race_hotel->hotel->id,
                                                            ])); ?>"><?php echo e($payment->payment_name); ?></a>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php echo ($payment->amount_due < 0) ? '-' . $bill->currency->symbol . number_format(abs($payment->amount_due), 2) : $bill->currency->symbol . number_format($payment->amount_due, 2); ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php echo ($payment->amount_paid < 0) ? '-' . $bill->currency->symbol . number_format(abs($payment->amount_paid), 2) : $bill->currency->symbol . number_format($payment->amount_paid, 2); ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php echo (($payment->amount_due - $payment->amount_paid) < 0) ? '-' . $bill->currency->symbol . number_format(abs(($payment->amount_due - $payment->amount_paid)), 2) : $bill->currency->symbol . number_format(($payment->amount_due - $payment->amount_paid), 2); ?>
                                                        </td>
                                                        <td class="text-center"><?php echo e($payment->friendly_due_on); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</bills>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>