<div class="col-md-12">
    <div class="approved-proposals no-padding">
        <div class="datatable-listing">
            {!! $html->table(); !!}
        </div>
    </div>
</div>
@push('inlinescript')
    <style type="text/css">
        .view-profile-name .last-viewed-icon {
            position: absolute;
            right: 24px;
            margin-top: 8px;
        }
    </style>
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}
@endpush
