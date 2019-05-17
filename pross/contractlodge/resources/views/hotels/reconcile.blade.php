@extends('spark::layouts.app')

@section('content')
<hotels-reconcile :user="user" inline-template>
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
                                <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}">{{ $hotel->name }}</a> /
                                Reconcile
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('partials.hotels.reconcile')
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <div class="card-footer fixed-bottom button-sticky-footer">
                <div class="row">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}" class="btn btn-default">Cancel</a>
                        <button type="button" class="btn btn-secondary ml-3" dusk="races-hotels-reconcile-save">
                            <i class="fa fa-save mr-2"></i> Save
                        </button>
                        <button type="button" class="btn btn-primary ml-3" dusk="races-hotels-reconcile-generate-invoices">
                            <i class="fa fa-clone mr-2"></i> Generate Invoices
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</hotels-reconcile>
@endsection
