@extends('spark::layouts.app')

@section('content')
<races-create :user="user" :start-date="'{{ format_input_date_to_system(old('start_on', @$race->start_on)) }}'"
    :end-date="'{{ format_input_date_to_system(old('end_on', @$race->end_on)) }}'" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">

                <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('races.store') }}">
                    {{ csrf_field() }}

                    @include('flash::message')

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="{{ route('races.index') }}">{{__('Races')}}</a> /
                            {{__('Create New Race')}}
                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5>{{__('Create New Race')}}</h5>
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <div class="col-sm-12">
                                    @include('partials.races.form', ['set_default_end_date' => true])
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <a href="{{ route('races.index') }}" class="btn btn-default ml-2" dusk="race-cancel">
                                        {{__('Cancel')}}
                                    </a>
                                    <button type="submit" class="btn btn-primary ml-2" dusk="race-submit">
                                        <i class="fa fa-save mr-2"></i>
                                        {{__('Save')}}
                                    </button>
                                    {{-- <a href="/races/create" class="btn btn-primary ml-2">
                                        <i class="fa fa-save mr-2"></i>
                                        Save and Create Another
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</races-create>
@endsection
