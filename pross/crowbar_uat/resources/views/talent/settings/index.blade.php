@extends('layouts.talent.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/easy-responsive-tabs.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.nstSlider.css') }}" rel="stylesheet">
        <link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
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
        <script src="{{ asset('js/cropper.min.js') }}" type="text/javascript"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        <script type="text/javascript">
            $(".cropper").SGCropper({
                viewMode: 1,
                aspectRatio: "2/3",
                cropBoxResizable: false,
                formContainer:{
                    actionURL:"{{ url(sprintf('ajax/crop?imagename=image&user_id=%s',Auth::user()->id_user)) }}",
                    modelTitle:"{{ trans('website.W0261') }}",
                    modelSuggestion:"{{ trans('website.W0263') }}",
                    modelDescription:"{{ trans('website.W0264') }}",
                    modelSeperator:"{{ trans('website.W0265') }}",
                    uploadLabel:"{{ trans('website.W0266') }}",
                    fieldLabel:"",
                    fieldName: "image",
                    btnText:"{{ trans('website.W0262') }}",
                    defaultImage: "../images/product_sample.jpg",
                    loaderImage: "../images/loader.gif",
                }
            });
        </script>
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{$title}}</h4>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                <div class="container">
                    <div class="row mainContentWrapper">
                        @includeIf('talent.viewprofile.includes.sidebar')
                        <div class="col-md-8 col-sm-8 col-xs-12 no-padding-xs">
                            @includeIf('talent.settings.includes.header',$user)
                            @includeIf($view)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
