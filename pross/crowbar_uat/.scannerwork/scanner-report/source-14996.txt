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
        <!-- Main Content -->
        <div class="contentWrapper">  
            <section class="login-section">
                <div class="container top-margin-20px">
                    <div class="login-inner-wrapper top-margin-20px">                    
                        <div class="row has-vr">
                            @if(!empty($email))
                                <div class="col-md-7 col-sm-8 col-xs-12">                            
                                    <h4 class="form-heading blue-text">{{trans('website.W0168')}}</h4>
                                    <div class="verifiedAccount">
                                        <p>{{ str_replace('\n',' ',sprintf(trans('general.M0021'),$email)) }}</p>
                                        <a href="{{ sprintf('%s?token=%s',url('/'),\Request::get('token'))}}">{{trans('website.W0169')}}</a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <h4 class="form-heading blue-text">
                                        {!! str_replace("×","",strip_tags($alert,'<br>')) !!}
                                    </h4>
                                    <div class="col-md-12">
                                        @if($agent->isMobile())
                                            <a href="crowbar://">&larr; {{trans('website.W0164')}}</a>
                                        @else
                                            <a href="{{ url('/') }}">&larr; {{trans('website.W0164')}}</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection