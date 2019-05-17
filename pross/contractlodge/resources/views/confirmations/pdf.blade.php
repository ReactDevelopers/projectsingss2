@extends('layouts.pdf')

@section('content')
    <div id="top" class="page" role="document">
        @include('partials.invoices.confirmations.pdf-header')

        <header><h4 style="text-align: center;">{{ $client->name }}</h4></header>

        <main role="main">
            <div style="float:left; width: 40%">
                @isset($contact_client->name)
                    <h6>ATTENTION: {{ $contact_client->name }}</h6>
                @endisset
            </div>

            <p style="text-align: right;">
                Date: {{ \Carbon\Carbon::parse($confirmation->created_at)->format('d-M-Y') }} <br>
                Race: {{ $race->full_name }} <br>

                @isset($race->race_code)
                    Race Code: {{ $race->race_code }} <br>
                @endif

                Confirmation Nº: {{ $race->race_code }}-{{ $confirmation->id }} <br>
            </p>

            <header>
                <h6 style="text-align: center; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                    CONFIRMATION OF HOTEL ACCOMMODATION
                </h6>
            </header>

            <section>
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
                </article>

                <hr>

                <article style="margin-left: 25%; width: 50%;">
                    <dl>
                        <dt>Min Nights:</dt>
                        <dd>Mon - Mon</dd>

                        <dt>Number of Nights:</dt>
                        <dd>7</dd>

                        {{-- <dt>Check-in Time:</dt>
                        <dd>16:00</dd>

                        <dt>Check-out Time:</dt>
                        <dd>11:00</dd> --}}
                    </dl>
                </article>
            </section>

            <br style="clear:both;">

            <section>
                <header style="margin-top: 10px;">
                    <h6 style="text-align: center; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                        BOOKING CONFIRMED ACCORDING TO THE STIPULATED PERIOD
                    </h6>
                </header>

                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Room Type</th>
                            <th style="text-align: center;">Nº Rooms</th>
                            <th style="text-align: center;">Check-in/out</th>
                            <th style="text-align: center;">Days</th>
                            <th style="text-align: center;">Room Nights</th>
                            <th style="text-align: right;">Rate ({{ $confirmation->currency->name }})</th>
                            <th style="text-align: right;">Total ({{$confirmation->currency->name}})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_rooms = 0;
                            $total_room_nights = 0;
                            $total_revenue = 0;
                        @endphp
                        @isset($confirmation->confirmation_items)
                            @foreach ($confirmation->confirmation_items as $confirmation_item)
                                @php
                                    $total_rooms += $confirmation_item->quantity;
                                    $total_room_nights += $confirmation_item->friendly_room_nights;
                                    $total_revenue += ($confirmation_item->friendly_room_nights * $confirmation_item->rate);
                                @endphp
                                <tr>
                                    <td style="text-align: left;">{{ $confirmation_item->races_hotels_inventory->room_name }}</td>
                                    <td style="text-align: center;">{{ $confirmation_item->quantity }}</td>
                                    <td style="text-align: center;">
                                        {{ $confirmation_item->friendly_check_in }}<br>
                                        - {{ $confirmation_item->friendly_check_out }}
                                    </td>
                                    <td style="text-align: center;">{{ $confirmation_item->friendly_diff }}</td>
                                    <td style="text-align: center;">{{ $confirmation_item->friendly_room_nights }}</td>
                                    <td style="text-align: right;">@money($confirmation_item->rate, $confirmation->currency->symbol)</td>
                                    <td style="text-align: right;">@money(($confirmation_item->friendly_room_nights * $confirmation_item->rate), $confirmation->currency->symbol)</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: left;"><strong>TOTALS</strong></th>
                            <th style="text-align: center;"><strong>{{ $total_rooms }}</strong></th>
                            <th></th>
                            <th></th>
                            <th style="text-align: center;"><strong>{{ $total_room_nights }}</strong></th>
                            <th style="text-align: right;"><strong></strong></th>
                            <th style="text-align: right;">
                                <strong>({{ $confirmation->currency->name }})  @money($total_revenue, $confirmation->currency->symbol)</strong><br>
                                @if ($confirmation->currency->symbol !== $meta->currency->symbol)
                                    <small>(est {{ $meta->currency->name }} @money(($total_revenue * $confirmation->exchange_rate), $confirmation->currency->symbol))</small>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <p>{{ $confirmation->notes }}</p>

                <h6 style="text-align: center;">
                    This confirmation must be signed within 72 hours unless otherwise stated
                </h6>
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
                            <th style="text-align: right;">Amount due ({{ $confirmation->currency->name }})</th>
                            <th style="text-align: center;">Due on</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($confirmation->payments)
                            @foreach($confirmation->payments as $payment)
                                <tr>
                                    <td style="min-width: 50%;">{{ $payment->payment_name }}</td>
                                    <td style="text-align: right;">
                                        @money($payment->amount_due, $confirmation->currency->symbol)
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

            <p style="margin-top: 25px;">This confirmation is subject to our terms and conditions as stipulated below.</p>

        </main>

        @include('partials.invoices.confirmations.pdf-terms')

        <p style="margin-top: 20px; margin-bottom: 20px; color: #fff;">
            [sig|req]<br>
            [date|req]
        </p>

        @include('partials.invoices.confirmations.pdf-footer')
    </div>
@endsection
