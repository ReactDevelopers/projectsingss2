@extends('spark::layouts.app')

@section('content')
<hotels-search :user="user" :race-id={{ $race->id }} inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                @isset($race->id)
                    <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('races.hotels.store', ['race' => $race->id]) }}">
                @else
                    <form enctype="multipart/form-data" method="POST" role="form" action="/hotels/">
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
                                    <h5>{{__('Search for a Hotel')}}</h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-6">
                                    <input type="search" class="form-control" placeholder="Hotel name" dusk="hotel-name-search"
                                        v-model="query" v-on:keyup="autoComplete">

                                    <ul class="list-group" v-if="results.length">
                                        <li class="list-group-item" v-for="result in results">
                                            <a :href="attachHotelToRaceUrl(result.id)" style="display:block;">
                                                <strong>@{{ result.name }}</strong>
                                            </a> <span v-if="result.region">(@{{ result.region }})</span>
                                        </li>
                                    </ul>

                                    <small class="form-text text-muted">
                                        Clicking one of the results will add it to the "{{ $race->full_name }}" race automatically.
                                    </small>
                                </div>

                                <div class="col-sm-6">
                                    OR

                                    <a href="{{ route('races.hotels.create', ['race' => $race->id]) }}" class="btn btn-primary btn-sm ml-3" dusk="add-hotel" data-offline="disabled">
                                        <i class="fa fa-plus mr-2"></i> Create New Hotel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    <a href="{{ route('races.show', ['race' => $race->id]) }}" class="btn btn-default ml-3">
                                        {{__('Cancel')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</hotels-search>
@endsection
