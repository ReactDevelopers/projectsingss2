<div class="form-row mb-3">
    <div class="col-sm-6">
        <h5 class="mb-3">Payment Schedule</h5>
    </div>
</div>
<div class="form-row mb-5">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Payment</th>
                    <th class="text-right">Amount due ({{ $currency->name }})</th>
                    <th class="text-center">Due on</th>
                    <th class="text-right">Amount paid ({{ $currency->name }})</th>
                    <th class="text-center">Paid on</th>
                    <th class="text-center">@{{ __('To Accounts') }}</th>
                    <th class="text-center">@{{ __('Invoice Nº') }}</th>
                    <th class="text-center">@{{ __('Invoice Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_due = 0;
                    $total_paid = 0;
                    $total_due_exchanged = 0;
                    $total_paid_exchanged = 0;
                @endphp
                @isset($payments)
                    @foreach($payments as $payment)
                        @php
                            $total_due += $payment->amount_due;
                            $total_paid += $payment->amount_paid;
                            $total_due_exchanged += ($payment->amount_due * $exchange_rate);
                            $total_paid_exchanged += ($payment->amount_due * $exchange_rate);
                        @endphp
                        <tr>
                            <td>{{ $payment->payment_name }}</td>
                            <td class="text-right">
                                @money($payment->amount_due, $currency->symbol)
                                @if ($currency->symbol !== $meta->currency->symbol)
                                    <small class="text-muted ml-2">
                                        (est @money(($payment->amount_due * $exchange_rate), $meta->currency->symbol))
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $payment->friendly_due_on }}
                            </td>
                            <td class="text-right">
                                @money($payment->amount_paid, $currency->symbol)
                                @if ($currency->symbol !== $meta->currency->symbol)
                                    <small class="text-muted ml-2">
                                        (est @money(($payment->amount_paid * $exchange_rate), $meta->currency->symbol))
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">{{ $payment->friendly_paid_on }}</td>
                            <td class="text-center">{{ $payment->friendly_to_accounts_on }}</td>
                            <td class="text-center">{{ $payment->invoice_number }}</td>
                            <td class="text-center">{{ $payment->friendly_invoice_date }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-right">
                        <strong>@money($total_due, $currency->symbol)</strong>
                        @if ($currency->symbol !== $meta->currency->symbol)
                            <small class="text-muted ml-2">
                                (est @money($total_due_exchanged, $meta->currency->symbol))
                            </small>
                        @endif
                    </td>
                    <td></td>
                    <td class="text-right">
                        <strong>@money($total_paid, $currency->symbol)</strong>
                        @if ($currency->symbol !== $meta->currency->symbol)
                            <small class="text-muted ml-2">
                                (est @money($total_paid_exchanged, $meta->currency->symbol))
                            </small>
                        @endif
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
