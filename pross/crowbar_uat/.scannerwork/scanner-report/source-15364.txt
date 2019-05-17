@extends('layouts.backend.dashboard')

@section('requirejs')
<script type="text/javascript">
    var dataTableInstance = '';
    $(function(){
        dataTableInstance = $('#customer-table').DataTable({
            paging: true,
            searching: true,
            processing: true,
            serverSide: true,
            ajax: "{!! url(sprintf('%s/ajax/emails',$url)) !!}",
            columns : [
                { data: null,"className": 'sno',"orderable": false,"defaultContent": '',"searchable": false},
                { data: 'language', name: 'language' },
                { data: 'alias', name: 'alias' },
                { data: 'subject', name: 'subject' },
                { data: null,"className": 'action',"orderable": false,"defaultContent": '',"searchable": false}
            ],
            order:[
                [1, "ASC"]
            ],
            "columnDefs": [{
                "targets": 0,
                "data": null,
                "render": function (data, type, full, meta) {
                    return parseFloat(meta.row) + parseFloat(1) + parseFloat(meta.settings._iDisplayStart);
                }
            },{
                "targets": 4,
                "data": null,
                "render": function (data) {
                    return '<a href="{{$url}}/emails/'+data.id_email+'/edit" class="badge bg-light-blue">Edit</a>'
                }
            }],
        });
    });
</script>
@endsection

@section('content')
<section class="content">
    <div class="row">
        &nbsp;
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
                        <table id="customer-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Language</th>
                                    <th>Template Name</th>
                                    <th>Email Subject</th>
                                    <th width="10">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection