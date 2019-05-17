@extends('layouts.front.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/owl.theme.default.min.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="welcome-section">
            <div class="container">
                <div class="tile-sec">
                    <div class="tile-registeration-form">
                        <form method="POST" role="signup" action="{{ url('/signup/none/process') }}" class="form-horizontal login-form" autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ Request::get('token') }}" name="remember_token" />
                            @if(empty($social))
                                <h2 class="light-heading white-color">{{trans('website.W0837')}}</h2>
                            @else
                                <h2 class="light-heading white-color">{{trans('website.W0838')}}</h2>
                            @endif
                            <div class="form-group has-feedback{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0142')}}@if ($errors->has('first_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('first_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="first_name" value="{{ old('first_name',(!empty($social['first_name'])?$social['first_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('first_name')}}">
                                </div>
                            </div>
                            <div class="form-group has-feedback{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0143')}}@if ($errors->has('last_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('last_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="last_name" value="{{ old('last_name',(!empty($social['last_name'])?$social['last_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('last_name')}}">
                                </div>
                            </div>
                            @if(0)
                                <div class="form-group has-feedback{{ $errors->has('company_name') ? ' has-error' : '' }}" id="company_name" style="display: none;">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0096')}}@if ($errors->has('company_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('company_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="company_name" value="{{ old('company_name')}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('company_name')}}">
                                    </div>
                                </div>
                            @endif
                            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0144')}}@if ($errors->has('email') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('email')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input name="email" value="{{ old('email',(!empty($social['social_email'])?$social['social_email']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('email')}}">
                                </div>
                            </div>
                            @if(empty($social['social_id']))
                                <div class="form-group has-feedback toggle-social{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0145')}} @if ($errors->has('password') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('password')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="password" type="password" class="form-control" autocomplete="off"  data-toggle="tooltip" title="{{$errors->first('password')}}">
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="login-type-radio">
                                        <div class="grouped-radio">
                                            <label class="get-hired">
                                                <input type="radio" name="work_type" value="individual" {{!empty(old('work_type')) ? (old('work_type') == "individual"? "checked='checked'": '' ) : "checked='checked'"}} data-request="show-hide" data-true-condition=".normal-section" data-false-condition=".company-name-section" data-condition="individual">
                                                <span>{{trans('website.W0942')}}</span>
                                            </label>                                        
                                        </div>
                                        <div class="grouped-radio">
                                            <label class="hire-talent">
                                                <input type="radio" name="work_type" value="company" {{ old('work_type') == "company" ? "checked='checked'" : '' }} data-request="show-hide" data-true-condition=".company-name-section" data-false-condition=".normal-section" data-condition="company">
                                                <span>{{trans('website.W0943')}}</span>
                                            </label>                                        
                                        </div>
                                    </div>                                
                                </div>
                            </div>  

                            <div class="company-name-section">
                                <div class="form-group has-feedback{{ $errors->has('company_name') ? ' has-error' : '' }}">
                                    <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0944')}} @if($errors->has('company_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('company_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="company_name" value="{{ old('company_name',(!empty($social['company_name'])?$social['company_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('company_name')}}">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="login-type-radio">
                                        <div class="grouped-radio">
                                            <label class="get-hired">
                                                <input type="radio" name="type" value="talent" {{!empty(old('type')) ? (old('type') == "talent"? "checked='checked'": '' ) : "checked='checked'"}} data-request="show-target" data-show="false" data-target="#company_name" data-form_action="{{ url('/signup/talent/process') }}" data-form_role='[role="signup"]'>
                                                <span>{{trans('website.W0651')}}</span>
                                            </label>                                        
                                        </div>
                                        <div class="grouped-radio">
                                            <label class="hire-talent">
                                                <input type="radio" name="type" value="employer" {{ old('type') == "employer" ? "checked='checked'" : '' }} data-request="show-target" data-show="true" data-target="#company_name" data-form_action="{{ url('/signup/employer/process') }}" data-form_role='[role="signup"]'>
                                                <span>{{trans('website.W0650')}}</span>
                                            </label>                                        
                                        </div>
                                    </div>                                
                                </div>
                            </div>  --}}
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <p class="policy-text">{!!trans('website.W0662')!!}</p>
                                </div>
                            </div>                                
                            <div class="form-group submit-form-btn text-center">
                                <div class="col-sm-12 col-xs-12">
                                    <input type="hidden" name="social_key" value="{{ (!empty($social['social_key']))?$social['social_key']:"" }}" />
                                    <input type="hidden" name="social_id" value="{{ (!empty($social['social_id']))?$social['social_id']:"" }}" />
                                    <input type="hidden" name="name" value="{{ (!empty($social['social_name']))?$social['social_name']:"" }}" />
                                    <input type="hidden" name="picture" value="{{ (!empty($social['social_picture']))?$social['social_picture']:"" }}" />
                                    <input type="hidden" name="country" value="{{ (!empty($social['social_country']))?$social['social_country']:"" }}" />
                                    <input type="hidden" name="gender" value="{{ (!empty($social['social_gender']))?$social['social_gender']:"" }}" />
                                    <button type="submit" class="btn btn-sm redShedBtn">{{trans('website.W0839')}}</button>
                                </div>
                            </div>                     
                        </form>
                        <a class="hide" data-target="#select-type" data-request="ajax-modal" data-url="{{ url('social/signup') }}" href="javascript:void(0);">Select Type</a>
                    </div>
                    
                </div>
            </div>   
        </div>
    @endsection

    @push('inlinecss')
        <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
        <style type="text/css">
            .tile-registeration-form{
                width: 35%;
                background: #1b262f;
                margin: 40px auto;
                float: none;
            }
            .tile-registeration-form form{
                width: 330px;
                margin: 0 auto;
            }
            .tile-registeration-form .submit-form-btn button{
                margin-top: 5px;
            }
        </style>
    @endpush
    @push('inlinescript')
        <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
        <script type="text/javascript">
            $('[name="password"]').hidePassword(true);
            $(document).ready(function(){
                @if(old('type') == "employer")
                    $('[data-request="show-target"]').trigger('change');
                @endif
            });
            $(document).on('change','[data-request="show-target"]',function(){
                var $this           = $(this);
                var $form_action    = $this.data('form_action');
                var $target         = $this.data('target');
                var $show           = $this.data('show');
                var $form_role      = $this.data('form_role');
                var $value          = $this.val();

                /*if($show == true){
                    $($target).show();
                    $this.closest('[role="signup"]').find('.signup-Options').hide();
                    $('.tile-registeration-form .submit-form-btn button').css('margin-top','26px');
                }else{
                    $($target).hide();
                    $this.closest('[role="signup"]').find('.signup-Options').show();
                    $('.tile-registeration-form .submit-form-btn button').css('margin-top','35px');
                }*/
                $($form_role).attr('action',$form_action);
            });
           
        </script>
    @endpush
