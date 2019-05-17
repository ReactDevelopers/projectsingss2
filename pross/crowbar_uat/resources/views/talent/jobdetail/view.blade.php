<div>
    <ul class="user-profile-links">
        <li class="active">
            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0678')}}
            </a>
        </li>
        <li class="resp-tab-item">
            <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0679')}}
            </a>
        </li>
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="content-box find-job-listing clearfix">
            <div class="find-job-left no-border">
                <div class="content-box-header clearfix">
                    <img src="{{$project->company_logo}}" alt="profile" class="job-profile-image">
                    <div class="contentbox-header-title">
                        <h3>
                            <a>{{$project->title}}</a>
                        </h3>
                        <span class="company-name">{{$project->company_name}}</span>
                    </div>
                </div>
                <div class="content-box-description">
                    {!!___e(nl2br($project->description))!!}
                </div>
            </div>
            <div class="find-job-right b-l">
                <div class="contentbox-price-range">
                    <span>
                        {{___format($project->price,true,true)}}
                        <br>
                        @if($project->employment == 'fixed')
                            <span class="label-green color-grey">{{$project->employment}}</span>
                        @else
                            <span class="small-price-type">{{job_types_rates_postfix($project->employment)}}</span>
                        @endif
                    </span>
                
                    @if(!empty($project->last_viewed))
                        <span class="last-viewed-icon active"></span>
                    @endif
                </div>
                <div class="contentbox-minutes clearfix">
                    @if(!empty($project->is_saved == DEFAULT_YES_VALUE))
                        <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="{{url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))}}"></a>
                    @else
                        <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="{{url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))}}"></a>
                    @endif
                    <br>
                    <div class="minutes-right">
                        <span class="posted-time">{{trans('general.M0177')}} {{___ago($project->created)}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
