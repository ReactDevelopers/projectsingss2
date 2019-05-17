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
        <!-- Banner Section -->
        @if(Request::get('stream') != 'mobile')
            @includeIf('front.includes.banner')
        @endif
        <section class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{!! $subpage['title'] !!}</h4>
                    </div>
                </div>
            </div>
        </section>
        <div class="contentWrapper">
            <div class="aboutStartedSection">
                <div class="container">
                    <div class="row">
                        <div class="col-md-2 col-sm-2 col-xs-2"></div>
                        <div class="col-md-8 col-sm-8 col-xs-8">
                            <div class="aboutContent text-center">
                                {!! $subpage['content'] !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @includeIf('front.includes.social') 
        </div>
    @endsection