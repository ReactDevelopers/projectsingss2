@extends('spark::layouts.app')

@section('content')
<invoices-edit
    :user="user"
    :race-id="{{ isset($race->id) ? $race->id : '0' }}"
    :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}"
    :race-hotel-id="{{ isset($meta->id) ? $meta->id : '0' }}"
    :inventory-currency-id="{{ isset($meta->inventory_currency_id) ? $meta->inventory_currency_id: '0' }}"
    :client-id="{{ isset($client->id) ? $client->id : '0' }}"
    :custom-invoice-id="{{ isset($custom_invoice->id) ? $custom_invoice->id: '0'}}"
    :confirmation-id="{{ isset($confirmation->id) ? $confirmation->id : '0' }}"
    :invoice-id="{{ isset($confirmation->id) ? $confirmation->id : $custom_invoice->id }}"
    invoice-type="{{ isset($confirmation->id) ? 'confirmations' : 'extras' }}"
    inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <form enctype="multipart/form-data" method="PUT" role="form" action="">

                    @method('PUT')
                    {{ csrf_field() }}

                    <div class="card card-default">
                        <div class="card-header">
                            @isset($race->id)
                                <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                                <a href="{{ route('races.show', ['race' => $race->id]) }}">
                                    {{ $race->full_name }}
                                </a> /
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
                                Edit Confirmation #{{ $confirmation->id }}
                            @else
                                Edit Invoice #{{ $custom_invoice->id }}
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5>
                                        @if (isset($confirmation))
                                            Edit Confirmation #{{ $confirmation->id }}
                                        @else
                                            Edit Invoice #{{ $custom_invoice->id }}
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-sm-2">
                                    @if (isset($confirmation))
                                        <label><strong>Expires On</strong></label> <br>
                                    @else
                                        <label><strong>Due On</strong></label> <br>
                                    @endif
                                    <div class="navbar-item" id="date_picker">
                                        <date-pick name="due_on"
                                            v-model="due_on"
                                            :input-attributes="{
                                                name: 'due_on',
                                                class: {'form-date-picker form-control text-center': !form.errors.has('due_on') , 'form-date-picker form-control text-center is-invalid': form.errors.has('due_on')},
                                                placeholder: 'dd/mm/yyyy',
                                                autocomplete: 'off'
                                            }"
                                            :display-format="'DD/MM/YYYY'"
                                            :start-week-on-sunday="true">
                                        </date-pick>
                                    </div>
                                    <span class="invalid-feedback" v-show="form.errors.has('due_on')">
                                        @{{ form.errors.get('due_on') }}
                                    </span>
                                    <small class="form-text text-muted">
                                        Ex: "31/12/2019"
                                    </small>

                                    <label for="currency" class="mt-2"><strong>Currency</strong></label>
                                    <template v-if="typeof currency !== undefined">
                                        <select @change="setExchangedRates()" :class="{'form-exchange-rate form-control': !form.errors.has('currency_id') , 'form-exchange-rate form-control is-invalid': form.errors.has('currency_id')}"
                                            v-model="currency" name="currency">
                                            <option v-for="c in currencies" :value="c">@{{ c.name }}</option>
                                        </select>
                                    </template>
                                    <small class="form-text text-muted">Ex: "EUR"</small>
                                </div>
                                <div class="col-sm-3 pl-4">
                                    <label>
                                        <strong>Client</strong>
                                        <span v-if="client.id" class="ml-2">
                                            (<a href="#" @click.prevent="destroyClient">reset</a>)
                                        </span>
                                    </label> <br>

                                    <div v-if="client.id">
                                        <input type="hidden" name="client.id" v-model="client.id">
                                        <span v-text="client.name"></span> <br>
                                        <template v-if="client.address">
                                            <span v-text="client.address">,</span> <br>
                                        </template>

                                        <template v-if="client.city">
                                            <span v-text="client.city"></span>,
                                        </template>

                                        <template v-if="client.region">
                                            <span v-text="client.region"></span>
                                        </template>

                                        <template v-if="client.postal_code">
                                            <span v-text="client.postal_code"></span> <br>
                                        </template>

                                        <template v-if="client.country === null">
                                        </template>
                                        <template v-else>
                                            <span v-text="client.country.name"></span> <br>
                                        </template>

                                        <template v-if="client.phone">
                                            <span v-text="client.phone"></span> <br>
                                        </template>

                                        <template v-if="client.website">
                                            <span v-text="client.website"></span> <br>
                                        </template>

                                        <template v-if="client.contacts && client.contacts[0]">
                                            Attn: <span v-text="client.contacts[0].name"></span>
                                        </template>
                                    </div>
                                    <div v-else>
                                        <input type="search" class="form-control col-sm-10" placeholder="Client name search"
                                            v-model="query" v-on:keyup="autoComplete"
                                            :class="{'is-invalid': form.errors.has('client_id')}">
                                        <ul class="list-group col-sm-11" v-if="results.length">
                                            <li class="list-group-item" v-for="result in results">
                                                <a href="#" @click.prevent="setClient(result)" style="display:block;">
                                                    <strong>@{{ result.name }}</strong>
                                                </a>
                                            </li>
                                        </ul>
                                        <span class="invalid-feedback" v-show="form.errors.has('client_id')">
                                            @{{ form.errors.get('client_id') }}
                                        </span>
                                        <small class="form-text text-muted">
                                            Ex: "Mercedes" (or <a href="#" data-toggle="modal" data-toggle="modal" :data-target="`#clientModal`">Create a client here</a>)
                                        </small>
                                        @include('partials.invoices.common.overlay-client')
                                    </div>
                                </div>
                                <div class="col-sm-3 pl-4">
                                    @if (isset($hotel->id) || isset($confirmation))
                                        @include('partials.hotels.header-block', ['with_hotel_name' => true, 'contact_hotel' => $contact_hotel])
                                    @else
                                        <label><strong>Hotel</strong></label> <br>
                                        <input type="text" class="form-control" placeholder="Hotel search">
                                        <small class="form-text text-muted">
                                            Ex: "Four Seasons"
                                        </small>
                                    @endif
                                </div>
                                @isset($race->id)
                                    <div class="col-sm-3 pl-4">
                                        <label><strong>Race</strong></label> <br>
                                        {{ $race->full_name }} <br>
                                        {{ $race->friendly_start_on }} - {{ $race->friendly_end_on }}
                                    </div>
                                @else
                                    <div class="col-sm-3 pl-4">
                                        <label><strong>Race</strong></label> <br>
                                        <input type="text" class="form-control" placeholder="Race search">
                                        <small class="form-text text-muted">
                                            Ex: "2019 US Grand Prix"
                                        </small>
                                    </div>
                                @endif
                            </div>

                            @if (isset($custom_invoice))
                                @include('partials.invoices.custom_invoices.table')
                                @include('partials.common.uploads')
                                @include('partials.invoices.common.edit-payment-schedule', ['payments' => $custom_invoice->payments])
                            @else {{-- thus... $invoice_type == 'confirmations' --}}
                                @include('partials.invoices.confirmations.table')
                                @include('partials.common.uploads')
                                @include('partials.invoices.common.edit-payment-schedule', ['payments' => $confirmation->payments])
                            @endif
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    @if (isset($race->id) && isset($hotel->id) && isset($client->id))
                                        @if (isset($confirmation->id))
                                            <a href="{{ route('races.hotels.clients.confirmations.show', [
                                                'race' => $race->id,
                                                'hotel' => $hotel->id,
                                                'client' => $client->id,
                                                'confirmation' => $confirmation->id,
                                                ]) }}" class="btn btn-default mx-3">Cancel</a>
                                        @elseif (isset($custom_invoice->id))
                                            <a href="{{ route('races.hotels.clients.invoices.show', [
                                                'race' => $race->id,
                                                'hotel' => $hotel->id,
                                                'client' => $client->id,
                                                'invoices' => $custom_invoice->id,
                                                ]) }}" class="btn btn-default mx-3">Cancel</a>
                                        @endif
                                        <button type="submit" class="btn btn-primary"
                                            @click.prevent="updateConfirmationInvoicePayment()">
                                            <i class="fa fa-save mr-2"></i> Save
                                        </button>
                                    @elseif (isset($race->id) && isset($client->id))
                                        <a href="/races/1/clients/1" class="btn btn-default mx-3">Cancel</a>
                                        {{-- <a href="/races/1/clients/1/invoices/1?msg=Sent&invoice_type={{ $invoice_type }}" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save</a> --}}
                                    @elseif (isset($race->id))
                                        <a href="/races/1" class="btn btn-default mx-3">Cancel</a>
                                        {{-- <a href="/races/1/invoices/1?msg=Sent&invoice_type={{ $invoice_type }}" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save</a> --}}
                                    @elseif (isset($client->id))
                                        <a href="/clients/1" class="btn btn-default mx-3">Cancel</a>
                                        {{-- <a href="/clients/1/invoices/1?msg=Sent&invoice_type={{ $invoice_type }}" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save</a> --}}
                                    @else
                                        <a href="/invoices" class="btn btn-default mx-3">Cancel</a>
                                        {{-- <a href="/invoices/1?msg=Sent&invoice_type={{ $invoice_type }}" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save</a> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</invoices-edit>
@endsection
