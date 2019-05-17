@if(!empty($job_details['similar_jobs']))
    <div class="jobdetails-sidebar-content">
        <h3>{{trans('job.J0016')}}</h3>
        <div class="other-gigs-item">
            @foreach($job_details['similar_jobs'] as $item)
                <div class="content-box">
                    <div class="content-box-header clearfix">
                        <img src="{{asset($item['company_logo'])}}" alt="profile" class="job-profile-image">
                        <div class="contentbox-header-title">
                            @if($item['employment'] == 'fulltime')
                                <span class="label-green">{{ employment_types('post_job',$item['employment']) }}</span>
                            @endif
                            <h3><a href="{{ url(sprintf('%s/find-jobs/job-details?job_id=%s',TALENT_ROLE_TYPE,___encrypt($item['id_project'])))}}" title="{{ $item['title'] }}">{{ $item['title'] }}</a></h3>
                            <span class="company-name">{{$item['company_name']}}</span>
                        </div>                       
                    </div>
                    <div class="contentbox-minutes clearfix">
                        <div class="minutes-left">
                            <span>{{trans('website.W0200')}}: <strong>{{ $item['industry_name'] }}</strong></span>                           
                        </div>
                    </div>
                    <div class="contentbox-minutes clearfix">
                        <div class="minutes-right">
                            <span class="posted-time">{{trans('general.M0177')}} {{ $item['created'] }}</span>
                        </div>
                    </div>
                    <div class="contentbox-price-range">
                        <span>{{___format($item['price'],true,true).job_types_rates_postfix($item['employment'])}}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif