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

    @if(!empty($banner['how-it-works'][1]))
        <section class="about-banner-section">
            <img src="{{ asset("uploads/banner/".$banner['how-it-works'][1]->banner_image) }}">
        </section>
    @endif

    <section class="red-bar-box">
        <div class="container">
            <a href="{{url('/')}}" class="btn btn-white-border">{{trans('website.W0151')}}</a>
            <h3>{{trans('website.W0502')}}</h3>
        </div>
    </section>
    
    @if(!empty($banner['how-it-works'][1]))
        <section class="about-white-box">
            <div class="container">
                <div class="about-description">
                    <p>{!!nl2br($banner['how-it-works'][1]->banner_text)!!}</p>
                </div>
            </div>
        </section>
    @endif
    
    <section class="about-tab-section">
        <ul class="about-tabs">
            <li>
                <a href="{{ url('/page/how-it-works?section=get-hired') }}" @if(\Request::get('section') == 'get-hired') class="active" @endif @if(empty(\Request::get('section'))) class="active" @endif id="get-hired">Get Hired</a>
            </li>
            <li>
                <a href="{{ url('/page/how-it-works?section=hire-talent') }}" @if(\Request::get('section') == 'hire-talent') class="active" @endif id="hire-talent">Hire Talent</a>
            </li>
            <li>
                <a href="{{ url('/page/faq') }}">FAQs</a>
            </li>
        </ul>
        @if(\Request::get('section') == 'hire-talent')
            @includeIf('front.includes.hiretalent')
        @else
            @includeIf('front.includes.gethired')
        @endif
    </section>
@endsection


@push('inlinescript')
    <script type="text/javascript">
        $(function(){
            @if(!empty(\Request::get("section")))
                $('html, body').animate({
                    scrollTop: ($('#{{\Request::get("section")}}').offset().top)
                }, 500);
            @endif
        });
    </script>
@endpush
