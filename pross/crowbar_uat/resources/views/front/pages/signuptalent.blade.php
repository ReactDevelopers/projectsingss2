@extends('layouts.front.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/owl.theme.default.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="contentWrapper">  
            <section class="login-section signuphire-section">
                <div class="container">             
                    <div class="login-inner-wrapper">   
                        <div class="row has-vr">
                            <div class="col-md-6 col-sm-6 col-xs-12 form-left">                            
                                <h4 class="form-heading">{{trans('website.W0125')}}</h4>
                                <div class="message">
                                    {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }} 
                                </div>          
                                <form method="POST" action="{{ url('/signup/talent/process') }}" class="form-horizontal login-form" autocomplete="off">
                                {{ csrf_field() }}
                                    <input type="hidden" value="{{ Request::get('token') }}" name="remember_token" />
                                    <div class="form-group has-feedback{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0142')}}</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input name="first_name" value="{{ old('first_name',(!empty($social['social_first_name'])?$social['social_first_name']:''))}}" type="text" class="form-control">
                                            @if ($errors->has('first_name'))
                                                <span class="help-block">{{ $errors->first('first_name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0143')}}</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input name="last_name" value="{{ old('last_name',(!empty($social['social_last_name'])?$social['social_last_name']:''))}}" type="text" class="form-control">
                                            @if ($errors->has('last_name'))
                                                <span class="help-block">{{ $errors->first('last_name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0144')}}</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input name="email" value="{{ old('email',(!empty($social['social_email'])?$social['social_email']:''))}}" type="text" class="form-control">
                                            @if ($errors->has('email'))
                                                <span class="help-block">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback toggle-social{{ $errors->has('password') ? ' has-error' : '' }}{{ (!empty($social['social_agree']))?' hide':'' }}">
                                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0145')}}</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input name="password" type="password" class="form-control" autocomplete="off">
                                            @if ($errors->has('password'))
                                                <span class="help-block">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback toggle-social{{ $errors->has('confirm_password') ? ' has-error' : '' }}{{ (!empty($social['social_agree']))?' hide':'' }}">
                                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0146')}}</label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input name="confirm_password" type="password" class="form-control" autocomplete="off">
                                            @if ($errors->has('confirm_password'))
                                                <span class="help-block">{{ $errors->first('confirm_password') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!empty($social))
                                        <div class="form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="checkbox small-checkbox">                
                                                    <input name="social_agree" type="checkbox" id="social_agree"{{ (!empty($social['social_agree']))?' checked="checked"':''  }}>
                                                    <label for="social_agree" data-request="toggle-class" data-target=".toggle-social" data-id='[name="social_agree"]'>
                                                        <span class="check"></span>
                                                        {!! sprintf(trans('website.W0004'),(!empty($social['social_key']))?ucfirst(str_replace("_id", "", $social['social_key'])):"") !!}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="checkbox small-checkbox">                
                                                <input name="agree" type="checkbox" id="agree"{{ (!empty(old('agree')))?' checked="checked"':''  }}>
                                                <label for="agree">
                                                    <span class="check"></span>
                                                    {!!
                                                        sprintf(
                                                            trans('website.W0149'),
                                                            "<a class='underline' target='_blank' href='".url('/page/terms-and-conditions')."'>".trans('website.W0147')."</a>",
                                                            "<a class='underline' target='_blank' href='".url('/page/privacy-policy')."'>".trans('website.W0148')."</a>"
                                                        )
                                                    !!}
                                                </label>
                                            </div>
                                        </div>
                                    </div>                                
                                    <div class="form-group submit-form-btn text-right">
                                        <div class="col-sm-12 col-xs-12">
                                            <input type="hidden" name="social_key" value="{{ (!empty($social['social_key']))?$social['social_key']:"" }}" />
                                            <input type="hidden" name="social_id" value="{{ (!empty($social['social_id']))?$social['social_id']:"" }}" />
                                            <input type="hidden" name="name" value="{{ (!empty($social['social_name']))?$social['social_name']:"" }}" />
                                            <input type="hidden" name="picture" value="{{ (!empty($social['social_picture']))?$social['social_picture']:"" }}" />
                                            <input type="hidden" name="country" value="{{ (!empty($social['social_country']))?$social['social_country']:"" }}" />
                                            <input type="hidden" name="gender" value="{{ (!empty($social['social_gender']))?$social['social_gender']:"" }}" />
                                            <button type="submit" class="btn btn-sm redShedBtn">{{trans('website.W0151')}}</button>
                                        </div>
                                    </div>                                
                                </form>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 form-right">
                                <h4 class="form-heading">{{trans('website.W0643')}}</h4>                            
                                <!-- <p>{{trans('website.W0129')}}</p> -->
                                <ul class="loginOptions">
                                    <li><a href="{{ asset('/login/linkedin') }}" class="linkedin-option"><span>{{sprintf(trans('website.W0130'),trans('website.W0120'))}}</span></a></li>
                                    <li><a href="{{ asset('/login/facebook') }}" class="facebook-option"><span>{{sprintf(trans('website.W0130'),trans('website.W0116'))}}</span></a></li>
                                    <li><a href="{{ asset('/login/instagram') }}" class="instagram-option"><span>{{sprintf(trans('website.W0130'),trans('website.W0131'))}}</span></a></li>
                                    <li><a href="{{ asset('/login/twitter') }}" class="twitter-option"><span>{{sprintf(trans('website.W0130'),trans('website.W0119'))}}</span></a></li>
                                </ul>
                            </div>
                            <div class="vertical-divison"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection

    @push('inlinescript')
        <script type="text/javascript">$('[name="password"]').hidePassword(true);</script>
    @endpush