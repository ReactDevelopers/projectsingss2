    @extends('layouts.employer.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <!-- <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet"> -->
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
                    uploadLabel:"{{ trans('website.W0267') }}",
                    fieldLabel:"",
                    fieldName: "image",
                    btnText:"{{ trans('website.W0262') }}",
                    defaultImage: "../images/product_sample.jpg",
                    loaderImage: "../../../../images/loader.gif",
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
                        @includeIf('employer.profile.includes.sidebar')
                        <div class="col-md-8 col-sm-8 right-sidebar">
                            <div class="navigation-bar">
                                <ul>
                                    <li class="@if(in_array('one',$steps)) {{ 'selected'}} @endif">
                                        <span class="navigation-stepCount">1</span>
                                        <a href="{{ url(sprintf('%s/profile/edit/one',EMPLOYER_ROLE_TYPE)) }}" title="{{ trans('website.W0020') }}" class="navigation-dot"></a>
                                    </li>
                                    <li class="@if(in_array('two',$steps)) {{ 'selected'}} @endif">
                                        <span class="navigation-stepCount">2</span>
                                        <a href="{{ url(sprintf('%s/profile/edit/two',EMPLOYER_ROLE_TYPE)) }}" title="{{ trans('website.W0021') }}" class="navigation-dot"></a>
                                    </li>
                                </ul>
                            </div>
                            @includeIf($view)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
