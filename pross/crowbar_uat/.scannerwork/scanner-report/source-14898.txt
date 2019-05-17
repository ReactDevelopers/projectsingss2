@extends('layouts.employer.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <style>.education-box .edit-icon, .work-experience-box .edit-icon, [data-request="delete"]{display: none!important;}</style>
        <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/easy-responsive-tabs.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.nstSlider.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css') }}" type="text/css" media="screen" />
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/easyResponsiveTabs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery.nstSlider.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    @endsection
    
    @section('content')
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>
                            <span class="hire-me-title">{{$title}}</span>
                            <a class="hire-me" class="manage-cards" data-target="#hire-me" data-request="ajax-modal" data-url="{{ url(sprintf('%s/hire-talent?talent_id=%s',EMPLOYER_ROLE_TYPE,$talent_id)) }}" href="javascript:void(0);"><img src="{{ asset('images/add-white.png') }}">{{trans('website.W0137')}}</a>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                <div class="container">
                    <div class="row mainContentWrapper">
                        @includeIf('employer.talent.includes.sidebar')
                        <div class="col-md-8 col-sm-8 right-sidebar">
                            @includeIf($view)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade upload-modal-box add-payment-cards" id="hire-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    @endsection