@extends('spark::layouts.app')

@section('content')

<hotels-create :user="user" inline-template action="{{isset($race_id) ? route('races.hotels.store', ['race' => $race_id]) : route('hotels.store')}}">

    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                @isset($race_id)
                    <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('races.hotels.store', ['race' => $race_id]) }}">
                @else
                    <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('hotels.store') }}">
                @endif

                    {{ csrf_field() }}

                    @include('flash::message')

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="{{ route('hotels.index') }}">{{__('Hotels')}}</a> /
                            @isset($race)
                                <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                            @endif
                            {{__('Add Hotel')}}
                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5>
                                        {{__('Add Hotel')}}
                                        @isset($race)
                                            to {{ $race->full_name }}
                                        @endif
                                    </h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-12">
                                    @include('partials.hotels.form')
                                </div>

                                <div class="col-sm-12">
                                    @include('partials.contacts', ['title' => 'Hotel Contacts'])
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    @isset($race)
                                        <a href="{{ route('races.show', ['race' => $race->id]) }}" class="btn btn-default ml-2" dusk="hotel-cancel">
                                            {{__('Cancel')}}
                                        </a>
                                    @else
                                        <a href="{{ route('hotels.index') }}" class="btn btn-default ml-2" dusk="hotel-cancel">
                                            {{__('Cancel')}}
                                        </a>
                                    @endif
                                    <button type="submit" class="btn btn-primary ml-2" dusk="hotel-submit" @click.prevent="register" :disabled="form.busy">
                                        <i v-if="form.busy" class="fa fa-spinner fa-spin"></i>
                                        <i v-else class="fa fa-save mr-2"></i>
                                        {{__('Save')}}
                                    </button>
                                    {{-- @isset($race_id)
                                        <a href="/races/1" class="btn btn-default">Cancel</a>
                                        <a href="/races/1/hotels/1" class="btn btn-primary mx-3"><i class="fa fa-save mr-2"></i> Save</a>
                                        <a href="/races/1/hotels/create" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save and Create Another</a>
                                    @else
                                        <a href="/hotels" class="btn btn-default">Cancel</a>
                                        <a href="/hotels/1" class="btn btn-primary mx-3"><i class="fa fa-save mr-2"></i> Save</a>
                                        <a href="/hotels/create" class="btn btn-primary"><i class="fa fa-save mr-2"></i> Save and Create Another</a>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</hotels-create>
@endsection
