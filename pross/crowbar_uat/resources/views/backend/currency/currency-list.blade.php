@extends('layouts.backend.dashboard')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12 margin-bottom">
            <span class="pull-right">
                <a href="{{url('administrator/currency/add')}}" class="btn btn-app" style="height: 40px; padding: 10px; margin: 0px;">
                    <i class="fa fa-plus-circle pull-left"></i> Add New
                </a>
            </span>
        </div>
    </div>
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
    <input type="hidden" id="delete-currency-url" value="{{url('administrator/currency/delete')}}" />
    <input type="hidden" id="update-currency-url" value="{{url('administrator/currency/update_currency_status')}}" />
</section>
@endsection

@push('inlinescript')
    {!! $html->scripts() !!}
    <script type="text/javascript">
    function deleteCurrency(id_curr){
        var url = $('#delete-currency-url').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_curr > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_curr: id_curr}
            })
            .done(function(data) {
                LaravelDataTables["dataTableBuilder"].draw();
                swal({
                    title: '',
                    html: data.message,
                    showLoaderOnConfirm: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick:false,
                    confirmButtonText: 'Okay',
                    cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                    confirmButtonColor: '#0FA1A8',
                    cancelButtonColor: '#CFCFCF'
                });
            });
        }
    }

    function updateStatus(id_curr){
        var url = $('#update-currency-url').val();
        // var isconfirm = confirm('Do you really want to continue with this action?');

        if(id_curr > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_curr: id_curr}
            })
            .done(function(data) {
                LaravelDataTables["dataTableBuilder"].draw();
                swal({
                    title: '',
                    html: data.message,
                    showLoaderOnConfirm: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick:false,
                    confirmButtonText: 'Okay',
                    cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                    confirmButtonColor: '#0FA1A8',
                    cancelButtonColor: '#CFCFCF'
                });
            });
        }
    }
    </script>
@endpush