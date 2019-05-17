@if($job_detail['jobaction']['section'] == 'no_proposal_accepted')
    <a class="button-line bottom-margin-10px" href="{{ url(sprintf('%s/proposals/listing?id_project=%s',EMPLOYER_ROLE_TYPE,___encrypt($job_detail['id_project']))) }}" title="{{trans('job.J0046')}}">{{trans('job.J0046')}}</a>
@elseif($job_detail['jobaction']['section'] == 'start' && 0)
    <button class="button bottom-margin-10px" title="{{trans('job.J0067')}}">{{trans('job.J0067')}}</button>
@elseif($job_detail['jobaction']['section'] == 'startpending')
    <button data-render="notification" data-receiver="{{$job_detail['receiver_id']}}" data-sender="{{$job_detail['sender_id']}}" data-request="inline-ajax" data-url="{{url(sprintf('%s/my-jobs/start?project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}" class="btn-green bottom-margin-10px" title="{{trans('job.J0069')}}">{{trans('job.J0069')}}</button>
@elseif($job_detail['jobaction']['section'] == 'close' && 0)
    <button class="button bottom-margin-10px" title="{{trans('job.J0071')}}" data-request="alert" data-title="{{trans('job.J0081')}}" data-message="{{trans('job.J0082')}}">{{trans('job.J0071')}}</button>
@elseif($job_detail['jobaction']['section'] == 'closepending')
    <button data-render="notification" data-receiver="{{$job_detail['receiver_id']}}" data-sender="{{$job_detail['sender_id']}}" class="button bottom-margin-10px" title="{{trans('job.J0057')}}" data-request="confirm-ajax" data-title="{{trans('general.M0378')}}" data-ask="{{trans('general.M0373')}}" data-url="{{url(sprintf('%s/my-jobs/close?project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}">{{trans('job.J0057')}}</button>
@endif

@if($job_detail['jobaction']['chat']['chataction'] == 'yes')
    <div class="send-chat-request">
        <button type="button bottom-margin-10px" class="button-line bottom-margin-10px" data-request="chat-initiate" data-user="{{ $job_detail['jobaction']['chat']['receiver_id'] }}" data-url="{{ url(sprintf('%s/chat',TALENT_ROLE_TYPE)) }}">{{ trans('website.W0296') }}</button>
    </div>
@endif

@if($job_detail['jobaction']['dispute'] == 'yes')
    <span class="italicText bottom-margin-10px" data-request="inline-ajax" data-url="{{url(sprintf('%s/raise/dispute/initiate?project_id=%s&sender_id=%s&receiver_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($job_detail['id_project']),___encrypt($job_detail['sender_id']),___encrypt($job_detail['receiver_id'])))}}">{{trans('website.W0409')}}</span>
@elseif($job_detail['jobaction']['dispute'] == 'already_disputed')
	<a class="italicText bottom-margin-10px" href="{{url(sprintf('%s/my-jobs/disputes?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}">{{trans('website.W0409')}}</a>
@endif 