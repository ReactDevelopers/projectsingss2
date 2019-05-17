@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body">
                        <img class="profile-user-img img-responsive img-circle" src="{{$picture}}" />
                        <h3 class="profile-username text-center">{{$user_details['name']}}</h3>
                        <p class="text-muted text-center">{{$user_details['company_name']}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <div class="nav-tabs-custom no-margin">
                        <ul class="nav nav-tabs">
                            <li class="<?php echo ($page == 'details')?'active':''; ?>"><a href="<?php echo $url.'details'; ?>">Details</a></li>
                            <li class="<?php echo ($page == 'chat')?'active':''; ?>"><a href="<?php echo $url.'chat'; ?>">Chat</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane <?php echo ($page == 'details')?'active':''; ?>">
                                @if($page == 'details')
                                    <div class="tab-pane<?php echo ($page == 'details')?' active':''; ?>">{!! $html->table(); !!}
									</div>
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'chat')?'active':''; ?>">
                                @if($page == 'chat')
                                    @include('backend.reportabuse.chat')
                                @endif
                            </div>                           
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="{{url('administrator/report-abuse')}}" class="btn btn-default">Back</a>
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
    @if($page == 'details' || $page == 'transactions')
        {!! $html->scripts() !!}
    @endif
@endsection
