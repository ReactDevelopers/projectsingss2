@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        @if(!empty(\Auth::guard('admin')->user()->type) && \Auth::guard('admin')->user()->type == 'superadmin')
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_projects']}}</h3>
                            <p>Total Projects</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-globe"></i>
                        </div>
                        <a href="{{url(sprintf('%s/project/listing',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_talents']}}</h3>
                            <p>Total Talents</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{url(sprintf('%s/users/talent',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_employers']}}</h3>
                            <p>Total Employers</p>
                        </div>
                        <div class="icon">
                            <i class="ion-ios-briefcase"></i>
                        </div>
                        <a href="{{url(sprintf('%s/users/employer',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_disputes']}}</h3>
                            <p>Total Disputes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bell"></i>
                        </div>
                        <a href="{{url(sprintf('%s/raise-dispute',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-aqua">
                            <h3 class="no-margin widget-user-username">Recent Projects</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_projects'] as $item)
                                    <li><a>{{substr($item->title,0,12)}} <span class="pull-right badge bg-black">{{___format($item->price,true,true)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/project/listing',ADMIN_FOLDER))}}">See all projects</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-green">
                            <h3 class="no-margin widget-user-username">Recent Talents</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_talents'] as $item)
                                    <li><a>{{substr($item->first_name.' '.$item->last_name,0,12)}} <span class="pull-right badge bg-black">{{___ago($item->created)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/users/talent',ADMIN_FOLDER))}}">See all talents</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-yellow">
                            <h3 class="no-margin widget-user-username">Recent Employers</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_employers'] as $item)
                                    <li><a>{{substr($item->first_name.' '.$item->last_name,0,12)}} <span class="pull-right badge bg-black">{{___ago($item->created)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/users/employer',ADMIN_FOLDER))}}">See all employers</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-purple">
                            <h3 class="no-margin widget-user-username">Recent Disputes</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_dispute'] as $item)
                                    <li>
                                        <a>{{substr($item->title,0,12)}} 
                                        <span class="pull-right badge bg-black">{{___ago($item->last_updated)}}</span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/raise-dispute',ADMIN_FOLDER))}}">See all disputes</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2nd Row --}}
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_contacts']}}</h3>
                            <p>Contacts</p>
                        </div>
                        <div class="icon">
                            <i class="ion-ios-email-outline"></i>
                        </div>
                        <a href="{{url(sprintf('%s/messages/inbox',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-red">
                            <h3 class="no-margin widget-user-username">Recent Contacts</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_contacts'] as $item)
                                    <li><a>{{substr($item->message_content,0,12)}} <span class="pull-right badge bg-black">{{___ago($item->created)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/messages/inbox',ADMIN_FOLDER))}}">See all contacts</a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 2nd Row --}}

        @elseif(!empty(\Auth::guard('admin')->user()->type) && \Auth::guard('admin')->user()->type == 'sub-admin')
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_abuses']}}</h3>
                            <p>Total Abuses</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <a href="{{url(sprintf('%s/report-abuse',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{$data[\Auth::guard('admin')->user()->type]['total_dispute']}}</h3>
                            <p>Total Disputes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-road"></i>
                        </div>
                        <a href="{{url(sprintf('%s/raise-dispute',ADMIN_FOLDER))}}" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-aqua">
                            <h3 class="no-margin widget-user-username">Recent Abuses</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_abuses'] as $item)
                                    <li><a>{{substr($item->title,0,12)}} <span class="pull-right badge bg-black">{{___ago($item->created)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/report-abuse',ADMIN_FOLDER))}}">See all abusess</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-green">
                            <h3 class="no-margin widget-user-username">Recent Disputes</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                @foreach($data[\Auth::guard('admin')->user()->type]['recent_dispute'] as $item)
                                    <li><a>{{substr($item->title,0,12)}} <span class="pull-right badge bg-black">{{___ago($item->created)}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="{{url(sprintf('%s/raise-dispute',ADMIN_FOLDER))}}">See all raised disputes</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection