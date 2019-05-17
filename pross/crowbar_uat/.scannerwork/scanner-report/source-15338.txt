@extends('layouts.backend.dashboard')

@section('requirejs')
<script type="text/javascript">

</script>
@endsection

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
    <script type="text/javascript">
    function resolveDispute(id_raise){
        var url = $('#resolve-url').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_raise > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_raise_dispute: id_raise}
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

    function unlinkChat(id_raise){
        var url = $('#unlink-chat').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_raise > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_raise_dispute: id_raise}
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
