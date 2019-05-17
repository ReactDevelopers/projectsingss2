@include('employer.job.includes.talent-profile-menu')
<div class="login-inner-wrapper profile-info-details">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0206') }} 
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
            {{ trans('website.W0207') }} 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                @if(!empty($talent['skills']))
                    {!! ___tags($talent['skills'],'<span class="small-tags">%s</span>','') !!}
                @else
                    {{ N_A }}
                @endif
            </label>
        </div>
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
            {{ trans('website.W0663') }} 
        </h2>
        <div class="form-group clearfix">
            @php
                if(!empty($talent['certificate_attachments'])){
                    foreach ($talent['certificate_attachments'] as $item) {
                        $url_delete = sprintf(
                            url('ajax/%s?id_file=%s'),
                            DELETE_DOCUMENT,
                            $item['id_file']
                        );
                        echo sprintf(RESUME_TEMPLATE,
                            $item['id_file'],
                            url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                            asset('/'),
                            substr($item['filename'],0,3),
                            $item['size'],
                            $url_delete,
                            $item['id_file'],
                            asset('/')
                        );  
                    }
                }else{
                    echo N_A;
                }
            @endphp
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
@endpush