@extends('spark::layouts.app')

@section('content')
<clients-create :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('clients.store') }}">

                {{ csrf_field() }}

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('clients.index') }}">{{__('Clients')}}</a> /
                        {{__('Add Client')}}
                    </div>

                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-sm-6">
                                <h5>{{__('Add Client')}}</h5>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            @include('partials.clients.form')
                        </div>
                        @include('partials.contacts')
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6 text-right">
                                @isset($race)
                                    <a href="{{ route('clients.show', ['client' => $client->id]) }}" class="btn btn-default ml-2">
                                        {{__('Cancel')}}
                                    </a>
                                @else
                                    <a href="{{ route('clients.index') }}" class="btn btn-default ml-2">
                                        {{__('Cancel')}}
                                    </a>
                                @endif
                                <button type="button" class="btn btn-primary ml-2" dusk="client-submit" @click.prevent="create()" :disabled="form.busy">
                                    <i class="fa fa-save mr-2"></i>
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
            </div>
        </div>
    </div>
</clients-create>
@endsection
