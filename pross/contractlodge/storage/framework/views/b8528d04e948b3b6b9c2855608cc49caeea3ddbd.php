<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-sm-6">
        <h5 class="mb-3">Hotel Payments</h5>
        <?php if(isset($bill)): ?>
            <h6>Contract Signed: <?php echo e($bill->friendly_contract_signed_on); ?></h6>
            <h6>
                <?php if(isset($bill->currency) && isset($bill->currency->name)): ?>
                    Billed in:
                    <?php echo e(@$bill->currency->name); ?>

                <?php endif; ?>
                <?php if(isset($bill->currency) && isset($bill->currency->name) && isset($bill->currency_exchange) && isset($bill->currency_exchange->name)): ?>
                    <i class="fa fa-arrow-right"></i>
                <?php endif; ?>
                <?php if(isset($bill->currency_exchange) && isset($bill->currency_exchange->name)): ?>
                    Exchange shown:
                    <?php echo e(@$bill->currency_exchange->name); ?>

                <?php endif; ?>
            </h6>
        <?php endif; ?>
    </div>
    <div class="col-sm-6 text-right">
        <a href="<?php echo e(route('races.hotels.bills.edit', [
            'race' => $race->id,
            'hotel' => $hotel->id
            ])); ?>" class="btn btn-primary btn-sm" data-offline="disabled" dusk="hotel-payments">
            <i class="fa fa-edit mr-2"></i> Edit Payments
        </a>
    </div>
</div>

<div class="form-row">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Payment</th>
                    <th class="text-right">Amount due (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-center">Due on</th>
                    <th class="text-right">Amount paid (<?php echo e($meta->currency->symbol); ?>)</th>
                    <th class="text-center">Paid on</th>
                    <th class="text-center">{{ __('To Accounts') }}</th>
                    <th class="text-center">{{ __('Invoice Nº') }}</th>
                    <th class="text-center">{{ __('Invoice Date') }}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $total_due = 0;
                    $total_paid = 0;
                    $currency_exchange = (isset($bill->currency_exchange)) ? $bill->currency_exchange : $meta->currency;
                ?>
                <?php if(isset($bill->payments)): ?>
                    <?php $__currentLoopData = $bill->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $total_due += $payment->amount_due;
                            $total_paid += $payment->amount_paid;
                        ?>
                        <tr>
                            <td><?php echo e($payment->payment_name); ?></td>
                            <td class="text-right">
                                <?php echo ($payment->amount_due < 0) ? '-' . $meta->currency->symbol . number_format(abs($payment->amount_due), 2) : $meta->currency->symbol . number_format($payment->amount_due, 2); ?>
                                <?php if(isset($payment->amount_paid) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol): ?>
                                    <br>
                                    <small class="form-text text-muted">
                                        (est <?php echo ($payment->amount_due * $bill->exchange_rate < 0) ? '-' . $currency_exchange->symbol . number_format(abs($payment->amount_due * $bill->exchange_rate), 2) : $currency_exchange->symbol . number_format($payment->amount_due * $bill->exchange_rate, 2); ?>)
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo e($payment->friendly_due_on); ?></td>
                            <td class="text-right">
                                <?php echo ($payment->amount_paid < 0) ? '-' . $meta->currency->symbol . number_format(abs($payment->amount_paid), 2) : $meta->currency->symbol . number_format($payment->amount_paid, 2); ?>
                                <?php if(isset($payment->amount_paid) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol): ?>
                                    <br>
                                    <small class="form-text text-muted">
                                        (est <?php echo ($payment->amount_paid * $bill->exchange_rate < 0) ? '-' . $currency_exchange->symbol . number_format(abs($payment->amount_paid * $bill->exchange_rate), 2) : $currency_exchange->symbol . number_format($payment->amount_paid * $bill->exchange_rate, 2); ?>)
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo e($payment->friendly_paid_on); ?></td>
                            <td class="text-center"><?php echo e($payment->friendly_to_accounts_on); ?></td>
                            <td class="text-center"><?php echo e($payment->invoice_number); ?></td>
                            <td class="text-center"><?php echo e($payment->friendly_invoice_date); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-right">
                        <strong><?php echo ($total_due < 0) ? '-' . $meta->currency->symbol . number_format(abs($total_due), 2) : $meta->currency->symbol . number_format($total_due, 2); ?></strong>
                        <?php if(isset($total_due) && !empty($bill->exchange_rate) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol): ?>
                            <small class="form-text text-muted">
                                (est <?php echo ($total_due * $bill->exchange_rate < 0) ? '-' . $currency_exchange->symbol . number_format(abs($total_due * $bill->exchange_rate), 2) : $currency_exchange->symbol . number_format($total_due * $bill->exchange_rate, 2); ?>)
                            </small>
                        <?php endif; ?>
                    </td>
                    <td></td>
                    <td class="text-right">
                        <strong><?php echo ($total_paid < 0) ? '-' . $meta->currency->symbol . number_format(abs($total_paid), 2) : $meta->currency->symbol . number_format($total_paid, 2); ?></strong>
                        <?php if(isset($total_paid) && !empty($bill->exchange_rate) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol): ?>
                            <small class="form-text text-muted">
                                (est <?php echo ($total_paid * $bill->exchange_rate < 0) ? '-' . $currency_exchange->symbol . number_format(abs($total_paid * $bill->exchange_rate), 2) : $currency_exchange->symbol . number_format($total_paid * $bill->exchange_rate, 2); ?>)
                            </small>
                        <?php endif; ?>
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
