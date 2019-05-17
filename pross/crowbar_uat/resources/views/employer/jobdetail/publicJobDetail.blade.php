@php 
    $header = 'innerheader';
    $footer = 'footer';
    $settings = \Cache::get('configuration');
@endphp
@extends('layouts.front.main')
@section('content')
    <div class="container public-job-detail summary-container">
        <div class="row mainContentWrapper">
            <div class="col-md-12 col-sm-12 right-sidebar">
                <div>
                    <ul class="user-profile-links public-job-detail">
                        <li class="active">
                            <b>{{trans('website.W0678')}}</b>                                
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
                                @if($project->project_status === 'pending' && $project->awarded === DEFAULT_YES_VALUE && $project->is_cancelable == DEFAULT_YES_VALUE && $project->is_cancelled == DEFAULT_NO_VALUE)
                                    <span class="pull-right m-t-15">
                                        <a href="javascript:void(0);" data-request="delete-job" data-url="{{ url(sprintf('employer/project/cancel-job?job_id=%s',___encrypt($project->id_project))) }}" data-ask="{{trans('website.cancel_job_confimation')}}" data-title="{{trans('website.W0551')}}" title="{{trans('website.W0786')}}"><img width="20" src="{{asset('images/cancel-icon.png') }}" /></a>
                                    </span>
                                @endif
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
                                <div class="content-box-description">
                                    @if(0)
                                        <div class="item-list">
                                            <span class="item-heading clearfix">{{trans('website.jobid')}}</span>
                                            <span class="item-description">
                                                <span class="small-tags">{{$project->project_display_id}}</span>
                                            </span>
                                        </div>
                                    @endif

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(!empty($project->industries->count()))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0655')}}</span>
                                                        <span class="item-description">
                                                            {!!___tags(array_column(array_column(json_decode(json_encode($project->industries),true),'industries'),'name'),'<span class="f-b">%s</span>','')!!}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if(!empty($project->expertise))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0208')}}</span>
                                                        <span class="item-description">
                                                            <span class="f-b">{{ucfirst($project->expertise)}}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(!empty($project->skills->count()))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0206')}}</span>
                                                        <span class="item-description">
                                                            {!!___tags(array_column(array_column(json_decode(json_encode($project->skills),true),'skills'),'skill_name'),'<span class="small-tags">%s</span>','')!!}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if(!empty($project->other_perks))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0658')}}</span>
                                                        <span class="item-description">
                                                            <span class="f-b">{{$project->other_perks}} {{trans('website.W0669')}}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div class="draw-underline">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(!empty($project->subindustries->count()))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0207')}}</span>
                                                        <span class="item-description">
                                                            {!!___tags(array_column(array_column(json_decode(json_encode($project->subindustries),true),'subindustries'),'name'),'<span class="small-tags">%s</span>','')!!}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if(!empty(strtotime($project->startdate) && strtotime($project->enddate)))
                                                    <div class="item-list">
                                                        <span class="item-heading clearfix">{{trans('website.W0682')}}</span>
                                                        <span class="item-description">
                                                            <span class="f-b">{{___date_difference($project->startdate, $project->enddate)}}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>                                        
                                    </div>
                                    {!!___e(($project->description))!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('inlinescript')
    <style type="text/css">
        .btn-green, .button {
            width: auto;
            display: inline-block;
            float: right;
        }
        .container.public-job-detail {
            clear: both;
        }
        .public-job-detail{
            margin-top: 30px;
        }   
        .user-profile-links li.active {
            background: #f7f7f7;
            display: block;
        }
        .draw-underline {
            border-bottom: 1px solid #d1d3d5!important;
            padding: 10px 0;
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