@extends('spark::layouts.app')

@section('content')
<invoices-show :user="user"
    :invoice-id="{{ isset($confirmation->id) ? $confirmation->id : $custom_invoice->id }}"
    invoice-type="{{ isset($confirmation->id) ? 'confirmations' : 'extras' }}"
    inventory-contact-id="{{ isset($contact_client) ? $contact_client->id : '' }}"
    client-id="{{ isset($confirmation->client_id) ? $confirmation->client_id : $custom_invoice->client_id }}"
    :race-id="{{ isset($race->id) ? $race->id : '0' }}"
    :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}"
    :race-hotel-id="{{ isset($meta->id) ? $meta->id : '0' }}"
    :inventory-currency-id="{{ isset($meta->inventory_currency_id) ? $meta->inventory_currency_id: '0' }}"
    :custom-invoice-id="{{ isset($custom_invoice->id) ? $custom_invoice->id: '0'}}"
    :confirmation-id="{{ isset($confirmation->id) ? $confirmation->id : '0' }}"
    inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                {{-- Support flash messaging using URL querystring params --}}
                @include('partials.common.flash-url-message')

                <div class="card card-default">
                    <div class="card-header">
                        @isset($race->id)
                            <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                            <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                        @endif
                        @if (isset($confirmation) || isset($hotel->id))
                            <a href="{{ route('races.hotels.show', [
                                'race' => $race->id,
                                'hotel' => $hotel->id
                                ]) }}">{{ $hotel->name }}</a> /
                        @endif
                        <a href="{{ route('races.hotels.clients.show', [
                            'race' => $race->id,
                            'hotel' => $hotel->id,
                            'client' => $client->id
                            ]) }}">{{ $client->name }}</a> /
                        @if (isset($confirmation))
                            Confirmation #{{ $confirmation->id }}
                        @else
                            Invoice #{{ $custom_invoice->id }}
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-sm-6">
                                <h5>
                                    @if (isset($confirmation))
                                        Confirmation #{{ $confirmation->id }}
                                    @else
                                        Invoice #{{ $custom_invoice->id }}
                                    @endif
                                </h5>
                            </div>
                            <div class="col-sm-6 text-right">
                                @if (isset($confirmation) && empty($confirmation->signed_on))
                                    <form class="rtl float-right ml-3" method="POST"
                                        action="{{ route('races.hotels.clients.confirmations.destroy', [
                                            'race' => $race->id,
                                            'hotel' => $hotel->id,
                                            'client' => $client->id,
                                            'confirmation' => $confirmation->id
                                        ]) }}">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button onclick="return confirm('Deleting is permanent. Are you sure?');"
                                            class="btn btn-secondary btn-sm" dusk="race-archive">
                                            <i class="fa fa-close mr-2"></i> Cancel Confirmation
                                        </button>
                                    </form>
                                    <a href="{{ route('races.hotels.clients.confirmations.edit', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                        ]) }}" class="btn btn-primary btn-sm" data-offline="disabled">
                                        <i class="fa fa-edit mr-2"></i> Edit Confirmation
                                    </a>
                                @elseif (isset($custom_invoice))
                                    <a href="{{ route('races.hotels.clients.invoices.edit', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'custom_invoice' => $custom_invoice->id
                                        ]) }}" class="btn btn-primary btn-sm" data-offline="disabled">
                                        <i class="fa fa-edit mr-2"></i> Edit Invoice
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mt-3
                        @if (isset($confirmation) && ! empty($confirmation->signed_on))
                            is-approved
                        @endif
                        ">
                            <div class="col-sm-2">
                                @isset($confirmation)
                                    <label><strong>Sent On</strong></label> <br>
                                    {{ $confirmation->friendly_sent_on }} <br> <br>

                                    <label><strong>Expires On</strong></label> <br>
                                    {{ $confirmation->friendly_expires_on }} <br> <br>

                                    @if(! empty($confirmation->signed_on))
                                        <label><strong>Signed On</strong></label> <br>
                                        {{ $confirmation->friendly_signed_on }} <br> <br>
                                    @endif

                                    @if(! empty($confirmation->currency))
                                        <label><strong>Currency</strong></label> <br>
                                        {{ $confirmation->currency->name }}
                                        ({{ $confirmation->currency->symbol}})
                                    @endif
                                @endif
                                @isset($custom_invoice->due_on)
                                    <label><strong>Due On</strong></label> <br>
                                    {{ $custom_invoice->friendly_due_on }} <br> <br>

                                    @if(! empty($custom_invoice->currency))
                                        <label><strong>Currency</strong></label> <br>
                                        {{ $custom_invoice->currency->name }}
                                        ({{ $custom_invoice->currency->symbol}})
                                    @endif
                                @endif
                            </div>
                            <div class="col-sm-3 pl-4">
                                <label><strong>Client</strong></label> <br>
                                {{ $client->name }} <br>
                                {{ $client->address }} <br>
                                {{ $client->city }}, {{ $client->region }} {{ $client->postal_code}} <br>
                                @isset($client->country->name)
                                    {{ $client->country->name }} <br>
                                @endif
                                @isset($client->phone)
                                    {{ $client->phone }} <br>
                                @endif
                                @isset($client->website)
                                    {{ $client->website }} <br>
                                @endif
                                @isset($contact_client)
                                    Attn: {{ $contact_client->name }}
                                @endif
                            </div>
                            <div class="col-sm-3 pl-4">
                                @if (isset($hotel->id) || isset($confirmation))
                                    @include('partials.hotels.header-block', [
                                        'with_hotel_name' => true,
                                        'contact_hotel' => $contact_hotel
                                    ])
                                @endif
                            </div>
                            @isset($race->id)
                                <div class="col-sm-3 pl-4">
                                    <label><strong>Race</strong></label> <br>
                                    {{ $race->full_name }} <br>
                                    {{ $race->friendly_start_on }} - {{ $race->friendly_end_on }}
                                </div>
                            @endif
                        </div>
                        <form method="PUT" role="form" action="">
                            @method('PUT')
                            {{ csrf_field() }}

                            @isset($custom_invoice)
                                @include('partials.invoices.custom_invoices.show')
                                {{-- FIXME: Had to put the exchange rate here because the
                                    payable() morphTo return is null,
                                    thus can't create an exchange rate method on Payment class --}}
                                {{--  @include('partials.invoices.common.show-payment-schedule', [
                                    'payments' => $custom_invoice->payments,
                                    'currency' => $custom_invoice->currency,
                                    'exchange_rate' => $custom_invoice->exchange_rate
                                ])  --}}
                                <form method="PUT" role="form" action="">
                                        @method('PUT')
                                        {{ csrf_field() }}
                                @include('partials.invoices.common.edit-payment-schedule', ['payments' => $custom_invoice->payments])
                            @else
                                @include('partials.invoices.confirmations.show')
                                {{-- FIXME: Had to put the exchange rate here because the
                                    payable() morphTo return is null,
                                    thus can't create an exchange rate method on Payment class --}}
                                {{--  @include('partials.invoices.common.show-payment-schedule', [
                                    'payments' => $confirmation->payments,
                                    'currency' => $confirmation->currency,
                                    'exchange_rate' => $confirmation->exchange_rate
                                ])  --}}

                                @include('partials.invoices.common.edit-payment-schedule', ['payments' => $confirmation->payments])
                            @endif
                        </form>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-7 text-right mt-1">
                                @isset($all_client_contact)
                                    <div class="form-group row">
                                        <label class="col-form-group-label col-sm-8 pt-1"><strong>Send to:</strong></label>
                                        <select @change="setClientContactOnChange($event)"
                                            class="form-control-sm form-control col-sm-4"
                                            v-model="confirmation_contact_id">
                                            @foreach ($all_client_contact as $contact)
                                                @if(isset($contact_client)  && !empty($contact_client))
                                                    <option :id={{ $contact->id }} value="{{ $contact->id }}"
                                                        {{ $contact->id == $contact_client->id ? 'selected' : '' }}>
                                                        {{ $contact->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-5 text-right">

                                @if (isset($confirmation) && empty($confirmation->signed_on))

                                    <a href="{{ route('races.hotels.clients.confirmations.request', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                    ]) }}" class="btn btn-info ml-3" data-offline="disabled">
                                        <i class="fa fa-google-wallet mr-2"></i> Send for Signature</a>

                                    <a href="{{ route('races.hotels.clients.confirmations.mark-as-signed', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                    ]) }}" class="btn btn-info ml-3" data-offline="disabled">
                                        <i class="fa fa-google-wallet mr-2"></i> Mark as Signed</a>

                                @elseif (isset($confirmation) && !empty($confirmation->signed_on))

                                    <a href="{{ route('races.hotels.clients.confirmations.mark-as-unsigned', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                    ]) }}" class="btn btn-info ml-3" data-offline="disabled">
                                        <i class="fa fa-undo mr-2"></i> Mark as Unsigned</a>

                                @endif

                                @isset($confirmation)
                                    <a href="{{ route('races.hotels.clients.confirmations.pdf', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                    ]) }}" target="_blank" class="btn btn-default ml-3"><i class="fa fa-download mr-2"></i> PDF</a>

                                @else
                                    <a href="{{ route('races.hotels.clients.invoices.pdf', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'custom_invoice' => $custom_invoice->id
                                    ]) }}" target="_blank" class="btn btn-default ml-3"><i class="fa fa-download mr-2"></i> PDF</a>
                                @endif

                                <button type="submit" class="btn btn-primary ml-3"
                                    @click.prevent="updateConfirmationInvoicePayment()">
                                    <i class="fa fa-save mr-2"></i> Save Payment
                                </button>

                                {{-- @isset($confirmation)
                                    <a href="{{ route('races.hotels.clients.confirmations.send', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'confirmation' => $confirmation->id
                                    ]) }}" class="btn btn-primary ml-3"><i class="fa fa-paper-plane mr-2"></i> Send Copy to Client</a>
                                @else
                                    <a href="{{ route('races.hotels.clients.invoices.send', [
                                        'race' => $race->id,
                                        'hotel' => $hotel->id,
                                        'client' => $client->id,
                                        'custom_invoice' => $custom_invoice->id
                                    ]) }}" class="btn btn-primary ml-3"><i class="fa fa-paper-plane mr-2"></i> Send Copy to Client</a>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</invoices-show>
@endsection
