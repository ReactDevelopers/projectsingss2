<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-sm-6">
        <h5 class="mb-3">Hotel Payments</h5>
        @isset($bill)
            <h6>Contract Signed: {{ $bill->friendly_contract_signed_on }}</h6>
            <h6>
                @if (isset($bill->currency) && isset($bill->currency->name))
                    Billed in:
                    {{ @$bill->currency->name }}
                @endif
                @if (isset($bill->currency) && isset($bill->currency->name) && isset($bill->currency_exchange) && isset($bill->currency_exchange->name))
                    <i class="fa fa-arrow-right"></i>
                @endif
                @if (isset($bill->currency_exchange) && isset($bill->currency_exchange->name))
                    Exchange shown:
                    {{ @$bill->currency_exchange->name }}
                @endif
            </h6>
        @endif
    </div>
    <div class="col-sm-6 text-right">
        <a href="{{ route('races.hotels.bills.edit', [
            'race' => $race->id,
            'hotel' => $hotel->id
            ]) }}" class="btn btn-primary btn-sm" data-offline="disabled" dusk="hotel-payments">
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
                    <th class="text-right">Amount due ({{ $meta->currency->symbol }})</th>
                    <th class="text-center">Due on</th>
                    <th class="text-right">Amount paid ({{ $meta->currency->symbol }})</th>
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
                    $currency_exchange = (isset($bill->currency_exchange)) ? $bill->currency_exchange : $meta->currency;
                @endphp
                @isset($bill->payments)
                    @foreach($bill->payments as $payment)
                        @php
                            $total_due += $payment->amount_due;
                            $total_paid += $payment->amount_paid;
                        @endphp
                        <tr>
                            <td>{{ $payment->payment_name }}</td>
                            <td class="text-right">
                                @money($payment->amount_due, $meta->currency->symbol)
                                @if (isset($payment->amount_paid) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol)
                                    <br>
                                    <small class="form-text text-muted">
                                        (est @money($payment->amount_due * $bill->exchange_rate, $currency_exchange->symbol))
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">{{ $payment->friendly_due_on }}</td>
                            <td class="text-right">
                                @money($payment->amount_paid, $meta->currency->symbol)
                                @if (isset($payment->amount_paid) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol)
                                    <br>
                                    <small class="form-text text-muted">
                                        (est @money($payment->amount_paid * $bill->exchange_rate, $currency_exchange->symbol))
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
                        <strong>@money($total_due, $meta->currency->symbol)</strong>
                        @if (isset($total_due) && !empty($bill->exchange_rate) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol)
                            <small class="form-text text-muted">
                                (est @money($total_due * $bill->exchange_rate, $currency_exchange->symbol))
                            </small>
                        @endif
                    </td>
                    <td></td>
                    <td class="text-right">
                        <strong>@money($total_paid, $meta->currency->symbol)</strong>
                        @if (isset($total_paid) && !empty($bill->exchange_rate) && isset($currency_exchange->symbol) && $currency_exchange->symbol !== $meta->currency->symbol)
                            <small class="form-text text-muted">
                                (est @money($total_paid * $bill->exchange_rate, $currency_exchange->symbol))
                            </small>
                        @endif
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
