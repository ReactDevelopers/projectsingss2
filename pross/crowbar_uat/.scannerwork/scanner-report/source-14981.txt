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
            <div class="greyBar-Heading">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>{{trans('website.W0133')}}</h4>
                        </div>
                    </div>
                </div>
            </div>  
            <section class="login-section m-t-15">
                <div class="container">                    
                    <div class="row has-vr">
                        <div class="col-md-12 col-sm-12 col-xs-12">                            
                            <form method="POST" action="{{ url(sprintf('/_forgotpassword')) }}" class="form-horizontal" autocomplete="off">
                                <div class="login-inner-wrapper">
                                    <h2 class="form-heading" style="line-height:32px;">{{trans('website.W0161')}}</h2>
                                    {{ csrf_field() }}
                                    <p>{{trans('website.W0162')}}</p>
                                    <p>{{trans('website.W0163')}}</p>
                                    <br>
                                    <div class="message">
                                        {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            
                                            <div class="form-group has-feedback toggle-social{{ $errors->has(LOGIN_EMAIL) ? ' has-error' : '' }}">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input name="{{ LOGIN_EMAIL }}" value="{{ old(LOGIN_EMAIL,(!empty(${LOGIN_EMAIL}))?${LOGIN_EMAIL}:'') }}" type="test" class="form-control" placeholder="{{trans('website.W0144')}}">
                                                    @if ($errors->has(LOGIN_EMAIL))
                                                        <span class="help-block">{{ $errors->first(LOGIN_EMAIL) }}</span>
                                                    @endif
                                                </div>
                                            </div>                                    
                                            
                                            <div class="form-group button-group">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="row form-btn-set">                                        
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