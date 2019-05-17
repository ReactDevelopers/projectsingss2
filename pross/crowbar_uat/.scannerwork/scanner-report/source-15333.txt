@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body">
                        <img class="profile-user-img img-responsive img-circle" src="{{ url($project_detail['employer']['company_logo']) }}" />
                        <h3 class="profile-username text-center">{{$project_detail['employer']['name']}}</h3>
                        <p class="text-muted text-center">{{$project_detail['employer']['company_name']}}</p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Created</b> <span class="pull-right">{{___d($project_detail['created'])}}</span>
                            </li>
                            @if(!empty($project_detail['completedate']))
                                <li class="list-group-item">
                                    <b>Completed</b> <span class="pull-right">{{___d($project_detail['completedate'])}}</span>
                                </li>
                            @endif
                            @if(!empty($project_detail['closedate']))
                                <li class="list-group-item">
                                    <b>Closed</b> <span class="pull-right">{{___d($project_detail['closedate'])}}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <div class="nav-tabs-custom no-margin">
                        <ul class="nav nav-tabs">
                            <li class="<?php echo ($page == '')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,''); ?>">Detail</a></li>
                            <li class="<?php echo ($page == 'description')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=description'); ?>">Description</a></li>
                            <li class="<?php echo ($page == 'proposal')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=proposal'); ?>">Proposal</a></li>
                            <li class="<?php echo ($page == 'transactions')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=transactions'); ?>">Transactions</a></li>
                            <li class="<?php echo ($page == 'chat')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=chat'); ?>">Chat</a></li>
                            <li class="<?php echo ($page == 'activity_log')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=activity_log'); ?>">Activity Log</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane <?php echo ($page == '')?'active':''; ?>">
                                @if($page == '')
                                    @include('backend.project.project-info')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'description')?'active':''; ?>">
                                @if($page == 'description')
                                    @include('backend.project.project-description')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'proposal')?'active':''; ?>">
                                @if($page == 'proposal')
                                    {!! $html->table(); !!}
                                @endif
                            </div>
                            <div class="tab-pane table-responsive <?php echo ($page == 'transactions')?'active':''; ?>">
                                @if($page == 'transactions')
                                    {!! $html->table(); !!}
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'chat')?'active':''; ?>">
                                @if($page == 'chat')
                                    @include('backend.project.chat')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'activity_log')?'active':''; ?>">
                                @if($page == 'activity_log')
                                    @include('backend.project.activity_log')
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        @if(Request::get('slug') == 'project')
                            <a href="{{url('administrator/project/listing')}}" class="btn btn-default">Back</a>
                        @else
                            <a href="{{url('administrator/report')}}" class="btn btn-default">Back</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('requirecss')
@endsection

@section('requirejs')    
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    @if($page == 'proposal' || $page == 'transactions')
        {!! $html->scripts() !!}
    @endif
@endsection