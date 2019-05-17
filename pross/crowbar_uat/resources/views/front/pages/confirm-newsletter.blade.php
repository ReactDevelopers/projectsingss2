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
        <div class="contentWrapper top-margin-20px">
            <section class="login-section top-margin-20px">
                <div class="container top-margin-20px">
                    <div class="top-margin-20px">                    
                        <div class="login-inner-wrapper top-margin-20px">
                            <div class="row has-vr">
                                @php $agent = new Jenssegers\Agent\Agent; @endphp
                                @if(empty(\Session::get('success')))
                                    @if($link_status !== 'expired')
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
                                    @else
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
                                    @endif
                                @else
                                    <div class="text-center">
                                        <h4 class="form-heading blue-text">
                                            {!! str_replace("×","",strip_tags(\Session::get('alert'),'<br>')) !!}
                                        </h4>
                                        <div class="col-md-12">
                                            <a href="{{ url('/') }}">&larr; Back to home page</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection
