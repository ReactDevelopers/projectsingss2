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
        <section class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{!! $page['title'] !!}</h4>
                    </div>
                </div>
            </div>
        </section>
        <div class="contentWrapper">
            <section class="staticSectionWrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="aboutContent">
                                @if(Request::segment(3) == 'privacy-policy')
                                    <div class="privacy-policy-img">
                                        <img src="{{asset('images/trust_certificate.png')}}" / >
                                    </div>
                                @endif
                                {!! $page['content'] !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    @endsection
