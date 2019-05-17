@extends('layouts.front.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        
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
        <div class="contentWrapper">  
            <section class="login-section">
                <div class="container">                    
                    <div class="row has-vr">
                        @if(empty(\Session::get('success')))
                            @if($link_status !== 'expired')
                                <div class="col-md-12 col-sm-12 col-xs-12">                       
                                    {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
                                    <form method="POST" action="{{ url(sprintf('/_resetpassword?token=%s',$token)) }}" class="form-horizontal" autocomplete="off">
                                        {{ csrf_field() }}
                                        <div class="login-inner-wrapper">
                                            <h2 class="form-heading">{{trans('website.W0165')}}</h4>
                                            <div class="form-group has-feedback toggle-social{{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0166')}}</label>
                                                <div class="col-md-5 col-sm-5 col-xs-12">
                                                    <input name="{{ 'password' }}" value="{{ old('password',(!empty(${'password'}))?${'password'}:'') }}" type="password" class="form-control" placeholder="{{trans('website.W0166')}}">
                                                    @if ($errors->has('password'))
                                                        <span class="help-block">{{ $errors->first('password') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group button-group">
                                                <div class="col-md-5 col-sm-5 col-xs-12">
                                                    <div class="row form-btn-set">      
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <button type="submit" class="btn btn-sm redShedBtn">{{trans('website.W0013')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                  
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    
                                                </div>
                                            </div>                                
                                        </div>                                
                                    </form>
                                </div>
                            @else
                                <div class="top-margin-20px">
                                    <br>                    
                                    <br>                    
                                    <div class="top-margin-20px">                    
                                        <div class="login-inner-wrapper top-margin-20px">
                                            <div class="text-center">
                                                <h4 class="form-heading blue-text">
                                                    {!! $message !!}
                                                </h4>
                                                <div class="col-md-12">
                                                    @if($agent->isMobile())
                                                        <a href="crowbar://">&larr; {{trans('website.W0164')}}</a>
                                                    @else
                                                        <a href="{{ url('/') }}">&larr; {{trans('website.W0164')}}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="top-margin-20px">
                                <br>                    
                                <br>                    
                                <div class="top-margin-20px">                    
                                    <div class="login-inner-wrapper top-margin-20px">
                                        <div class="text-center">
                                            <h4 class="form-heading blue-text">
                                                {!! str_replace("×","",strip_tags(\Session::get('alert'),'<br>')) !!}
                                            </h4>
                                            <div class="col-md-12">
                                                <a href="{{ url('/') }}">&larr; Back to home page</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    @endsection
@push('inlinescript')
    <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
    <script type="text/javascript">$('[name="{{ 'password' }}"]').hidePassword(true);</script>
@endpush