@extends('spark::layouts.app')

@section('content')
<invoices-create
    :user="user"
    :race-id="{{ isset($race->id) ? $race->id : '0' }}"
    :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}"
    :client-id="{{ isset($client->id) ? $client->id : '0' }}"
    :race-hotel-id="{{ isset($meta->id) ? $meta->id: '0' }}"
    :inventory-currency-id="{{ isset($meta->inventory_currency_id) ? $meta->inventory_currency_id: '0' }}"
    invoice-type="{{ $invoice_type }}"
    :race="{{ $race->toJson() }}"
    inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <form enctype="multipart/form-data" method="POST" role="form" action="">

                    @method('POST')
                    {{ csrf_field() }}
                    <div class="card card-default">
                        <div class="card-header">
                            @isset($race->id)
                                <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                                <a href="{{ route('races.show', ['race' => $race->id]) }}">
                                    {{ $race->full_name }}
                                </a> /
                            @endif
                            @isset($hotel->id)
                                <a href="{{ route('races.hotels.show', [
                                    'race' => $race->id,
                                    'hotel' => $hotel->id
                                    ]) }}">{{ $hotel->name }}</a> /
                            @endif
                            @isset($client->id)
                                <a href="{{ route('races.hotels.clients.show', [
                                    'race' => $race->id,
                                    'hotel' => $hotel->id,
                                    'client' => $client->id
                                    ]) }}">{{ $client->name }}</a> /
                            @endif
                            @if ($invoice_type == 'confirmations')
                                Create a new Confirmation
                            @else
                                Create a New Invoice
                            @endif
                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    @if ($invoice_type == 'confirmations')
                                        <h5>Create a New Confirmation</h5>
                                    @else
                                        <h5>Create a New Invoice</h5>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-2">
                                    @if ($invoice_type == 'confirmations')
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
                                    <small class="form-text text-muted">Ex: "31/12/2019"</small>

                                    <label for="currency" class="mt-2"><strong>Currency</strong></label>
                                    <template v-if="typeof currency !== undefined">

                                        <select @change="setExchangedRates()" class="form-exchange-rate form-control"
                                            v-model="currency" name="currency" dusk="currency">
                                            <option v-for="c in currencies" :value="c" :dusk="'curr_'+c.id" >@{{ c.name }}</option>

                                        </select>
                                    </template>
                                    <span class="invalid-feedback" v-show="form.errors.has('currency_id')">
                                        @{{ form.errors.get('currency_id') }}
                                    </span>
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
                                        <input type="hidden" name="client.id" v-model="client.id" dusk="client-name-search">
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
                                        <input type="search" class="form-control col-sm-10" placeholder="Client name search" dusk="client-name-search"
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
                                    <label>
                                        <strong>Hotel</strong>
                                        @if (isset($race->id) && !isset($hotel->id) )
                                            <span v-if="hotel.id" class="ml-2">
                                                (<a href="#" @click.prevent="destroyHotel">reset</a>)
                                            </span>
                                        @endif
                                    </label><br>
                                    @isset($hotel->id)
                                        @include('partials.hotels.header-block', ['with_hotel_name' => true, 'contact_hotel' => $hotel->contact])
                                    @else
                                        <div v-if="hotel.id">
                                            <input type="hidden" name="hotel.id" v-model="hotel.id" dusk="hotel-name-search">
                                            <span v-text="hotel.name"></span> <br>
                                            <span v-text="hotel.address"></span>, <span v-text="hotel.city"></span>,
                                                <span v-text="hotel.region"></span> <span v-text="hotel.postal_code"></span> <br>
                                            <template v-if="hotel.country.name">
                                                <span v-text="hotel.country.name"></span> <br>
                                            </template>
                                            <template v-if="hotel.phone">
                                                <span v-text="hotel.phone"></span> <br>
                                            </template>
                                            <template v-if="hotel.website">
                                                <span v-text="hotel.website"></span> <br>
                                            </template>
                                            <span class="invalid-feedback" v-show="form.errors.has('hotel_id')">
                                                @{{ form.errors.get('hotel_id') }}
                                            </span>
                                        </div>
                                        <div v-else>
                                            {{--  <input type="text" class="form-control" placeholder="Hotel search" dusk="hotel-name-search">  --}}
                                            <input type="search" class="form-control {'is-invalid': form.errors.has('hotel_id')}" placeholder="Hotel search" dusk="hotel-name-search"
                                            v-model="hotel_query" v-on:keyup="autoCompleteHotel">
                                    
                                            <ul class="list-group col-sm-11" v-if="hotels.length">
                                                <li class="list-group-item" v-for="hotel in hotels">
                                                    <a href="#" @click.prevent="setHotel(hotel)" style="display:block;">
                                                        <strong>@{{ hotel.name }}</strong>
                                                    </a>
                                                </li>
                                            </ul>
                                            <span class="invalid-feedback" v-show="form.errors.has('hotel_id')">
                                                @{{ form.errors.get('hotel_id') }}
                                            </span>
                                            <small class="form-text text-muted">
                                                Ex: "Four Seasons"
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-sm-3 pl-4">
                                    <label>
                                        <strong>Race</strong>
                                        @if (!isset($hotel->id) )
                                            <span v-if="race.id" class="ml-2">
                                                (<a href="#" dusk="race-link-reset" @click.prevent="destroyRace">reset</a>)
                                            </span>
                                        @endif
                                    </label> <br>
                                    @if (isset($race->id) && isset($hotel->id))
                                        {{ $race->full_name }} <br>
                                        {{ $race->friendly_start_on }} - {{ $race->friendly_end_on }}
                                    @else
                                        <div v-if="race.id">
                                            <input type="hidden" name="race.id" v-model="race.id" dusk="race-name-search">
                                            <span v-text="race.full_name"></span> <br>
                                            <span v-text="race.friendly_start_on"></span> - <span v-text="race.friendly_end_on"></span> <br>
                                        </div>
                                        <div v-else>
                                            {{--  <input type="text" class="form-control" placeholder="Race search">  --}}
                                            <input type="search" class="form-control" placeholder="Race search" dusk="race-name-search"
                                            v-model="race_query" v-on:keyup="autoCompleteRace">
                                            <ul class="list-group col-sm-11" v-if="races.length">
                                                <li class="list-group-item" v-for="race in races">
                                                    <a href="#" dusk="race-link" @click.prevent="setRace(race)" style="display:block;">
                                                        <strong>@{{ race.full_name }}</strong>
                                                    </a>
                                                </li>
                                            </ul>
                                            <span class="invalid-feedback" v-show="form.errors.has('race_id')">
                                                @{{ form.errors.get('race_id') }}
                                            </span>

                                            <small class="form-text text-muted">
                                                Ex: "2019 US Grand Prix"
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($invoice_type == 'extras')
                                @include('partials.invoices.custom_invoices.table')
                            @else {{-- thus... $invoice_type == 'confirmations' --}}
                                @include('partials.invoices.confirmations.table')
                            @endif

                            @include('partials.invoices.common.edit-payment-schedule')
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    @if (isset($race->id) && isset($hotel->id) && isset($client->id))
                                        <a href="{{ route('races.hotels.clients.show', [
                                            'race' => $race->id,
                                            'hotel' => $hotel->id,
                                            'client' => $client->id
                                            ]) }}" class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @elseif (isset($race->id) && isset($client->id))
                                        <a href="{{ route('races.clients.show', [
                                            'race' => $race->id,
                                            'client' => $client->id
                                            ]) }}" class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @elseif (isset($race->id) && isset($hotel->id))
                                        <a href="{{ route('races.hotels.show', [
                                            'race' => $race->id,
                                            'hotel' => $hotel->id
                                            ]) }}" class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @elseif (isset($race->id))
                                    <a href="{{ route('races.show', [
                                        'race' => $race->id
                                        ]) }}" class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @elseif (isset($client->id))
                                        <a href="{{ route('clients.show', [
                                            'client' => $client->id
                                            ]) }}" class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @else
                                        <a href="{{ route('invoices.index') }}"
                                            class="btn btn-default mx-3" dusk="cancel-race-invoice">Cancel</a>
                                    @endif
                                    <button type="submit" dusk="create-invoice" class="btn btn-primary"
                                        @click.prevent="createInvoice()">
                                        <i class="fa fa-save mr-2" dusk="submit-invoice"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</invoices-create>
@endsection
