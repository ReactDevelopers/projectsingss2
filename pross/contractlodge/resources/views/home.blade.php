@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container-fluid">
        <!-- Application Dashboard -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">{{__('Application Dashboard')}}</div>

                    <div class="card-body">
                        {{__('Your application\'s dashboard.')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
