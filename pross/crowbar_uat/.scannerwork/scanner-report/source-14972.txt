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

        @if($is_payment_already_captured == 1)
        	<div class="contentWrapper" style="text-align: center;">
	            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
	            	<span class="plan-main-heading">{{ trans('general.M0502') }}</span>
	            </div>
	        </div>
        @else
	        <div class="contentWrapper" style="text-align: center;">
	            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
	            		<span class="plan-main-heading">{{ trans('website.W0363') }}</span>
	                	<span>
                        {{str_replace('$', $currency, ___formatDefault($quoted_price,true,true)) }}</span>
	                	<br/>
	                	<br/>
	                    <span class="total-plan-heading">{{ trans('website.W0362') }}</span>
	                    <span class="total-plan-heading">{{ 
                            str_replace('$', $currency, ___formatDefault($transaction_total,true,true)) }}</span>
	                    <input type="hidden" name="transaction_id" value="{{$transaction_id}}">
	                    <input type="hidden" name="project_id" value="{{$project_id}}">
	                    <input type="hidden" name="proposal_id" value="{{$proposal_id}}">
	                    <input type="hidden" name="user_id" value="{{$user_id}}">
	                    <br/>
	                    <br/>
	                    <div id="paypal-button"></div>
	            </div>
	        </div>
        @endif
    @endsection

    @push('inlinescript')
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script>
        paypal.Button.render({
        env: '{{env('PAYPAL_ENV')}}', // 'sandbox' or 'production'
        style: {
            layout: 'vertical',  // horizontal | vertical
            size:   'medium',    // medium | large | responsive
            shape:  'rect',      // pill | rect
            color:  'gold'       // gold | blue | silver | black
        },
        commit:true,
        // Set up the payment:
        // 1. Add a payment callback
        payment: function(data, actions) {
        // 2. Make a request to your server
            return paypal.request({
                method: 'post',
                url: "{{url('/payment/mobile-create-payment')}}",
                headers: {
                    'x-csrf-token': "{{csrf_token()}}",
                },
                'data':{
                		'transaction_id': $('input[name="transaction_id"]').val(),
                		'user_id'       : $('input[name="user_id"]').val() 
                		}
            }).then(function(data) {
                return data.id;
            });


        },
        // Execute the payment:
        // 1. Add an onAuthorize callback
        onAuthorize: function(data, actions) {
          // 2. Make a request to your server
            return paypal.request({
                method: 'post',
                url: "{{url('/payment/mobile-execute-payment')}}",
                headers: {
                    'x-csrf-token': "{{csrf_token()}}",
                },
                'data':{
                		'paymentID'		 : data.paymentID,
                		'payerID'		 : data.payerID, 
                		'transaction_id' : $('input[name="transaction_id"]').val(), 
                		'project_id'	 : $('input[name="project_id"]').val(), 
                		'proposal_id'	 : $('input[name="proposal_id"]').val(),
                		'user_id'        : $('input[name="user_id"]').val() 
                		}
            }).then(function(response) {

                if(response.status == true){
                	window.location.href = response.redirect_url;
                }else{
                	window.location.href = response.redirect_url;
                }
                
            });

        }
      }, '#paypal-button');
    </script> 
    @endpush