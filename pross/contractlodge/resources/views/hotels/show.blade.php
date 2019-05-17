@extends('spark::layouts.app')

@section('content')
<hotels-show :user="user" :race-hotel-id="{{ isset($meta->id) ? $meta->id : 'null' }}" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                {{-- Support flash messaging using URL querystring params --}}
                @include('partials.common.flash-url-message')

                <div class="card card-default">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                @isset($race->id)
                                    <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                                    <a href="{{ route('races.show', ['race' => $race->id]) }}">{{ $race->full_name }}</a> /
                                @else
                                    <a href="{{ route('hotels.index') }}">{{__('Hotels')}}</a> /
                                @endif
                                {{ $hotel->name }}
                            </div>
                            @isset($race->id)
                                <div class="col-sm-6 text-right">
                                    @if(isset($rooming_list_guests) && ! $rooming_list_guests->isEmpty())
                                        <a href="{{ route('races.hotels.reconcile', ['race' => $race->id, 'hotel' => $hotel->id]) }}"
                                            class="btn btn-primary btn-sm ml-3 float-right">
                                            <i class="fa fa-check-circle mr-2"></i> Reconcile
                                        </a>
                                    @endif
                                    <form class="rtl float-right ml-3" method="POST"
                                        action="{{ route('races.hotels.destroy', ['race' => $race->id, 'hotel' => $hotel->id]) }}">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button onclick="return confirm('This will be a permanent action. Data will not be recoverable. Are you sure?');"
                                            class="btn btn-secondary btn-sm" dusk="race-archive">
                                            <i class="fa fa-close mr-2"></i> Remove Hotel from Race
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-row mb-4">
                            <div class="col-sm-6">
                                @isset($race->id)
                                    <h5><a href="{{ route('hotels.show', ['hotel' => $hotel->id]) }}">{{ $hotel->name }}</a></h5>
                                @else
                                    <h5>{{ $hotel->name }}</h5>
                                @endif
                            </div>
                            <div class="col-sm-6 text-right">
                                @if (! isset($race->id))
                                    <a href="{{ route('hotels.edit', ['hotel' => $hotel->id]) }}"
                                        class="btn btn-primary btn-sm" dusk="hotel-edit" data-offline="disabled">
                                        <i class="fa fa-edit mr-2"></i> Edit Hotel
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="col-sm-3">
                                @include('partials.hotels.header-block', ['contact_hotel' => $contact])
                            </div>
                            <div class="col-sm-9">
                                @isset($hotel->notes)
                                    <label><strong>Notes on Hotel</strong></label> <br>
                                    {{ $hotel->notes }}
                                @endif
                            </div>
                        </div>

                        @isset($race->id)
                            @include('partials.hotels.show.with-race.room-types-and-rates')
                            @include('partials.hotels.show.with-race.client-breakdown')
                            @include('partials.hotels.show.with-race.hotel-payments')
                            {{-- FIXME: Make showing this (below) conditional upon there being signed confirmations --}}
                            @include('partials.hotels.show.with-race.rooming-list')
                        @else
                            @include('partials.hotels.show.without-race')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</hotels-show>
@endsection
