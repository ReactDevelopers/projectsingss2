@extends('spark::layouts.app')

@section('content')
<hotels-edit :user="user" :race-hotel-id="{{ isset($meta->id) ? $meta->id : 'null' }}" :race-id="{{ isset($race->id) ? $race->id : 'null' }}" :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}"
    inventory-check-in="{{ isset($meta->inventory_min_check_in) ? format_input_date_to_system($meta->inventory_min_check_in) : '' }}"
    inventory-check-out="{{ isset($meta->inventory_min_check_out) ? format_input_date_to_system($meta->inventory_min_check_out) : '' }}"
    race-start-date="{{ isset($race->start_on) ? format_input_date_to_system($race->start_on) : '' }}"
    race-end-date="{{ isset($race->end_on) ? format_input_date_to_system($race->end_on) : '' }}"
    inline-template :hotel="{{$hotel->toJson()}}" :contacts="{{isset($contacts) ? $contacts->toJson(): '[]'}}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @isset($race->id)
                    <form enctype="multipart/form-data" method="POST" role="form"
                        action="{{ route('races.hotels.update', ['race' => $race->id, 'hotel' => $hotel->id]) }}">
                @else
                    <form enctype="multipart/form-data" method="POST" role="form"
                        action="{{ route('hotels.update', ['hotel' => $hotel->id]) }}">
                @endif

                    @method('PUT')
                    {{ csrf_field() }}

                    @include('flash::message')

                    <div class="card card-default">
                        <div class="card-header">
                            @isset($race->id)
                                <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                                <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                                <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}">
                                    {{ $hotel->name }}
                                </a> /
                                Edit Room Types and Rates
                            @else
                                <a href="{{ route('hotels.index') }}">{{__('Hotels')}}</a> /
                                {{ $hotel->name }}
                            @endif
                        </div>

                        <div class="card-body">

                            @isset($race->id)
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        @isset($race->id)
                                            <h5><a href="{{ route('hotels.show', ['hotel' => $hotel->id]) }}">{{ $hotel->name }}</a></h5>
                                        @else
                                            <h5>{{ $hotel->name }}</h5>
                                        @endif
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        @if (! isset($race->id))
                                            <a href="{{ route('hotels.edit', ['hotel' => $hotel->id]) }}" class="btn btn-primary btn-sm" data-offline="disabled">
                                                <i class="fa fa-edit mr-2"></i> Edit Hotel
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-row mt-3">
                                    <div class="col-sm-4">
                                        @include('partials.hotels.header-block', ['contact_hotel' => $contact])
                                    </div>
                                </div>

                                <hr class="my-5">

                                <div class="form-row mb-3">
                                    <div class="col-sm-12">
                                        <h5 class="mb-3">Room Types and Rates</h5>
                                        <div class="row mb-3">
                                            <div class="col-sm-2">
                                                <label><strong>Currency:</strong></label> <br>
                                                <select class="form-control" v-model="inventory_currency_id" name="inventory_currency_id" >
                                                    @foreach ($currencies as $currency)
                                                        @if(isset($meta->inventory_currency_id)  && !empty($meta->inventory_currency_id))
                                                            <option value="{{ $currency->id }}"
                                                                {{ $currency->id == $meta->inventory_currency_id ? ' selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @elseif (isset($race->currency->id))
                                                            <option value="{{ $currency->id }}"
                                                                {{ $currency->id == $race->currency->id ? ' selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Ex: "USD"
                                                </small>
                                            </div>
                                            <div class="col-sm-2">
                                                <label><strong>Minimum Check-in:</strong></label> <br>
                                                <div class="navbar-item" id="date_picker">
                                                    <date-pick name="inventory_min_check_in"
                                                        v-model="inventory_min_check_in"
                                                        :input-attributes="{
                                                        name: 'inventory_min_check_in',
                                                        class: 'form-date-picker form-control {{ $errors->has('inventory_min_check_in') ? 'is-invalid' : '' }}',
                                                        placeholder: 'dd/mm/yyyy',
                                                        autocomplete: 'off'
                                                        }"
                                                        :display-format="'DD/MM/YYYY'"
                                                        :start-week-on-sunday="true">
                                                    </date-pick>
                                                </div>

                                                <span class="invalid-feedback" v-show="form.errors.has('inventory_min_check_in')">
                                                    @{{ form.errors.get('inventory_min_check_in') }}
                                                </span>
                                                <small class="form-text text-muted">
                                                    Ex: "31/12/2020"
                                                </small>
                                            </div>
                                            <div class="col-sm-2">
                                                <label><strong>Minimum Check-out:</strong></label> <br>
                                                <date-pick name="inventory_min_check_out"
                                                    v-model="inventory_min_check_out"
                                                    :input-attributes="{
                                                    name: 'inventory_min_check_out',
                                                    class: 'form-date-picker form-control {{ $errors->has('inventory_min_check_out') ? 'is-invalid' : '' }}',
                                                    placeholder: 'dd/mm/yyyy',
                                                    autocomplete: 'off'
                                                    }"
                                                    :display-format="'DD/MM/YYYY'"
                                                    :start-week-on-sunday="true">
                                                    </date-pick>
                                                <span class="invalid-feedback" v-show="form.errors.has('inventory_min_check_out')">
                                                    @{{ form.errors.get('inventory_min_check_out') }}
                                                </span>
                                                <small class="form-text text-muted">
                                                    Ex: "31/12/2020"
                                                </small>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><strong>Total Nights:</strong></label> <br>
                                                <span v-if="inventory_min_check_in && inventory_min_check_out">
                                                    @{{ inventory_min_check_in_formatted }} - @{{ inventory_min_check_out_formatted }} (@{{ inventory_number_of_nights }} nights)
                                                </span>
                                                <span v-else>N/A</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label><strong>Notes:</strong></label>
                                                <input type="text" class="form-control" placeholder="Add any notes about these rooms here"
                                                    name="inventory_notes" v-model="inventory_notes"
                                                    value="{{ old('inventory_notes', @$meta->inventory_notes) }}">
                                                <small class="form-text text-muted">
                                                    Ex: "Buffet breakfast is additional cost of USD 40 (exclusive 18% VAT)."
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                @include('partials.common.uploads', ['margin_class_remove' => true])
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row mb-4">
                                    <div class="col-sm-12" style="overflow: auto;">
                                        <table class="table table-sm table-striped override-table">
                                            <thead>
                                                <tr>
                                                    <th>Room Type</th>
                                                    <th class="text-right">Min/Nt Hotel (@{{ chosen_currency_symbol }})</th>
                                                    <th class="text-right">Min/Nt Client (@{{ chosen_currency_symbol }})</th>
                                                    <th class="text-right">Booked</th>
                                                    <th class="text-right">P&P/Nt Hotel (@{{ chosen_currency_symbol }})</th>
                                                    <th class="text-right">P&P/Nt Client (@{{ chosen_currency_symbol }})</th>
                                                    <th class="text-right">Booked</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(input, index) in inventory_rows">
                                                    <td>
                                                        <input type="text" class="form-control" value="" name="room_name[]"
                                                        :class="{'is-invalid': form.errors.has(`inventory_rows.${index}.room_name`)}"
                                                        v-model="input.room_name" ref="room_name"
                                                        dusk="room-type-name">
                                                        <span class="invalid-feedback" v-show="form.errors.has(`inventory_rows.${index}.room_name`)">
                                                            @{{ form.errors.get(`inventory_rows.${index}.room_name`) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-10 float-right text-right" value=""
                                                        name="min_night_hotel_rate[]" dusk="min-night-hotel-rate" v-model="input.min_night_hotel_rate"
                                                        :class="{'is-invalid': form.errors.has('inventory_rows.0.min_night_hotel_rate')}"
                                                        ref="min_night_hotel_rate">
                                                        <span class="invalid-feedback" v-show="form.errors.has('inventory_rows.0.min_night_hotel_rate')">
                                                            @{{ form.errors.get('inventory_rows.0.min_night_hotel_rate') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-10 float-right text-right" value=""
                                                            name="min_night_client_rate[]" dusk="min_night_client"
                                                            v-model="input.min_night_client_rate"
                                                            :class="{'is-invalid': form.errors.has('inventory_rows.0.min_night_client_rate')}"
                                                            ref="min_night_client_rate">
                                                        <span class="invalid-feedback" v-show="form.errors.has('inventory_rows.0.min_night_client_rate')">
                                                            @{{ form.errors.get('inventory_rows.0.min_night_client_rate') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-7 float-right text-right" value=""
                                                            name="min_stays_contracted[]" dusk="min_stays_contracted"
                                                            v-model="input.min_stays_contracted"
                                                            :class="{'is-invalid': form.errors.has(`inventory_rows.${index}.min_stays_contracted`)}"
                                                            ref="min_stays_contracted">
                                                        <span class="invalid-feedback" v-show="form.errors.has(`inventory_rows.${index}.min_stays_contracted`)">
                                                            @{{ form.errors.get(`inventory_rows.${index}.min_stays_contracted`) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-10 float-right text-right" value=""
                                                            name="pre_post_night_hotel_rate[]" dusk="pre_post_night_hotel"
                                                            v-model="input.pre_post_night_hotel_rate"
                                                            :class="{'is-invalid': form.errors.has('inventory_rows.0.pre_post_night_hotel_rate')}"
                                                            ref="pre_post_night_hotel_rate">
                                                        <span class="invalid-feedback" v-show="form.errors.has('inventory_rows.0.pre_post_night_hotel_rate')">
                                                            @{{ form.errors.get('inventory_rows.0.pre_post_night_hotel_rate') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-10 float-right text-right" value=""
                                                            name="pre_post_night_client_rate[]" dusk="pre_post_night_client_rate" v-model="input.pre_post_night_client_rate"
                                                            :class="{'is-invalid': form.errors.has('inventory_rows.0.pre_post_night_client_rate')}"
                                                            ref="pre_post_night_client_rate">
                                                        <span class="invalid-feedback" v-show="form.errors.has('inventory_rows.0.pre_post_night_client_rate')">
                                                            @{{ form.errors.get('inventory_rows.0.pre_post_night_client_rate') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" class="form-control col-sm-7 float-right text-right" value=""
                                                            name="pre_post_nights_contracted[]" v-model="input.pre_post_nights_contracted"
                                                            :class="{'is-invalid': form.errors.has(`inventory_rows.${index}.pre_post_nights_contracted`)}"
                                                            ref="pre_post_nights_contracted"
                                                            dusk="pre-post-nights-contracted">
                                                        <span class="invalid-feedback" v-show="form.errors.has(`inventory_rows.${index}.pre_post_nights_contracted`)">
                                                            @{{ form.errors.get(`inventory_rows.${index}.pre_post_nights_contracted`) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="#" @click.prevent="deleteRow(index)"
                                                            class="btn btn-sm btn-danger" data-offline="disabled" dusk="delete-line"><i class="fa fa-close"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-active">
                                                    <td><strong>Totals</strong></td>
                                                    <td class="text-right">
                                                        <strong v-text="formattedMoney(total_min_per_night_hotel, chosen_currency_symbol)"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong v-text="formattedMoney(total_min_per_night_client, chosen_currency_symbol)"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong v-text="total_min_stays_contracted"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong v-text="formattedMoney(total_pre_post_per_night_hotel, chosen_currency_symbol)"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong v-text="formattedMoney(total_pre_post_per_night_client, chosen_currency_symbol)"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong v-text="total_pre_post_nights_contracted"></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="#" class="btn btn-sm btn-primary" data-offline="disabled"
                                                            @click.prevent="addRow" dusk="add-line"><i class="fa fa-plus mr-2"></i> Add Line</a>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            @else
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <h5>{{ $hotel->name }}</h5>
                                    </div>
                                </div>

                                <div class="form-row mt-3">
                                    <div class="col-sm-12">
                                        @include('partials.hotels.form')
                                    </div>

                                    <div class="col-sm-12">
                                        @include('partials.contacts')
                                    </div>
                                </div>

                                {{-- <hr class="my-5">

                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <h5>Notes on Hotel</h5>
                                    </div>
                                </div>

                                <div class="form-row mt-3 mb-5">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="5" placeholder="Add hotel notes here"
                                            name="notes">{{ old('notes', @$hotel->notes) }}</textarea>
                                    </div>
                                </div> --}}
                            @endif

                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    @isset($race->id)
                                        <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}"
                                            class="btn btn-default" dusk="room-type-rate-cancil">Cancel</a>
                                        <button type="submit" class="btn btn-primary mx-3" @click.prevent="update" dusk="room-type-rate-submit">
                                            <i class="fa fa-save mr-2"></i> Save
                                        </button>
                                    @else
                                        <a href="{{ route('hotels.show', ['hotel' => $hotel->id]) }}"
                                            class="btn btn-default" dusk="room-type-rate-cancil">Cancel</a>
                                        <button type="submit" class="btn btn-primary mx-3"  @click.prevent="update" :disabled="form.busy" dusk="hotel-edit-submit">
                                            <i v-if="form.busy" class="fa fa-spinner fa-spin"></i>
                                            <i v-else class="fa fa-save mr-2"></i>
                                            Save
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</hotels-edit>
@endsection
