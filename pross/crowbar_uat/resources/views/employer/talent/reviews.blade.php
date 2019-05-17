@include('employer.job.includes.talent-profile-menu',$user)
<div class="payment-tabs job-related-tabs shift-up-5px">
    <div class="datatable-listing"> 
        <div class="no-table"> 
            {!! $html->table() !!}
        </div>
    </div>
</div>

@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}
    
    <script type="text/javascript">
        $(function(){
            $('.no-table thead').remove();
            $('#dataTableBuilder_wrapper > .row:first').remove();
            $('#dataTableBuilder_wrapper thead').remove();
        });
    </script>
@endpush
