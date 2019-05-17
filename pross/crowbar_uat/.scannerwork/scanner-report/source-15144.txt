<div class="proposals-listing-section">
    {!! ___getmenu('talent-proposal-menu','%s<ul class="user-profile-links">%s</ul>','active',true,false) !!} 
    <div class="clearfix"></div>
    <div class="currentJobOne-section accepted-proposals-listing">
        <div class="datatable-listing no-padding-cell shift-up-5px">
            {!! $html->table(); !!}
        </div>
    </div>
</div>   

@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper .row:first').remove();
    	});
    </script>
@endpush
