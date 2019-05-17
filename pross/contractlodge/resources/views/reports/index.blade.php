@extends('spark::layouts.app')

@section('content')
<reports :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        Reports
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <ul>
                                <li>
                                    <a href="{{ route('hotels.bills.index') }}">
                                        {{__('Unpaid Hotel Payments')}}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.confirmations.outstanding') }}">
                                        {{__('Outstanding Client Confirmations')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</reports>
@endsection
