<div class="login-inner-wrapper profile-info-details margin-left-none margin-top-none">
    <div class="no-wrapper">
        <h2 class="form-heading bold-heading">
            {{ trans('website.W0030') }} 
        </h2>
        <div class="form-group clearfix m-b-15">
            <label class="info-label">
                @if(!empty(array_column($user['industry'],'name')))
                    {!! ___tags(array_column($user['industry'],'name'),'<span class="small-tags">%s</span>','') !!}
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
                @if(!empty($user['skills']))
                    {!! ___tags(array_column($user['skills'], 'skill_name'),'<span class="small-tags">%s</span>','') !!}
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
                @if(!empty(array_column($user['subindustry'],'name')))
                    {!! ___tags(array_column($user['subindustry'],'name'),'<span class="small-tags">%s</span>','') !!}
                @else
                    {{ N_A }}
                @endif
            </label>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">
                {{ trans('website.W0032') }}
            </h2>
            <div class="form-group clearfix">
                <div class="work-experience-box row">
                    @includeIf('front.pages.talent_profile.includes.workexperience',['work_experience_list' => $user['work_experiences']])
                </div>
            </div>
        </div>
        <div class="m-t-35">
            <h2 class="form-heading bold-heading">{{ trans('website.W0172') }}
            </h2>
            <div class="form-group clearfix">
                <div class="education-box row">
                    @includeIf('front.pages.talent_profile.includes.education', ['education_list' => $user['educations']])
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12  ">
            <div class="form-btn-set form-top-padding pull-right">
                @if(!empty(\Auth::user()) && \Auth::user()->type == 'talent')
                    <div class="col-md-6 col-sm-6 col-xs-6 pull-right">
                        <a href="{!!url('talent/view/'.Request::segment('4'))!!}" class="button"  >View More</a>
                    </div>
                @elseif(!empty(\Auth::user()) && \Auth::user()->type == 'employer')
                    <div class="col-md-6 col-sm-6 col-xs-6 pull-right">
                        <a href="{!!url('employer/find-talents/profile?talent_id='.Request::segment('4'))!!}" class="button"  >View More</a>
                    </div>
                @else
                    <div class="col-md-10 col-sm-10 col-xs-10 pull-right">
                        <a href="{!!url('login?talent_id='.Request::segment('4'))!!}" class="button"  >Login to View More</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>     
@push('inlinescript')
    <style>.education-box .edit-icon, .work-experience-box .edit-icon{display: none!important;}.m-t-35 .educationEditSec{margin-bottom: 15px;}</style>
@endpush