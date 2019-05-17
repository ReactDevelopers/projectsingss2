@extends('layouts.blank')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
        <link href="{{ asset('css/easy-responsive-tabs.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.nstSlider.css') }}" rel="stylesheet">
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
        <script src="{{ asset('js/jquery-ui.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/easyResponsiveTabs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery.nstSlider.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                        <h4>{{$title}}</h4>
                    </div>                    
                </div>
            </div>
        </div>

    	<div class="contentWrapper" style="text-align: center;">
            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
            	<h5>Payment {{ucfirst(\Request::get('status'))}}.</h5>
            	<span class="plan-main-heading">Please wait. We are redirecting you back. <br/> Or click on the link below.</span>
				<div>
					<a style="color:#d41556;" id="paypal_click_link" href="{{('crowbar://payment_status='.(\Request::get('status') == 'success'?'success':'fail'))}}">Go Back</a>
				</div>
            </div>
        </div>
    @endsection

    @push('inlinescript')
    <script>
    	$(document).ready(function(){
			setTimeout(function(){
				$('#paypal_click_link')[0].click();
			},4000);
			/*Disable link after 5 mins.*/
			setTimeout(function(){
				$('#paypal_click_link').bind('click', false);
			}, 5 * 60 * 1000);
		});
    </script> 
    @endpush