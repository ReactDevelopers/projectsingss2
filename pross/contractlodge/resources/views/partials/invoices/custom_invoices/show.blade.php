<div class="form-row mt-5">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Rate ({{ $custom_invoice->currency->name }})</th>
                    <th class="text-right">Total ({{ $custom_invoice->currency->name }})</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totals = 0;
                    $totals_exchanged = 0;
                @endphp
                @isset($custom_invoice->invoice_items)
                    @foreach ($custom_invoice->invoice_items as $invoice_item)
                        <tr>
                            <td>{{ $invoice_item->friendly_date }}</td>
                            <td>{{ $invoice_item->description }}</td>
                            <td class="text-right">{{ $invoice_item->quantity }}</td>
                            <td class="text-right">
                                @money(($invoice_item->rate), $custom_invoice->currency->symbol)
                                <small class="text-muted ml-2">
                                    (@money(($invoice_item->rate_exchanged), $meta->currency->symbol))
                                </small>
                            </td>
                            <td class="text-right">
                                @money(($invoice_item->rate * $invoice_item->quantity), $custom_invoice->currency->symbol)
                                <small class="text-muted ml-2">
                                    (@money(($invoice_item->rate_exchanged * $invoice_item->quantity), $meta->currency->symbol))
                                </small>
                            </td>
                            @php
                                $totals += $invoice_item->rate * $invoice_item->quantity;
                                $totals_exchanged += $invoice_item->rate_exchanged * $invoice_item->quantity;
                            @endphp
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td><strong>Totals</strong></td>
                    <td><strong></strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-right">
                        <strong>
                            @money($totals, $custom_invoice->currency->symbol)
                        </strong>
                        <small class="text-muted ml-2">
                            (@money($totals_exchanged, $meta->currency->symbol))
                        </small>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="form-row my-4">
    <div class="col-sm-12">
        <label><strong>Additional Notes</strong></label> <br>
        @isset($custom_invoice->notes)
            {{ $custom_invoice->notes }}
        @endif
    </div>
</div>
@include('partials.common.uploads')

