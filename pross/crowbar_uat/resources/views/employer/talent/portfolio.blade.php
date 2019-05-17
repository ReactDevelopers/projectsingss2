@include('employer.job.includes.talent-profile-menu',$user)
<div class="payment-tabs job-related-tabs shift-up-5px">
    <div class="datatable-listing find-talent-portfolio">
        <div class="no-table">
            {!! $html->table(); !!}
        </div>
    </div>
</div>
@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.fancybox.js') }}"></script>
    {!! $html->scripts() !!}
    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper > .row:first').remove();
            $('#dataTableBuilder_wrapper thead').remove();
        });
        $('.fancybox').fancybox({
            openEffect  : 'elastic',
            closeEffect : 'elastic',
            closeBtn    : true,
            helpers : {
                title : {type : 'inside'},
                buttons : {},
                overlay : {closeClick: false}
            }
        });
    </script>
@endpush
