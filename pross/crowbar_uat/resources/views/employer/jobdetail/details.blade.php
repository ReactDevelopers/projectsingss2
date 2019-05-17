<div class="row mainContentWrapper">
    @includeIf('employer.jobdetail.includes.sidebar')
    <div class="col-md-8 col-sm-8 right-sidebar">
        <div>
            <ul class="user-profile-links">
                <li class="active">
                    <a href="{{url('employer/project/details?job_id='.___encrypt($project->id_project))}}">
                        {{trans('website.W0678')}}
                    </a>
                </li>
                @if(!empty($project->proposal))
                    <li class="resp-tab-item">
                        <a href="{{url('employer/project/proposals/talent?proposal_id='.___encrypt($project->proposal->id_proposal).'&project_id='.___encrypt($project->id_project))}}">
                            {{trans('website.W0722')}}
                        </a>
                    </li>
                @endif
                @if($project->reviews_count > 0)
                    <li class="resp-tab-item">
                        <a href="{{url('employer/project/submit/reviews?job_id='.___encrypt($project->id_project))}}">
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

                        <div class="row">
                            <div class="col-md-6">
                                @if($project->awarded === DEFAULT_NO_VALUE)
                                    <span class="pull-right m-t-15">
                                        <a href="{{ url(sprintf('employer/hire/talent/edit/one?job_id=%s',___encrypt($project->id_project))) }}" title="{{trans('website.W0785')}}"><img src="{{asset('images/edit-icon.png') }}" /></a>&nbsp;&nbsp;
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="{{ url(sprintf('employer/project/delete-job?job_id=%s',___encrypt($project->id_project))) }}" data-ask="{{trans('website.delete_job_confimation')}}" data-title="{{trans('website.W0551')}}" title="{{trans('website.W0784')}}"><img src="{{asset('images/delete-icon.png') }}" /></a>
                                    </span>
                                @endif

                                @if($project->project_status === 'pending' && $project->awarded === DEFAULT_YES_VALUE && $project->is_cancelable == DEFAULT_YES_VALUE && $project->is_cancelled == DEFAULT_NO_VALUE)
                                    <span class="pull-right m-t-15">
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="{{ url(sprintf('employer/project/cancel-job?job_id=%s',___encrypt($project->id_project))) }}" data-ask="{{ sprintf(trans('website.cancel_job_confimation_amt'),$cancellation_amount) }}" data-title="{{trans('website.W0551')}}" title="{{trans('website.W0786')}}"><img width="20" src="{{asset('images/cancel-icon.png') }}" /></a>
                                    </span>
                                @endif
                                
                            </div>
                            <div class="col-md-6 social_listing">
                                {{-- Sharing --}}
                                <div class="dropdown pull-right socialShareDropdown m-t-15">
                                    <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans('website.W0908')}}</a>
                                    <ul class="dropdown-menu">
                                        @php
                                            /*This is the public link for Job detail*/
                                            $job_detail_url = url('project/show-details/category/'.$job_category.'/job_id/'.___encrypt($project->id_project));
                                        @endphp
                                        <li>
                                            <a href="javascript:void(0);" class="linkdin_icon">
                                                <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                                <script type="IN/Share" data-url="{{$job_detail_url}}"></script>
                                                <img src="{{asset('images/linkedin.png')}}">
                                            </a>
                                        </li>
                                        <li>
                                            <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{$job_detail_url}}" target="_blank">
                                                <img src="{{asset('images/facebook.png')}}">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://web.whatsapp.com/send?text={{ $job_detail_url }}" target="_blank
                                            " id="whatsapp_link" data-action="share/whatsapp/share" >
                                            <img src="{{asset('images/whatsapp-logo.png')}}">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                {{-- Sharing --}}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="job-detail-separator">
                    <div class="">
                        @if(!empty($project->proposal) && $project->project_status != 'pending' && 0)
                            <div class="m-t-10px">
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
                            </div>
                            <br>
                        @endif
                        @if(!empty($project->proposals))
                            <div class="item-list">
                                <span class="item-heading m-b-10 clearfix">{{trans('website.W0845')}}</span>
                                <a class="image-tags-wrapper" href="{{url(sprintf('%s/project/proposals/detail?id_project=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))}}">
                                    {!!___tags(array_column(array_column(json_decode(json_encode($project->proposals),true),'talent'),'company_logo'),'<img src="%s" class="image-tags" />','',false,NULL,5)!!}
                                </a>
                            </div>
                        @endif
                        <div class="content-box-description">
                            @if(0)
                                <div class="item-list">
                                    <span class="item-heading clearfix">{{trans('website.jobid')}}</span>
                                    <span class="item-description">
                                        <span class="small-tags">{{$project->project_display_id}}</span>
                                    </span>
                                </div>
                            @endif
                            
                            @if(!empty($project->industries->count()))
                                <div class="item-list">
                                    <span class="item-heading clearfix">{{trans('website.W0655')}}</span>
                                    <span class="item-description">
                                        {!!___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>','')!!}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($project->skills->count()))
                                <div class="item-list">
                                    <span class="item-heading clearfix">{{trans('website.W0206')}}</span>
                                    <span class="item-description">
                                        {!!___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>','')!!}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($project->subindustries->count()))
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
                                        <span class="f-b">{{$project->other_perks}} {{trans('website.W0669')}}</span>
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
        </div>
        <div class="row form-group button-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                @if($project->is_cancelled == DEFAULT_NO_VALUE)
                    <div class="row form-btn-set">
                        @if(!empty($project->proposal) && ($project->proposal->payment == 'pending' || !empty($project->dispute)) && $project->project_status != 'pending' && $project->project_status != 'closed')
                            @if(empty($project->dispute))
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="{{trans('website.W0409')}}">{{trans('website.W0409')}}</a>
                                </div>
                            @else
                                <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                    <a href='{{url(sprintf("%s/project/dispute/details?job_id=%s",EMPLOYER_ROLE_TYPE,___encrypt($project->id_project)))}}' class="red-link italic m-t-10px pull-right" title="View Dispute">View Dispute</a>
                                </div>
                            @endif
                        @endif

                        @if($project->project_status !== 'closed' && 0)
                            @if(!empty($project->proposal))
                                @if(!empty($project->chat) && in_array($project->chat->chat_initiated,['employer','employer-accepted']))
                                    <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                        <a href="javascript:void(0);" class="btn btn-secondary" data-request="chat-initiate" data-user="{{ $project->proposal->user_id }}" data-url="{{ url(sprintf('%s/chat',EMPLOYER_ROLE_TYPE)) }}">
                                           {{trans('job.J00126')}}
                                        </a>
                                    </div>
                                @else
                                    <div class="col-md-6 col-sm-6 col-xs-12 v-t">
                                        <a href="javascript:void(0);" class="btn btn-secondary" data-request="inline-ajax" data-url="{{url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->company_id),___encrypt($project->proposal->user_id),___encrypt($project->id_project)))}}">
                                           {{trans('job.J00127')}}
                                        </a>
                                    </div>
                                @endif
                            @endif
                        @endif

                        @if($project->project_status == 'completed')
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button type="button" data-request="inline-ajax" data-url="{{ url(sprintf('%s/project/status/close?project_id=%s',EMPLOYER_ROLE_TYPE,___decrypt($project->id_project))) }}" class="button bottom-margin-10px" title="{{trans('job.J00132')}}">{{trans('job.J00132')}}</button>
                            </div>
                        @elseif($project->project_status == 'closed')
                            @if($project->reviews_count == 0 && $project->awarded != DEFAULT_NO_VALUE)
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <a href="{{ url(sprintf('%s/project/submit/reviews?job_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project))) }}" class="button" title="{{trans('website.W0719')}}">{{trans('website.W0719')}}</a>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
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
        });

        $(document).ready(function(){
            // console.log("readyyyyyyyy");
            // console.log("link is- "+'{{ $job_detail_url }}');
            //Change Whatsapp link according to Web or Mobile
            var isMobile1 = window.orientation > -1;
            isMobile1 = isMobile1 ? 'Mobile' : 'Not mobile';

            if(isMobile1 == 'Mobile'){
                //Whatsapp Mobile link share
                $('#whatsapp_link').attr('href','whatsapp://send?text={{ $job_detail_url }}');
            }else{
                //Whatsapp Web link share
                $('#whatsapp_link').attr('href','https://web.whatsapp.com/send?text={{ $job_detail_url }}');
            }
        });
    </script>
@endpush