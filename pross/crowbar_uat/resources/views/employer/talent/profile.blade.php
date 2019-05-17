@include('employer.job.includes.talent-profile-menu')
<div class="login-inner-wrapper profile-info-details">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0030') }} 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                @if(!empty(array_column($talent['industry'],'name')))
                    {!! ___tags(array_column($talent['industry'],'name'),'<span class="small-tags">%s</span>','') !!}
                @else
                    {{ N_A }}
                @endif
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0206') }} 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                @if(!empty($talent['skills']))
                    {!! ___tags(array_column($talent['skills'],'skill_name'),'<span class="small-tags">%s</span>','') !!}
                @else
                    {{ N_A }}
                @endif
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0207') }} 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                @if(!empty(array_column($talent['subindustry'],'name')))
                    {!! ___tags(array_column($talent['subindustry'],'name'),'<span class="small-tags">%s</span>','') !!}
                @else
                    {{ N_A }}
                @endif
            </label>
        </div>
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0663') }} 
        </h2>
        <div class="form-group clearfix">
            @if(!empty($talent['certificate_attachments']))
                @foreach ($talent['certificate_attachments'] as $item)
                    @includeIf('talent.jobdetail.includes.attachment',['file' => $item])
                @endforeach
            @else
                {{N_A}}
            @endif
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">
                {{ trans('website.W0032') }}
            </h2>
            <div class="form-group clearfix">
                <div class="work-experience-box row">
                    @includeIf('talent.profile.includes.workexperience',['work_experience_list' => $talent['work_experiences']])
                </div>
            </div>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">{{ trans('website.W0172') }}</h2>
            <div class="form-group clearfix">
                <div class="education-box row">
                    @includeIf('talent.profile.includes.education', ['education_list' => $talent['educations']])
                </div>
            </div>
        </div>
    </div>
</div>     
@push('inlinescript')
    <script type="text/javascript">$('[data-request="delete"]').remove();$('.delete-attachment').remove();$('.edit-icon').remove();</script>
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper .row:first').remove();
            $('#dataTableBuilder').next('.row').remove();
            
            setTimeout(function(){
                if($('.dataTables_empty').length > 0){
                    $('.completed-jobs-list').remove();
                }else{
                    $('.completed-jobs-list').show();
                }
            },2000);

            $(document).on('keyup click','[name="search"],#search-list',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.filter = $('[name="search"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
@endpush