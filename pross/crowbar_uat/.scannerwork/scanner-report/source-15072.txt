@if(!empty($invitation))
    <div class="jobdetails-sidebar-content">
        <h3>{{trans('job.J00123')}}</h3>
        <div class="other-gigs-item">
            <div class="content-box">
                <div class="content-box-header clearfix">
                    <img src="{{asset($job_details['company_logo'])}}" alt="profile" class="job-profile-image">
                    <div class="contentbox-header-title">
                        <span class="company-name">{{$job_details['company_name']}}</span>
                    </div>                       
                </div>
                <div class="contentbox-minutes clearfix">
                    <div class="minutes-left font-12x">
                        {{$invitation->message}}                     
                    </div>
                </div>
                <div class="contentbox-minutes clearfix">
                    <div class="minutes-right">
                        <span class="posted-time">{{ trans('general.M0177') }} {{ ___ago($invitation->created) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif