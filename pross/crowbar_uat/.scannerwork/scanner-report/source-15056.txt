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
        <div class="greyBar-Heading invite-talent-heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{$title}}</h4>
                    </div>                    
                </div>
            </div>
        </div>

        @includeIf($view)
                        
    @endsection

   
    @push('inlinescript')
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url: '{{url(TALENT_ROLE_TYPE.'/talent-connect/validate-talent/')}}', 
                    type: 'post', 
                    success: function($response){
                        if($response.message){
                            swal({
                            title: $alert_message_text,
                            html: $response.message,
                            showLoaderOnConfirm: false,
                            showCancelButton: false,
                            showCloseButton: false,
                            allowEscapeKey: false,
                            allowOutsideClick:false,
                            customClass: 'swal-custom-class',
                            confirmButtonText: $close_botton_text,
                            cancelButtonText: $cancel_botton_text,
                            preConfirm: function (res) {
                                return new Promise(function (resolve, reject) {
                                    if (res === true) {
                                        if($response.redirect){
                                            window.location = $response.redirect;
                                        }              
                                    }
                                    resolve();
                                })
                            }
                            }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                        }
                    }
                });
            });
        </script>
    @endpush