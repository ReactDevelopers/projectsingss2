@extends('layouts.backend.dashboard')
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link rel="stylesheet" href="{{ asset("/backend/plugins/datatables/dataTables.bootstrap.css")}}">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset ("/backend/plugins/datatables/jquery.dataTables.min.js") }}"></script>
        <script src="{{ asset ("/backend/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
        <script src="{{ asset ("/backend/js/datatable.js") }}"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel">
                        <div class="panel-body">
                            <table class="table table-striped" data-request="datatable-auto" data-url='{!! url(sprintf("/%s/%s",ADMIN_FOLDER,"templates/show")) !!}' data-number="4" data-primary="id" data-manager="templates" data-buttons="view" data-columns='[{"className":"sno","orderable": false,"data":null,"defaultContent":"","searchable": false},{"data":"alias","name":"alias"},{"data":"subject","name":"subject"},{"data":"status","name":"status"},{"className": "action","orderable": false,"data": null,"defaultContent":"","searchable": false,"orderable": false}]'>
                                <thead>
                                    <tr>
                                        <th width="1%">#</th>
                                        <th>Template Name</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
    