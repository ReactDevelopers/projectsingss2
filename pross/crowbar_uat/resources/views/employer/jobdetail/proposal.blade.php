<div class="row mainContentWrapper">
    <div class="col-md-4 col-sm-4 left-sidebar clearfix">
        @includeIf('employer.jobdetail.talent')
    </div>
    <div class="col-md-8 col-sm-8 right-sidebar"> 
        <div>
            <div class="message">
                {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
            </div>    
            <ul class="user-profile-links">
                <li class="resp-tab-item">
                    <a href="{{url('employer/project/details?job_id='.___encrypt($project->id_project))}}">
                        {{trans('website.W0678')}}
                    </a>
                </li>
                <li class="active">
                    <a href="{{url('employer/project/proposals/talent?proposal_id='.___encrypt($project->proposal->id_proposal).'&project_id='.___encrypt($project->id_project))}}">
                        @if(!empty($project->proposals_count))
                            {{trans('website.W0722')}}
                        @else
                            {{trans('website.W0343')}}
                        @endif
                    </a>
                </li>
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
                    <div class="view-information no-padding">
                        <h2>{{ trans('website.W0689') }} </h2>
                    </div>
                    @if(!empty($companydata))
                        <div class="company-name-wrapper">
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">Company Name</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info">{{ $companydata->company_name }}</span>
                                    </div>
                            </div>
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">Company Website</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info">{{$companydata->company_website}}</span>
                                    </div>
                            </div>
                            <div class="info-row row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label class="company-label">About the Company</label>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <span class="company-name-info">{{$companydata->company_biography }}</span>
                                    </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    @endif
                    <div class="col-md-7">
                        <div class="content-box-description m-b-20px">
                            <div class="view-information no-padding">
                                <h2>{{ trans('website.W0989') }} </h2>
                            </div>
                            @if(!empty($project->proposal->quoted_price))
                                <div class="item-list">
                                    <span class="item-heading clearfix">{{trans('website.W0363')}}</span>
                                    <span class="item-description">
                                        <span class="small-tags">{{___format($project->proposal->quoted_price,true,true,true)}}</span>
                                    </span>
                                </div>
                            @endif

                            @if($project->employment == 'hourly')
                                <div class="item-list">
                                    <span class="item-heading clearfix">{{trans('website.W0757')}}</span>
                                    <span class="item-description">
                                        @if(!empty($project->proposal->working_hours))
                                            <span class="small-tags">{{substr($project->proposal->working_hours, 0, -3)}} {{trans('website.W0759')}}</span>
                                        @else
                                            <span class="small-tags">{{'00:00'}} {{trans('website.W0759')}}</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="m-b-20px">
                            <span class="item-heading clearfix">{{trans('website.W0664')}}</span>
                            @if(!empty($project->proposal->comments))
                                {!!___e(nl2br($project->proposal->comments))!!}
                            @else
                                {{N_A}}
                            @endif
                        </div>
                        
                        <div class="m-b-10px">
                            <span class="item-heading clearfix">{{trans('website.W0691')}}</span>
                            @if(!empty($project->proposal->file))
                                @includeIf('talent.jobdetail.includes.attachment',['file' => json_decode(json_encode($project->proposal->  file),true)])
                            @else
                                {{N_A}}
                            @endif
                        </div>

                        <span class="review-time">
                            {{trans('website.W0690')}} {{___d($project->proposal->created)}}
                        </span>
                    </div>
                    <div class="col-md-5">
                        <div class="employer-detail-box">
                            <h2 class="heading-sm m-b-20px">{{trans('website.W0652')}}</h2>
                            <div class="form-group">
                                <h2 class="small-heading">{{trans('website.W0094')}}</h2>
                                <span>{{$project->title}}</span>
                            </div>
                            <div class="form-group price-list">
                                <h2 class="small-heading">{{trans('website.W0846')}}</h2>
                                <span>
                                    {{___format($project->price,true,true)}} / 
                                    @if($project->employment == 'fixed')
                                        {{$project->employment}}
                                    @else
                                        {{job_types_rates_postfix($project->employment)}}
                                    @endif
                                </span>
                            </div>  
                            <div class="form-group timeline">
                                <h2 class="small-heading">{{trans('website.W0682')}}</h2>
                                <span>
                                    @if(!empty(strtotime($project->startdate) && strtotime($project->enddate)))
                                        {{___date_difference($project->startdate, $project->enddate)}}
                                    @endif
                                </span>
                            </div>
                            @if($project->employment == 'hourly')
                                <div class="form-group">
                                    <h2 class="small-heading">{{trans('website.W0793')}}</h2>
                                    <span>
                                        @if(!empty(strtotime($project->expected_hour)))
                                            {{___hours($project->expected_hour)}}
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div class="form-group cancelation">
                                <h2 class="small-heading">{{trans('website.W0930')}}</h2>
                                @php
                                    $commission = ___cache('configuration')['cancellation_commission'];
                                    $commission_type = ___cache('configuration')['cancellation_commission_type'];

                                    if($commission_type == 'per'){
                                        $calculated_commission=___format(round(((($project->price*$commission)/100)),2)); 
                                    }else{
                                        $calculated_commission = ___format(round(($commission),2));
                                    }

                                    $refundable_amount = $project->price - $calculated_commission;
                                @endphp
                                <span>
                                {{___format($refundable_amount,true,true)}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                @if(empty($project->proposal))
                    <div class="row form-group button-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row form-btn-set">
                                <div class="col-md-7 col-sm-7 col-xs-6">
                                    <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}" class="greybutton-line">{{trans('job.J0028')}}</a>
                                </div>
                                <div class="col-md-5 col-sm-5 col-xs-6">
                                    <button id="doc-button" type="button" data-request="trigger-proposal" data-target="#proposal-form" data-copy-source='[name="documents[]"]' data-copy-destination='[name="proposal_docs"]' class="button" value="Submit">
                                            {{trans('website.W0013')}}
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if(empty($project->proposal_count) && $project->project_status == 'pending')
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">                                    
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="button" class="pull-right button-line" data-request="inline-ajax" data-url="{{url(sprintf('%s/proposals/decline?proposal_id=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___decrypt($project->proposal->id_proposal),___decrypt($proposal->project_id)))}}">{{trans('website.W0220')}}</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <a href="{{ url(sprintf('%s/payment/checkout?project_id=%s&proposal_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($project->id_project),___encrypt($project->proposal->id_proposal))) }}" class="button" title="{{trans('website.W0221')}}">{{trans('website.W0221')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@push('inlinecss')
    <style>
        .education-box .edit-icon, .work-experience-box .edit-icon, [data-request="delete"]{display: none!important;}
        span.last-viewed-icon{
            position: relative;
            top: -8px;
        }
        .new-upload .uploaded-docx{max-width: 100%;}
    </style>
@endpush
