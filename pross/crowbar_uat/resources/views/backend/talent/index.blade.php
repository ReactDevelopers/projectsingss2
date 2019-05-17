@extends('layouts.backend.dashboard')

@section('requirecss')
    <link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/crop.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body box-profile">
                        <div class="image-circle">
                            <div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="190" data-height="190" data-folder="{{TALENT_PROFILE_PHOTO_UPLOAD}}" data-record="0" data-column="profile" style="background: url('{{ $picture }}') no-repeat center center;background-size:190px 190px;"></div>
                        </div>
                        <h3 class="profile-username text-center">{{$user['first_name']}} {{$user['last_name']}}</h3>
                        <p class="text-muted text-center">{{ucfirst($user['type'])}}</p>
                        <p class="text-center">
                            @if($user['remuneration'])
                                @foreach($user['remuneration'] as $s)
                                    @if($s['interest'] != 'fixed')
                                        {{___cache('currencies')[$user['currency']]}}{{$s['workrate']}}/{{substr($s['interest'],0,1)}}&nbsp;
                                    @else
                                        <br>{{___cache('currencies')[$user['currency']]}}{{$s['workrate']}} <span class="small-tags" style="padding:0 5px;">{{$s['interest']}}</span>
                                    @endif
                                @endforeach
                            @endif
                        </p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Last Activity</b> <a class="pull-right">{{___d(date('Y-m-d', strtotime($user['last_login'])))}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Registered On</b> <a class="pull-right">{{___d(date('Y-m-d', strtotime($user['created'])))}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <div class="nav-tabs-custom no-margin">
                        <ul class="nav nav-tabs">
                            <li class="<?php echo ($page == '')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,''); ?>">Basic</a></li>
                            <li class="<?php echo ($page == 'industry')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=industry'); ?>">Indusrty & Skills</a></li>
                            <li class="<?php echo ($page == 'hiring')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=hiring'); ?>">Availability</a></li>
                            <li class="<?php echo ($page == 'verify')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=verify'); ?>">Verification</a></li>
                            @if(0)
                                <li class="<?php echo ($page == 'talent_answer')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=talent_answer'); ?>">Answers</a></li>
                            @endif
                            <li class="<?php echo ($page == 'portfolio')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=portfolio'); ?>">Portfolio</a></li>

                            <li class="<?php echo ($page == 'activity_log')?'active':''; ?>"><a href="<?php echo url(sprintf('%s/users/talent/edit/activity_log?user_id=%s', ADMIN_FOLDER, ___encrypt($id_user))); ?>">Activity Log</a></li>
                            @if(!empty($companydata))
                            <li class="<?php echo ($page == 'firm_detail')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=firm_detail'); ?>">Firm Detail</a></li>
                                @if(!empty($isOwner))
                                    <li class="<?php echo ($page == 'connected_talent')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=connected_talent'); ?>">Connected Talent</a></li>
                                @endif
                            @endif
                        </ul>
                        <div class="tab-content no-padding">
                            <div class="tab-pane <?php echo ($page == '')?'active':''; ?>">
                                @if($page == '')
                                @include('backend.talent.basic')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'industry')?'active':''; ?>">
                                @if($page == 'industry')
                                @include('backend.talent.industry')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'hiring')?'active':''; ?>">
                                @if($page == 'hiring')
                                @include('backend.talent.availability')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'verify')?'active':''; ?>">
                                @if($page == 'verify')
                                @include('backend.talent.verification')
                                @endif
                            </div>
                            @if(0)
                                <div class="tab-pane <?php echo ($page == 'talent_answer')?'active':''; ?>">
                                    @if($page == 'talent_answer')
                                    @include('backend.talent.interviews')
                                    @endif
                                </div>
                            @endif
                            <div class="tab-pane <?php echo ($page == 'portfolio')?'active':''; ?>">
                                @if($page == 'portfolio')
                                @include('backend.talent.portfolio')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'activity_log')?'active':''; ?>">
                                @if($page == 'activity_log')
                                @include('backend.talent.activity_log')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'firm_detail')?'active':''; ?>">
                                @if($page == 'firm_detail')
                                @include('backend.talent.firm_detail')
                                @endif
                            </div>
                            <div class="tab-pane <?php echo ($page == 'connected_talent')?'active':''; ?>">
                                @if($page == 'connected_talent')
                                @include('backend.talent.connected_talent')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('inlinescript')
<script src="{{ asset('js/cropper.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $('select').trigger('change');

        $(".cropper").SGCropper({
            viewMode: 1,
            aspectRatio: "2/3",
            cropBoxResizable: false,
            formContainer:{
                actionURL:"{{ url(sprintf('ajax/crop?imagename=image&user_id=%s',$user['id_user'])) }}",
                modelTitle:"{{ trans('website.W0261') }}",
                modelSuggestion:"{{ trans('website.W0263') }}",
                modelDescription:"{{ trans('website.W0264') }}",
                modelSeperator:"{{ trans('website.W0265') }}",
                uploadLabel:"{{ trans('website.W0266') }}",
                fieldLabel:"",
                fieldName: "image",
                btnText:"{{ trans('website.W0262') }}",
                defaultImage: base_url+"/images/product_sample.jpg",
                loaderImage: base_url+"/images/loader.gif",
            }
        });
    </script>
@endpush
