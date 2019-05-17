@extends('spark::layouts.app')

@section('content')
<races-edit :user="user" :start-date="'{{ format_input_date_to_system(old('start_on', @$race->start_on)) }}'"
    :end-date="'{{ format_input_date_to_system(old('end_on', @$race->end_on)) }}'" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form enctype="multipart/form-data" method="POST" role="form"
                    action="{{ route('races.update', ['race' => $race->id]) }}">

                    @method('PUT')
                    {{ csrf_field() }}


                    @include('flash::message')

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="{{ route('races.index') }}" title="{{__('Races')}}">{{__('Races')}}</a> /
                            {{__('Edit Race')}}
                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5>{{__('Edit Race')}}</h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-12">
                                    @include('partials.races.form')
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6 text-right">
                                    <a href="{{ route('races.show', ['race' => $race->id]) }}"
                                        class="btn btn-default ml-2" dusk="race-edit-cancel">
                                        {{__('Cancel')}}
                                    </a>
                                    <button type="submit" class="btn btn-primary ml-2" dusk="race-edit-submit">
                                        <i class="fa fa-save mr-2"></i>
                                        {{__('Save')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</races-edit>
@endsection
