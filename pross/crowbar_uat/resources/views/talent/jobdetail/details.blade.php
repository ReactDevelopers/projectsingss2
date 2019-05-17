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
        <li>
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
        <div class="content-box find-job-listing clearfix">
            <div class="find-job-left no-border">
                <div class="content-box-header clearfix">
                    <img src="{{$project->company_logo}}" alt="profile" class="job-profile-image">
                    <div class="contentbox-header-title">
                        <h3>
                            <a>{{$project->title}}</a>
                        </h3><br>
                        <span class="small-tags m-t-5px m-b-5 f-12">{{$project->project_display_id}}</span>
                        <span class="company-name">{{$project->company_name}}</span>
                    </div>
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
                    @if(0)
                        @if(!empty($project->is_saved == DEFAULT_YES_VALUE))
                            <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="{{url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))}}"></a>
                        @else
                            <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="{{url(sprintf('%s/jobs/save-job?job_id=%s',TALENT_ROLE_TYPE,$project->id_project))}}"></a>
                        @endif
                    @endif
                    <br>
                    <div class="minutes-right">
                        @if($project->is_cancelled == DEFAULT_YES_VALUE)
                            <span class="posted-time">{{trans('general.M0578')}} {{___ago($project->canceldate)}}</span>
                        @elseif($project->project_status == 'closed')
                            <span class="posted-time">{{trans('general.M0520')}} {{___ago($project->closedate)}}</span>
                        @else
                            <span class="posted-time">{{trans('general.M0177')}} {{___ago($project->created)}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="job-detail-separator">
            <div>
                @if(!empty($project->proposal) && $project->proposal->status == 'accepted' && $project->project_status == 'initiated' && 0)
                    <div class="m-t-10px">
                        @if(0)
                            @if(!empty($project->projectlog))
                                <div class="job-total-time  m-b-10">
                                    <span class="total-time-text">{{trans('website.W0706')}}</span>
                                    <span class="total-time" id="total_working_hours_{{$project->id_project}}">{{___hours(substr($project->projectlog->total_working_hours, 0, -3))}}</span>
                                </div>
                            @else
                                <div class="job-total-time  m-b-10">
                                    <span class="total-time-text">{{trans('website.W0706')}}</span>
                                    <span class="total-time" id="total_working_hours_{{$project->id_project}}">{{___hours('00:00')}}</span>
                                </div>
                            @endif
                        @endif
                        <div class="jobtimer white-wrapper m-b-10">
                            <div class="submit-timesheet">
                                <form role="working-hours-{{$project->id_project}}" method="post" action="{{url('talent/save/working/hours?project_id='.$project->id_project)}}" autocomplete="off" >
                                    <div class="row">
                                        <div class="col-md-3 col-xs-3 col-sm-3">
                                            <label class="control-label m-t-5px">{{trans('website.W0700')}}</label>
                                        </div>
                                        <div class="col-md-6 col-xs-6 col-sm-6">
                                            <div class="form-group no-margin-bottom">
                                                <input type="text" name="working_hours_{{$project->id_project}}" autocomplete="off" class="form-control" placeholder="{{trans('website.W0701')}}" />
                                            </div>
                                            <input type="text" class="hide"/>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3">
                                            <button class="btn btn-sm redShedBtn small-button" type="button" data-request="ajax-submit" data-target='[role="working-hours-{{$project->id_project}}"]' >{{trans('website.W0013')}}</button>
                                        </div>                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif($project->project_status == 'closed')
                    <div class="m-t-10px">
                        @if(0)
                            @if(!empty($project->projectlog))
                                <div class="job-total-time  m-b-10">
                                    <span class="total-time-text">{{trans('website.W0706')}}</span>
                                    <span class="total-time" id="total_working_hours_{{$project->id_project}}">{{___hours(substr($project->projectlog->total_working_hours, 0, -3))}}</span>
                                </div>
                            @else
                                <div class="job-total-time  m-b-10">
                                    <span class="total-time-text">{{trans('website.W0706')}}</span>
                                    <span class="total-time" id="total_working_hours_{{$project->id_project}}">{{___hours('00:00')}}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
                <div class="content-box-description no-padding">
                    @if(!empty($project->industries->count()))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0655')}}</span>
                            <span class="item-description">
                                {!!___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>','')!!}
                            </span>
                        </div>
                    @endif

                    @if(!empty($project->subindustries->count()))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0206')}}</span>
                            <span class="item-description">
                                {!!___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>','')!!}
                            </span>
                        </div>
                    @endif

                    @if(!empty($project->skills->count()))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0207')}}</span>
                            <span class="item-description">
                                {!!___tags(array_column(array_column(json_decode(json_encode($project->subindustries),true),'subindustries'),'name'),'<span class="small-tags">%s</span>','')!!}
                            </span>
                        </div>
                    @endif

                    @if(!empty($project->expertise))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0208')}}</span>
                            <span class="item-description">
                                <span class="f-b">{{ucfirst($project->expertise)}}</span>
                            </span>
                        </div>
                    @endif

                    @if(!empty($project->other_perks))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0658')}}</span>
                            <span class="item-description">
                                <span class="f-b">{{$project->other_perks}} {{trans('website.W0751')}}</span>
                            </span>
                        </div>
                    @endif

                    @if(!empty(strtotime($project->startdate) && strtotime($project->enddate)))
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.W0682')}}</span>
                            <span class="item-description">
                                <span class="f-b">{{___date_difference($project->startdate, $project->enddate)}}</span>
                            </span>
                        </div>
                    @endif
                    <br/>
                    <div>
                        <span class="item-description">
                            <span class="f-b">{{trans('website.W0925')}}</span>
                        </span>
                    </div>
                    {!!___e(($project->description))!!}
                </div>
            </div>
        </div>
    </div>

    @if($project->is_cancelled == DEFAULT_NO_VALUE)
        @if(empty($project->proposal) && $project->project_status != 'closed' && $project->awarded === DEFAULT_NO_VALUE)
            <a href="{{ url(sprintf('%s/find-jobs/proposal?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project->id_project))) }}" class="button bottom-margin-10px" title="{{trans('job.J0013')}}">{{trans('job.J0013')}}</a>
        @elseif($project->project_status != 'closed')
            @if(!empty($project->proposal) && $project->project_status != 'closed' && $project->awarded === DEFAULT_NO_VALUE && $project->status != 'trashed')
                <a href="{{ url(sprintf('%s/find-jobs/proposal?job_id=%s&action=edit',TALENT_ROLE_TYPE,___encrypt($project->id_project))) }}" class="btn btn-secondary bottom-margin-10px pull-right" title="{{trans('website.W0810')}}">{{trans('website.W0810')}}</a>
            @endif
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        @if(!empty($project->dispute) && !empty($project->proposal))
                            <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                @if(empty($project->dispute))
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="{{trans('website.W0409')}}">{{trans('website.W0409')}}</a>
                                @else
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                @endif
                            </div>
                        @endif
                        @if(!empty($project->chat))
                            @if(empty($project->chat->chat_initiated) && !empty($project->proposal) && $project->proposal->status === 'accepted')
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <div class="send-chat-request">
                                        <button type="button" data-request="send-chat-request" data-receiver="{{ $project->company_id }}" data-sender="{{ $project->proposal->user_id }}" data-project="{{ $project->id_project }}" data-target=".send-chat-request" data-url="{{ url(sprintf('%s/chat/initiate-chat-request',TALENT_ROLE_TYPE)) }}" class="btn btn-secondary" title="{{trans('job.J0062')}}">{{trans('job.J0062')}}</button>    
                                    </div>
                                </div>
                            @elseif($project->chat->chat_initiated === 'talent')
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <div class="send-chat-request">
                                        <button class="btn btn-secondary" title="{{ trans('job.J0063') }}">{{ trans('job.J0063') }}</button>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(!empty($project->proposal) && $project->proposal->status == 'accepted')
                            @if($project->project_status == 'pending')
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="button" data-request="inline-ajax" data-url="{{ url(sprintf('%s/project/status/start?project_id=%s',TALENT_ROLE_TYPE,___decrypt($project->id_project))) }}" class="button bottom-margin-10px" title="{{trans('job.J0053')}}">{{trans('job.J0053')}}</button>
                                </div>
                            @elseif($project->project_status == 'initiated')
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="button" data-request="inline-ajax" data-url="{{ url(sprintf('%s/project/status/close?project_id=%s',TALENT_ROLE_TYPE,___decrypt($project->id_project))) }}" class="button bottom-margin-10px" title="{{trans('job.J0054')}}">{{trans('job.J0054')}}</button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        @if(!empty($project->dispute) && !empty($project->proposal))
                            <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                @if(empty($project->dispute))
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="{{trans('website.W0409')}}">{{trans('website.W0409')}}</a>
                                @else
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",TALENT_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                @endif
                            </div>
                        @endif
                        @if($project->project_status == 'closed' && $project->reviews_count == 0 && (!empty($project->proposal) && $project->proposal->talent->id_user == $user['id_user']))
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <a href="{{ url(sprintf('%s/project/submit/reviews?job_id=%s',TALENT_ROLE_TYPE,___encrypt($project->id_project))) }}" class="button bottom-margin-10px" title="{{trans('website.W0719')}}">{{trans('website.W0719')}}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

@push('inlinescript')
    <style type="text/css">
        .btn-green, .button {
            width: auto;
            display: inline-block;
            float: right;
        }
    </style>
    <script type="text/javascript" src="{{asset('js/bootstrap-timepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("[name=\"working_hours_{{$project->id_project}}\"]").timepicker({
                template: false,
                showMeridian: false,
                defaultTime: "00:00"
            });
        })
    </script>
@endpush