{{-- JOB ACTION BUTTON --}}
@if($job_detail['jobaction']['section'] == 'submit_application')
    <a href="{{ url(sprintf('%s/find-jobs/proposal?job_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project']))) }}" class="button bottom-margin-10px" title="{{trans('job.J0064')}}">{{trans('job.J0064')}}</a>
@elseif($job_detail['jobaction']['section'] == "application_submitted")
    <button class="button-line bottom-margin-10px">{{trans('job.J0066')}}</button>
@elseif($job_detail['jobaction']['section'] == 'submit_proposal')
    <a href="{{ url(sprintf('%s/find-jobs/proposal?job_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project']))) }}" class="button bottom-margin-10px" title="{{trans('job.J0013')}}">{{trans('job.J0013')}}</a>
@elseif($job_detail['jobaction']['section'] == 'start')
    <button data-render="notification" data-receiver="{{$job_detail['receiver_id']}}" data-sender="{{$job_detail['sender_id']}}" data-request="inline-ajax" data-url="{{url(sprintf('%s/jobs/actions/start?project_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}" class="btn-green bottom-margin-10px" title="{{trans('job.J0053')}}">{{trans('job.J0053')}}</button>
@elseif($job_detail['jobaction']['section'] == 'startpending' && 0)
    <button class="button bottom-margin-10px" title="{{trans('job.J0067')}}">{{trans('job.J0067')}}</button>
@elseif($job_detail['jobaction']['section'] == 'close')
    <button data-render="notification" data-receiver="{{$job_detail['receiver_id']}}" data-sender="{{$job_detail['sender_id']}}" data-request="confirm-ajax" data-title="{{trans('general.M0378')}}" data-ask="{{trans('general.M0377')}}" data-url="{{url(sprintf('%s/jobs/actions/close?project_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}"  class="button bottom-margin-10px" title="{{trans('job.J0054')}}">{{trans('job.J0054')}}</button>
@elseif($job_detail['jobaction']['section'] == 'closepending' && 0)
    <button class="button bottom-margin-10px" title="{{trans('job.J0068')}}">{{trans('job.J0068')}}</button>
@endif

{{-- FOR CHAT BUTTON --}}

@if($job_detail['jobaction']['chat']['chataction'] == 'yes')
    <div class="send-chat-request">
        <button type="button" class="button-line bottom-margin-10px" data-request="chat-initiate" data-user="{{ $job_detail['jobaction']['chat']['receiver_id'] }}" data-url="{{ url(sprintf('%s/chat',TALENT_ROLE_TYPE)) }}">{{ trans('website.W0296') }}</button>
    </div>
@elseif($job_detail['jobaction']['chat']['chataction'] == 'chat_not_accepted')
    <div class="send-chat-request">
        <button class="button-line bottom-margin-10px" title="{{ trans('job.J0063') }}">{{ trans('job.J0063') }}</button>
    </div>
@elseif($job_detail['jobaction']['chat']['chataction'] == 'request_for_chat')
    <div class="send-chat-request">
        <button type="button" data-request="send-chat-request" data-receiver="{{ $job_detail['jobaction']['chat']['receiver_id'] }}" data-sender="{{ $job_detail['jobaction']['chat']['sender_id'] }}" data-target=".send-chat-request" data-url="{{ url(sprintf('%s/chat/initiate-chat-request',TALENT_ROLE_TYPE)) }}" class="button-line bottom-margin-10px" title="{{trans('job.J0062')}}">{{trans('job.J0062')}}</button>
    </div>
@endif

{{-- JOB RAISE DISPUTE BUTTON --}}

@if($job_detail['jobaction']['dispute'] == 'yes')
    <span class="italicText bottom-margin-10px" data-request="inline-ajax" data-url="{{url(sprintf('%s/raise/dispute/initiate?project_id=%s&sender_id=%s&receiver_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project']),___encrypt($job_detail['sender_id']),___encrypt($job_detail['receiver_id'])))}}">{{trans('website.W0409')}}</span>
@elseif($job_detail['jobaction']['dispute'] == 'already_disputed')
    <a class="italicText bottom-margin-10px" href="{{url(sprintf('%s/my-jobs/disputes?job_id=%s',TALENT_ROLE_TYPE,___encrypt($job_detail['id_project'])))}}">{{trans('website.W0409')}}</a>
@endif 