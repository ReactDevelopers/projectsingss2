<div class="contentWrapper job-details-section find-jobs-list-section proposals-listing-section">
    <div class="container">
        <div class="row mainContentWrapper">
            <div class="col-md-9 job-details-left">
                <h2 class="form-heading">{{trans('job.J0098')}}</h2>
                <div id="parentHorizontalTab">
                    {!! ___getmenu('talent-proposal-menu','%s<ul class="user-profile-links">%s</ul>','active',true,false) !!} 
                    <div class="clearfix"></div>
                    <div class="currentJobOne-section">
                        <div class="datatable-listing no-padding-cell shift-up-5px">
                            {!! $html->table(); !!}
                        </div>
                    </div>
                </div>    
            </div>
            <div class="col-md-3 job-details-right">
                @includeIf('talent.includes.my-profile-percentage')
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
    	});
    </script>
@endpush
