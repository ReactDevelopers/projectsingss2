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
                    <div class="table-responsive admin-table-wrapper">
                        {!! $html->table(); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('inlinescript')
    {!! $html->scripts() !!}
@endpush
