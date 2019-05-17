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
                    <h4>Help Center</h4>
                </div>
            </div>
        </div>
    </section>
    <section class="search-box-wrapper">
        <form action="#" method="POST" class="form-horizontal">
            <div class="search-banner">
                <div class="container form-horizontal">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" value="{{ \Request::get('_search') }}" name="search" placeholder="Search" class="form-control" />
                            <input type="text" name="__search" class="hide" value="{{ \Request::get('_search') }}"/>
                            <button type="button" data-request="search" class="button">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <section>
        <div class="container">
            <ul class="breadcrum">
                <li><a href="{{ url('page/faq') }}">Crowbar Help Centre</a></li>
                <li><a href="{{ url('page/faq') }}">{{ $faq['postcategory']['postcategory']['description']['title'] }}</a></li>
                <li><a href="javascript:void(0);" class="current">{{ $faq['postcategory']['description']['title'] }}</a></li>
            </ul>
            <div class="faq-list-detail">
                <div class="row">
                    <div class="col-md-7 col-sm-7 col-xs-12">
                        <h3 class="form-heading">{{ $faq['description']['title'] }}</h3>
                        {!! $faq['description']['description'] !!}
                        <div class="voting-section">
                            <p class="like-dislike">
                                <span>Was this answer helpful?</span>
                                <a href="javascript:void(0)" data-request="like-dislike" data-value="like" data-url="{{ url(sprintf('like-dislike?faq_id=%s',___encrypt($faq['id_faq']))) }}"  data-target="#response-txt" data-inactive="dislike" class="like {{ $faq['faq_response_by_ip']['response'] == 'like' ? 'active' : ''}}"></a>
                                <a href="javascript:void(0)" data-request="like-dislike" data-value="dislike" data-url="{{ url(sprintf('like-dislike?faq_id=%s',___encrypt($faq['id_faq']))) }}" data-target="#response-txt" data-inactive="like" class="dislike {{ $faq['faq_response_by_ip']['response'] == 'dislike' ? 'active' : ''}}"></a>
                                <span class="grey-text" id="response-txt">{{ sprintf(trans('website.W0686'),$faq['faq_response_count_count'],$faq['faq_response_count']) }}</span>
                            </p>
                            <p>
                                <span>Have more questions?</span>
                                <a href="{{ url('/page/contact') }}" class="red-link">Submit a request</a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        @if(!empty($related_faq))
                            <div class="grey-sidebar">
                                <h6 class="small-subheading">Related questions</h6>
                                <ul class="related-topics">
                                    @foreach($related_faq as $key => $value)
                                        <li><a href="{{ url(sprintf('page/faq-detail?faq=%s',___encrypt($value['id_faq'])))}}">{{ $value['description']['title'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
    