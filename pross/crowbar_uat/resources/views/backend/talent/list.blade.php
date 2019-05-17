@extends('layouts.backend.dashboard')

@section('content')
<section class="content">
    @if($add_user)
        <div class="row">
            <div class="col-md-12 margin-bottom ">
                <button class=" btn btn-info" id="export-excel" title="Export">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </button>
                <span class="pull-right">
                    <a href="{{url(sprintf('%s/users/%s/add',ADMIN_FOLDER,$page))}}" class="btn btn-app" style="height: 40px; padding: 10px; margin: 0px;">
                        <i class="fa fa-plus-circle pull-left"></i> Add New
                    </a>
                </span>
            </div>
        </div>
    @endif
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
</section>
<div class="modal" id="hire-me" >
    <div >
    </div>
</div>
@endsection

@push('inlinescript')
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $('#export-excel').click(function(){
            var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
            params.download = 'csv';
            params.length = {{$userscount}};
            var paramsQuery = $.param(params);
            window.location.href= '{!!url('administrator/users/'.$page)!!}'+'?'+paramsQuery;          
        });
    </script>
@endpush
