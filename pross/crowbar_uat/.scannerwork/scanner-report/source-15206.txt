@extends('layouts.backend.dashboard')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        {!! $html->table(); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="resolve-url" value="{{url('administrator/resolve-raise-dispute')}}" />
    <input type="hidden" id="unlink-chat" value="{{url('administrator/unlink-chat')}}" />
</section>
@endsection
@push('inlinescript')
    {!! $html->scripts() !!}
@endpush
