@extends('layouts.pdf')

@section('content')
    <div id="top" class="page" role="document">
        @include('partials.invoices.custom_invoices.pdf-header')

        <header><h4 style="text-align: center;">{{ $client->name }}</h4></header>

        <main role="main">
            <div style="float:left; width: 40%">
                <h6>ATTENTION: {{ $contact_client->name }}</h6>
            </div>

            <p style="text-align: right;">
                Date: {{ \Carbon\Carbon::parse($custom_invoice->created_at)->format('d-M-Y') }} <br>
                Race: {{ $race->full_name }} <br>

                @isset($race->race_code)
                    Race Code: {{ $race->race_code }} <br>
                @endif

                Invoice Nº: {{ $race->race_code }}-{{ $custom_invoice->id }} <br>
            </p>

            <header>
                <h6 style="text-align: center; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                    INVOICE
                </h6>
            </header>

            <section>
                @isset($hotel->id)
                    <article style="margin-left: 25%; width: 50%;">
                        <dl>
                            <dt>Hotel:</dt>
                            <dd>{{ $hotel->name }}</dd>

                            <dt>Address:</dt>
                            <dd>
                                {{ $hotel->address }} <br>
                                {{ $hotel->city }}, {{ $hotel->region }} {{ $hotel->postal_code }}
                                @isset($hotel->country->name)
                                    <br>{{ $hotel->country->name }}
                                @endif
                            </dd>

                            @isset($hotel->phone)
                                <dt>Tel:</dt>
                                <dd>
                                    {{ $hotel->phone }} <br>
                                </dd>
                            @endif

                            @isset($hotel->website)
                                <dt>Website:</dt>
                                <dd>
                                    {{ $hotel->website }}
                                </dd>
                            @endif
                        </dl>
                        <br>
                    </article>
                @endif
            </section>

            <br style="clear:both;">

            <section>
                <header style="margin-top: 10px;">
                    {{-- <h6 style="text-align: center; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                        CONFIRMED ACCORDING TO THE STIPULATED PERIOD
                    </h6> --}}
                </header>

                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Date</th>
                            <th style="text-align: left;">Description</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Rate</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_revenue = 0;
                        @endphp
                        @isset($custom_invoice->invoice_items)
                            @foreach ($custom_invoice->invoice_items as $invoice_item)
                                @php
                                    $total_revenue += ($invoice_item->quantity * $invoice_item->rate);
                                @endphp
                                <tr>
                                    <td style="text-align: left;">{{ $invoice_item->friendly_date }}</td>
                                    <td style="text-align: left;">{{ $invoice_item->description }}</td>
                                    <td style="text-align: center;">{{ $invoice_item->quantity }}</td>
                                    <td style="text-align: right;">@money($invoice_item->rate, $custom_invoice->currency->symbol)</td>
                                    <td style="text-align: right;">@money(($invoice_item->quantity * $invoice_item->rate), $custom_invoice->currency->symbol)</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: left;"><strong>TOTALS</strong></th>
                            <th style="text-align: center;"><strong></strong></th>
                            <th style="text-align: center;"><strong></strong></th>
                            <th style="text-align: right;"><strong></strong></th>
                            <th style="text-align: right;">
                                <strong>({{ $custom_invoice->currency->name }})  @money($total_revenue, $custom_invoice->currency->symbol)</strong><br>
                                @if ($confirmation->currency->symbol !== $meta->currency->symbol)
                                    <small>(est {{ $meta->currency->name }} @money(($total_revenue * $custom_invoice->exchange_rate), $custom_invoice->currency->symbol))</small>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <br>
                <p>{{ $custom_invoice->notes }}</p>

                {{-- <h6 style="text-align: center;">
                    This custom_invoice must be signed within 72 hours unless otherwise stated
                </h6> --}}
            </section>

            <br style="clear:both;">

            <section>
                <header>
                    <h6>Payment Schedule</h6>
                </header>

                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50%;"></th>
                            <th style="text-align: right;">Amount due ({{ $custom_invoice->currency->name }})</th>
                            <th style="text-align: center;">Due on</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($custom_invoice->payments)
                            @foreach($custom_invoice->payments as $payment)
                                <tr>
                                    <td style="min-width: 50%;">{{ $payment->payment_name }}</td>
                                    <td style="text-align: right;">
                                        @money($payment->amount_due, $custom_invoice->currency->symbol)
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $payment->friendly_due_on }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </section>

        </main>

        <p style="margin-top: 20px; margin-bottom: 20px; color: #fff;">
            [sig|req]<br>
            [date|req]
        </p>

        @include('partials.invoices.custom_invoices.pdf-bank-info')

        @include('partials.invoices.custom_invoices.pdf-footer')
    </div>
@endsection
