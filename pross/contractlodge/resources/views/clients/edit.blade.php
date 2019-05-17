@extends('spark::layouts.app')

@section('content')
<clients-edit :user="user" :client-id="{{ isset($client->id) ? $client->id : '0' }}" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form enctype="multipart/form-data" method="POST"
                    role="form" action="{{ route('clients.update', ['client' => $client->id]) }}">

                    @method('PUT')
                    {{ csrf_field() }}

                    @include('flash::message')

                    <div class="card card-default">
                        <div class="card-header">
                            <a href="{{ route('clients.index') }}">{{__('Clients')}}</a> /
                            {{ $client->name }}
                        </div>

                        <div class="card-body">

                            <div class="form-row">
                                <div class="col-sm-6">
                                    <h5>{{ $client->name }}</h5>
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
                                    <a href="{{ route('clients.show', ['client' => $client->id]) }}"
                                        class="btn btn-default">Cancel</a>
                                    <button type="button" class="btn btn-primary ml-2" dusk="client-submit"
                                    @click.prevent="update()">
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
</clients-edit>
@endsection
