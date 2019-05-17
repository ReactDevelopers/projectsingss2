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
                        <div class="col-md-12 col-sm-12 col-xs-12">                            
                            <form method="POST" action="{{ url(sprintf('/_verifyforgotpassword?token=%s',$token)) }}" class="form-horizontal" autocomplete="off">
                                <div class="login-inner-wrapper">
                                    <h2 class="form-heading">{{trans('website.W0640')}}</h2>
                                    {{ csrf_field() }}
                                    <p>{{trans('general.M0029')}}</p>
                                    <br>
                                    <div class="row">
                                        
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <div class="message">
                                                {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
                                            </div>
                                            
                                            <div class="form-group has-feedback toggle-social{{ $errors->has('verification_code') ? ' has-error' : '' }}">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input name="{{ 'verification_code' }}" value="{{ old('verification_code',(!empty(${'verification_code'}))?${'verification_code'}:'') }}" type="test" class="form-control" placeholder="{{trans('website.W0195')}}">
                                                    @if ($errors->has('verification_code'))
                                                        <span class="help-block">{{ $errors->first('verification_code') }}</span>
                                                    @endif
                                                </div>
                                            </div>                                    
                                            
                                            <div class="form-group button-group">
                                                <div class="col-md-8 col-sm-12 col-xs-12">
                                                    <p class="resend-link text-left p-tb-7">{{trans('website.W0641')}} {!!sprintf(trans('website.W0642'),url('forgot/password'))!!}</p>
                                                </div>
                                                <div class="col-md-4 col-sm-12 col-xs-12">
                                                    <div class="row">                                        
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <button type="submit" class="btn btn-sm redShedBtn">{{trans('website.W0013')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                              
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection