@extends('spark::layouts.app')

@section('content')
<reservations
    :user="user"
    :race-id="{{ isset($race->id) ? $race->id : '0' }}"
    :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}"
    inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @include('flash::message')
                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                        <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                        <a href="{{ route('races.hotels.show', [
                            'race' => $race->id,
                            'hotel' => $hotel->id,
                        ]) }}">{{ $hotel->name }}</a> /
                        {{__('Rooming List')}}
                    </div>

                    <div class="card-body">
                        <div class="form-row mb-4">
                            <div class="col-sm-12">
                                <h5 class="mb-3">{{__('Rooming List')}}</h5>
                            </div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="col-sm-5 pt-1"><label for="list_sent_on">List Sent On:</label></div>
                                    <div class="col-sm-7 mb-2">
                                        <date-pick name="list_sent_on" v-model="list_sent_on"
                                            :input-attributes="{
                                                name: 'list_sent_on',
                                                class: 'form-date-picker form-control text-center {{ $errors->has('list_sent_on') ? 'is-invalid' : '' }}',
                                                placeholder: 'dd/mm/yyyy',
                                                autocomplete: 'off',
                                            }"
                                            :display-format="'DD/MM/YYYY'"
                                            :start-week-on-sunday="true">
                                        </date-pick>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5 pt-1"><label for="list_confirmed_on">List Confirmed On:</label></div>
                                    <div class="col-sm-7 mb-2">
                                        <date-pick name="list_confirmed_on" v-model="list_confirmed_on"
                                            :input-attributes="{
                                                name: 'list_confirmed_on',
                                                class: 'form-date-picker form-control text-center {{ $errors->has('list_confirmed_on') ? 'is-invalid' : '' }}',
                                                placeholder: 'dd/mm/yyyy',
                                                autocomplete: 'off'
                                            }"
                                            :display-format="'DD/MM/YYYY'"
                                            :start-week-on-sunday="true">
                                        </date-pick>
                                    </div>
                                </div>
                            </div>

                            @include('partials.common.rooming-list-room-type-breakdown')

                            <div class="col-sm-4">
                                <a href="#" class="btn btn-sm btn-primary file-upload float-right">
                                    <i class="fa fa-upload mr-2"></i> Import
                                    <form enctype="multipart/form-data" method="POST" role="form"
                                        action="{{ route('races.hotels.reservations.import', [
                                            'race' => $race->id,
                                            'hotel' => $hotel->id
                                        ]) }}">
                                        {{ method_field('POST') }}
                                        {{ csrf_field() }}
                                        <input class="spark-uploader-control" onchange="this.form.submit()" type="file" name="upload_file" >
                                    </form>
                                </a>
                                <a href="{{ route('races.hotels.reservations.export', [
                                    'race' => $race->id,
                                    'hotel' => $hotel->id
                                    ]) }}" class="btn btn-primary btn-sm float-right mr-3">
                                    <i class="fa fa-download mr-2"></i> Download Sample
                                </a>
                            </div>

                            <div class="col-sm-12 mb-3">
                                <label for="">Notes to Hotel</label>
                                <textarea name="" class="form-control" v-model="list_notes" rows="3"
                                placeholder="Add notes you want to go to the hotel when you export the rooming list here."></textarea>
                            </div>

                            @include('partials.common.rooming-list-legend')
                        </div>

                        {{-- FIXME: Make showing this conditional upon there being signed confirmations --}}
                        @include('partials.common.rooming-listing')
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}"
                                    class="btn btn-default mx-3">Cancel</a>
                                <button @click.prevent="update()" type="button" class="btn btn-primary mx-3" dusk="save-rooming-list">
                                    <i class="fa fa-save mr-2"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</reservations>
@endsection
