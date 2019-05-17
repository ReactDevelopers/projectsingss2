<div class="search-banner">
    <div class="container form-horizontal">
        <div class="form-group">
            <div class="col-md-12">
                <input type="text" autocomplete="off" name="search" placeholder="Search" value="{{ Request::get('search') }}" class="form-control" />
                <button type="button" id="search-list" class="button">Search</button>
            </div>
        </div>
    </div>
</div>
<!-- /.Search Banner Section -->

<!-- Main Content -->
<div class="contentWrapper job-details-section find-jobs-list-section schedule-job-listing">
    <div class="container">
        <div class="col-md-9 job-details-left">
            <div id="parentHorizontalTab">
                {!! ___getmenu('employer-myjobs','<span class="hide">%s</span><ul class="user-profile-links">%s</ul>','active',true,true) !!} 
                <div class="">
                    <div>
                        <div class="no-table datatable-listing shift-up-5px">
                            {!! $html->table(); !!}
                        </div>                
                    </div>
                </div>
            </div>   
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

            $(document).on('keyup click','[name="search"],#search-list',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.search.value = $('[name="search"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
@endpush
