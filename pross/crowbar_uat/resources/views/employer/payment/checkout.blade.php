<div class="contentWrapper job-listing-section employer-job-listing-payment">
    <div class="trnasferAmountList">
        <div class="no-padding-left clearfix">
            <div class="col-md-10 col-sm-12 col-xs-12 full-width-element">
                <form action="{{ url(sprintf('%s/payment/initiate',EMPLOYER_ROLE_TYPE)) }}" method="post">
                    <div class="top-margin-20px">{{ ___alert(\Session::get('alert')) }}</div>
                    <div class="login-inner-wrapper">
                        {{ csrf_field() }}
                        <h2 class="form-heading">{{ sprintf(trans('website.W0356'),$proposal['talent_name']) }}</h2>
                        <small class="small-heading"></small>
                        {{-- <div class="radio radio-inline">
                            <input type="radio" name="payment_type" checked value="card_payment" id="card">
                            <label for="card">Card Payment</label>
                        </div>
                        <div class="radio radio-inline">
                            <input type="radio" name="payment_type" value="express_payment" id="express">
                            <label for="express">Pay via Paypal</label>
                        </div> --}}
                        <ul>
                            <li>
                                <span class="plan-main-heading">{{ trans('website.W0363') }}</span>
                                <span>{{ ___format($proposal['quoted_price'],true,true) }}</span>
                            </li>
                            <li>
                                <div class="row">
                                    <div class="col-md-7 col-sm-12 col-xs-12" id="payment-checkout">
                                        {{-- <span class="plan-main-heading">{{trans('website.W0498')}}</span>
                                        @if(!empty($default_card_detail))
                                            <span>
                                                <a href="javascript:void(0);" class="manage-cards" data-target="#add-cards" data-request="ajax-modal" data-url="{{url(sprintf('%s/payment/card/manage',EMPLOYER_ROLE_TYPE))}}">{{trans('website.W0431')}}</a>
                                                <img class="selected-card" src="{{$default_card_detail['image_url']}}" />
                                                <strong>
                                                    {{
                                                        sprintf(
                                                            "%s%s",
                                                            wordwrap(str_repeat(".",strlen($default_card_detail['masked_number'])-4),4,' ',true),
                                                            $default_card_detail['last4']
                                                        )
                                                    }}
                                                </strong>
                                            </span>
                                        @else
                                            <span>
                                                <a class="manage-cards" style="left: 0;" data-target="#add-cards" data-request="ajax-modal" data-url="{{url(sprintf('%s/payment/card/manage',EMPLOYER_ROLE_TYPE))}}">{{trans('website.W0430')}}</a>
                                            </span>
                                        @endif --}}
                                    </div>
                                    <div class="col-md-5 col-sm-12 col-xs-12 plan-cost-block">
                                        <span class="total-plan-heading">{{ trans('website.W0362') }}</span>
                                        <span class="total-plan-price">{{ ___format( ($payment['transaction_total']),true,true) }}</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    {{-- <div class="form-group button-group row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="row form-btn-set">
                                <div class="col-md-7 col-sm-7 col-xs-6">
                                    <a class="greybutton-line" href="{{$back}}">{{ trans('website.W0355') }}</a>
                                </div>
                                <div class="col-md-6 col-sm-5 col-xs-6">
                                    <button type="submit" class="button" data-request="display-popup-on-post">{{ trans('website.W0364') }}</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <br/>
                    @if($checkPayoutMgmt)
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5 pull-right">   
                                <span class="total-plan-heading">{{ trans('website.W0939') }}</span>
                                <button type="button" data-request="inline-ajax-2" data-url="{{url('project/payout/mgmt')}}" class="button bottom-margin-10px" title="Accept">{{ trans('website.W0940') }}</button>
                            </div>
                        </div>
                    @else
                        <div id="paypal-button" style="float:right"></div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade upload-modal-box add-payment-cards" id="add-cards" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

@push('inlinescript')
    <style>.trnasferAmountList .right-sidebar{width: inherit;}</style>
    <script>
        function credit_card_number_format(value) {
            var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
            var matches = v.match(/\d{4,16}/g);
            var match = matches && matches[0] || ''
            var parts = []
            for (i=0, len=match.length; i<len; i+=4) {
                parts.push(match.substring(i, i+4))
            }
            
            if (parts.length) {
                return parts.join(' ')
            } else {
                return value
            }
        }

        $(document).on('keypress','#credit_card_number',function(){
            $(this).val(credit_card_number_format($(this).val()));
        });
    </script>
    
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
                url: "{{url('/payment/create-payment')}}",
                headers: {
                    'x-csrf-token': "{{csrf_token()}}",
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
                url: "{{url('/payment/execute-payment')}}",
                headers: {
                    'x-csrf-token': "{{csrf_token()}}",
                },
                'data':{'paymentID': data.paymentID,'payerID': data.payerID}
            }).then(function(response) {

                if(response.status == true){
                    swal({
                        title: 'Success',
                        html: response.message,
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
                                    if(response.redirect_url){
                                        window.location = response.redirect_url;
                                    }              
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }else{
                    swal({
                        title: 'Notification',
                        html: response.message,
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
                                    location.reload();
                                }
                            })
                        }
                    }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                }
            });
        }
      }, '#paypal-button');
    </script> 
@endpush