<ul class="user-profile-links">
    <li class="{{ Request::path() == (sprintf('%s/jobs/saved-jobs', TALENT_ROLE_TYPE)) ? 'active' : '' }}">
        <a href="{{url(sprintf('%s/jobs/saved-jobs', TALENT_ROLE_TYPE))}}">{{trans('job.J0032')}}</a>
    </li>
    <li class="{{ Request::path() == (sprintf('%s/jobs/current-jobs', TALENT_ROLE_TYPE)) ? 'active' : '' }}">
        <a href="{{url(sprintf('%s/jobs/current-jobs', TALENT_ROLE_TYPE))}}">{{trans('job.J0033')}}</a>
    </li>
    <li class="{{ Request::path() == (sprintf('%s/jobs/jobs-scheduled', TALENT_ROLE_TYPE)) ? 'active' : '' }}">
        <a href="{{url(sprintf('%s/jobs/jobs-scheduled', TALENT_ROLE_TYPE))}}">{{trans('job.J0034')}}</a>
    </li>
    <li class="{{ Request::path() == (sprintf('%s/jobs/past-job-history', TALENT_ROLE_TYPE)) ? 'active' : '' }}">
        <a href="{{url(sprintf('%s/jobs/past-job-history', TALENT_ROLE_TYPE))}}">{{trans('job.J0035')}}</a>
    </li>
</ul> 