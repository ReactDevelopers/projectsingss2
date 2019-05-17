@extends('spark::layouts.app')

@section('content')
<clients-show :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        @isset($race->id)
                            <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                            <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                        @else
                            <a href="{{ route('clients.index') }}">{{__('Clients')}}</a> /
                        @endif
                        @isset($hotel->id)
                            <a href="{{ route('races.hotels.show', [
                                'race' => $race->id,
                                'hotel' => $hotel->id
                                ]) }}">{{ $hotel->name }}</a> /
                        @endif
                        {{ $client->name }}
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <div class="col-sm-6">
                                @isset($race->id)
                                    <h5>
                                        <a href="{{ route('clients.show', ['client' => $client->id]) }}">
                                            {{ $client->name }}
                                        </a> at {{ $hotel->name }}
                                    </h5>
                                @else
                                    <h5>{{ $client->name }}</h5>
                                @endif
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('clients.edit', ['client' => $client->id]) }}"
                                    class="btn btn-primary btn-sm" dusk="client-edit" data-offline="disabled">
                                    <i class="fa fa-edit mr-2"></i> Edit Client
                                </a>
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="col-sm-4">
                                @if (isset($hotel->id) || isset($race->id))
                                    @include('partials.clients.header-block', ['with_client_name' => true, 'contact_client' => $client->contact])
                                @else
                                    @include('partials.clients.header-block', ['contact_client' => $client->contact])
                                @endif
                            </div>
                            <div class="col-sm-4">
                                @isset($hotel->id)
                                    @include('partials.hotels.header-block', ['with_hotel_name' => true, 'contact_hotel' => $hotel->contact])
                                @endif
                            </div>
                            <div class="col-sm-4">
                                @isset($race->id)
                                    <label><strong>Race</strong></label> <br>
                                    {{ $race->full_name }} <br>
                                    {{ $race->friendly_start_on }} - {{ $race->friendly_end_on }}
                                @endif
                            </div>
                        </div>

                        <hr class="mb-5">

                        <div class="form-row mb-4">
                            <div class="col-sm-6">
                                <h5 class="mb-3">Invoices / Confirmations</h5>
                            </div>
                            <div class="col-sm-6 text-right">
                                @isset($race->id)
                                    <a href="{{ route('races.hotels.clients.invoices.create', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'invoice_type' => 'confirmations'
                                        ]) }}" class="btn btn-primary btn-sm mr-2" data-offline="disabled">
                                        <i class="fa fa-plus mr-2"></i> Add Rooms Confirmation
                                    </a>
                                    <a href="{{ route('races.hotels.clients.invoices.create', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'invoice_type' => 'extras'
                                        ]) }}" class="btn btn-primary btn-sm" data-offline="disabled">
                                        <i class="fa fa-plus mr-2"></i> Add Extras Invoice
                                    </a>
                                @else
                                    {{-- Commented by Nate because we don't currently support
                                    non-race-specified room confirmation creation yet --}}

                                    {{-- <a href="{{ route('clients.invoices.create', [
                                        'client' => $client->id,
                                        'invoice_type' => 'confirmations'
                                        ]) }}" class="btn btn-primary btn-sm mr-2">
                                        <i class="fa fa-plus mr-2"></i> Add Rooms Confirmation
                                    </a> --}}
                                    <a href="{{ route('clients.invoices.create', [
                                        'client' => $client->id,
                                        'invoice_type' => 'extras'
                                        ]) }}" class="btn btn-primary btn-sm" data-offline="disabled">
                                        <i class="fa fa-plus mr-2"></i> Add Extras Invoice
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-sm-12">
                                <table class="table table-sm table-striped override-table">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            @if (! isset($hotel->id))
                                                <th>Hotel</th>
                                            @endif
                                            <th>Race</th>
                                            <th>Sent On</th>
                                            <th>Expires On</th>
                                            <th class="text-right">Amount Due</th>
                                            <th>Signed On</th>
                                            <th class="text-right">Amount Paid</th>
                                            <th class="text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @isset($recievables)
                                            @php
                                                $total_total_amount_due = 0;
                                                $total_total_amount_paid = 0;
                                                $total_total_balance = 0;
                                            @endphp
                                            @foreach($recievables as $recievable)
                                                <tr>
                                                    <td>
                                                        @isset($hotel->id)
                                                            @isset($recievable->confirmation_items)
                                                                <a href="{{ route('races.hotels.clients.confirmations.show', [
                                                                    'race' => $race->id,
                                                                    'hotel' => $hotel->id,
                                                                    'client' => $client->id,
                                                                    'invoice' => $recievable->id
                                                                    ]) }}">Confirmation #{{ $recievable->id }}</a>
                                                            @else
                                                                <a href="{{ route('races.hotels.clients.invoices.show', [
                                                                    'race' => $race->id,
                                                                    'hotel' => $hotel->id,
                                                                    'client' => $client->id,
                                                                    'invoice' => $recievable->id
                                                                    ]) }}">Invoice #{{ $recievable->id }}</a>
                                                            @endif
                                                        @else
                                                            @isset($recievable->confirmation_items)
                                                                <a href="{{ route('clients.confirmations.show', [
                                                                    'client' => $client->id,
                                                                    'invoice' => $recievable->id
                                                                    ]) }}">Confirmation #{{ $recievable->id }}</a>
                                                            @else
                                                                <a href="{{ route('clients.invoices.show', [
                                                                    'client' => $client->id,
                                                                    'invoice' => $recievable->id
                                                                    ]) }}">Invoice #{{ $recievable->id }}</a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    @if (! isset($hotel->id))
                                                        <td>
                                                            @isset($hotel_name)
                                                                {{ $hotel_name }}
                                                            @elseif (isset($recievable->race_hotel->hotel))
                                                                {{ $recievable->race_hotel->hotel->name }}
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>
                                                        @isset($race->id)
                                                            <a href="{{ route('races.show', ['race' => $race->id]) }}">
                                                                {{ $race->full_name }}
                                                            </a>
                                                        @elseif (isset($recievable->race_hotel->race))
                                                            <a href="{{ route('races.show', [
                                                                'race' => $recievable->race_hotel->race->id
                                                                ]) }}">
                                                                {{ $recievable->race_hotel->race->full_name }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @isset($recievable->friendly_sent_on)
                                                            {{ $recievable->friendly_sent_on }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @isset($recievable->friendly_expires_on)
                                                            {{ $recievable->friendly_expires_on }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @php $total_amount_due = 0; @endphp
                                                        @isset($recievable->confirmation_items)
                                                            @foreach ($recievable->confirmation_items as $confirmation_item)
                                                                @php
                                                                    $total_amount_due += ($confirmation_item->rate * $confirmation_item->friendly_room_nights);
                                                                @endphp
                                                            @endforeach
                                                        @else
                                                            @foreach ($recievable->invoice_items as $invoice_item)
                                                                @php
                                                                    $total_amount_due += ($invoice_item->quantity * $invoice_item->rate);
                                                                @endphp
                                                            @endforeach
                                                        @endif
                                                        @php $total_total_amount_due += $total_amount_due; @endphp
                                                        @money($total_amount_due, $recievable->currency->symbol)
                                                    </td>
                                                    <td>
                                                        @isset($recievable->confirmation_items)
                                                            {{ $recievable->friendly_signed_on }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @php $total_amount_paid = 0; @endphp
                                                        @foreach ($recievable->payments as $payment)
                                                            @php
                                                                $total_amount_paid += ($payment->amount_paid);
                                                            @endphp
                                                        @endforeach
                                                        @php $total_total_amount_paid += $total_amount_paid; @endphp
                                                        @money($total_amount_paid, $recievable->currency->symbol)
                                                    </td>
                                                    <td class="text-right">
                                                        @php
                                                            $total_balance = $total_amount_due - $total_amount_paid;
                                                            $total_total_balance += $total_balance;
                                                        @endphp
                                                        @money($total_balance, $recievable->currency->symbol)
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                            {{-- <tr>
                                                @isset($hotel->id)
                                                    <td colspan="6">Hotel: {{ $hotel->name }} (6)</td>
                                                @else
                                                    <td colspan="7">No hotel id (7)</td>
                                                @endif
                                            </tr> --}}

                                    </tbody>
                                    {{--
                                        FIXME: Commenting this out becuase we can't be assured at this point that
                                        the currencies are always the same. This may not be resolvable.
                                    --}}
                                    {{-- <tfoot>
                                        <tr>
                                            <td><strong>Totals</strong></td>
                                            @if (! isset($hotel->id))
                                                <td><strong>-</strong></td>
                                            @endif
                                            <td><strong>-</strong></td>
                                            <td class="text-right">
                                                <strong>@money($total_total_amount_due, $recievable->currency->symbol)</strong>
                                            </td>
                                            <td><strong>-</strong></td>
                                            <td class="text-right">
                                                <strong>@money($total_total_amount_paid, $recievable->currency->symbol)</strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>@money($total_total_balance, $recievable->currency->symbol)</strong>
                                            </td>
                                        </tr>
                                    </tfoot> --}}
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</clients-show>
@endsection
