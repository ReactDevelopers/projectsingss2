<div>
    <ul class="user-profile-links">
        <li class="resp-tab-item">
            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0678')}}
            </a>
        </li>
        <li>
            <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0679')}}
            </a>
        </li>
        <li class="active">
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
        <div class="inner-profile-section">
            <div class="form-group clearfix">
                <label class="control-label">{{ trans('website.W0248') }}</label>
                    @if(!empty($project->employer->contact_person_name))
                        <br>{{ $project->employer->contact_person_name }}
                    @else
                        <br>{{ N_A }}
                    @endif
            </div>
            <div class="form-group clearfix">
                <label class="control-label">{{ trans('website.W0249') }}</label>
                @if(!empty($project->employer->company_website))
                    <br><a target="_blank">{{ $project->employer->company_website }}</a>
                @else
                    <br>{{ N_A }}
                @endif
            </div>
            <div class="form-group clearfix">
                <label class="control-label">{{ trans('website.W0787') }}</label>
                @if(!empty($project->employer->company_work_field))
                    <br>{{ ___cache("work_fields",$project->employer->company_work_field) }}
                @else
                    <br>{{ N_A }}
                @endif
            </div>            
            <div class="form-group clearfix">
                <label class="control-label">{{ trans('website.W0800') }}</label>
                @if(!empty($project->employer->company_biography))
                    <br>{{ nl2br($project->employer->company_biography) }}
                @else
                    <br>{{ N_A }}
                @endif
            </div>
        </div>
    </div>
</div>