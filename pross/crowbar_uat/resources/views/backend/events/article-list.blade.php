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
    <input type="hidden" id="delete-question-url" value="{{url('administrator/forum/question/delete')}}" />
    <input type="hidden" id="update-status-url" value="{{url('administrator/forum/question/update')}}" />
</section>
@endsection

@push('inlinescript')
    {!! $html->scripts() !!}
    <script type="text/javascript">
    function updateStatus(id_question){
        var url = $('#update-status-url').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_question > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_question: id_question}
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
    function deleteQues(id_question){
        var url = $('#delete-question-url').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_question > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_question: id_question}
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
