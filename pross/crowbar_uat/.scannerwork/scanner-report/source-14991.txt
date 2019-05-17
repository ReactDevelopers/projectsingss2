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
                <div class="container top-margin-20px">                    
                    <div class="login-inner-wrapper top-margin-20px">
                        <div class="row has-vr">
                            <div class="text-center">
                                <h2 class="form-heading">
                                    {!! $message !!}
                                </h2>
                                <div class="col-md-12">
                                    @php $agent = new Jenssegers\Agent\Agent; @endphp
                                    @if($agent->isMobile())
                                        <a href="crowbar://">&larr; {{trans('website.W0164')}}</a>
                                    @else
                                        <a href="{{ url('/login') }}">&larr; {{trans('website.W0164')}}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection
