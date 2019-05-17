<div>
    <ul class="user-profile-links">
        <li class="resp-tab-item">
            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0678')}}
            </a>
        </li>
        <li class="active">
            <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0679')}}
            </a>
        </li>
        <li>
            <a href="{{url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0680')}}
            </a>
        </li>
        @if(!empty($project->reviews_count))
            <li class="resp-tab-item">
                <a href="{{url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0721')}}
                </a>
            </li>
        @endif
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="shift-up-5px">
            <div>
                <div class="no-table datatable-listing">
                    {!!$html->table()!!}
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
            $('#dataTableBuilder_wrapper thead').remove();
            $('#dataTableBuilder_wrapper .row:first').remove();
            $('#dataTableBuilder').next('.row').remove();
        });
    </script>
@endpush