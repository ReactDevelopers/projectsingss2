<form role="paypal" method="POST" action="{{ url(sprintf('%s/__payments',EMPLOYER_ROLE_TYPE)) }}" class="form-horizontal" autocomplete="off">
    {{ csrf_field() }}
    <div class="login-inner-wrapper setting-wrapper">
        <p class="p-b-15">{{trans('website.W0999')}}</p>
        <div class="message">
            {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
        </div>
        <div class="col-md-7 col-sm-12 col-xs-12">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">{{trans('website.W0716')}}</label>
                    </div>
                    <div class="col-md-7 col-sm-12 col-xs-12">
                        <input name="paypal_id" value="{{$user['paypal_id']}}" type="text" class="form-control big-field" placeholder="{{ trans('website.W0716') }}" />
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-4 no-padding">
                        <button type="button" class="btn" data-request="paypal-ajax-submit" data-target='[role="paypal"]'><img height="20" width="80" src="{{asset('images/paypal.png')}}"></button>
                    </div>
                </div>
                <input type="text" class="hide" />
                    <br/>
                    <a class="paypal-url" id="paypal_url" data-paypal-button="true" href="" target="PPFrame" style="display:none;">Login for PayPal. Click Here</a>
            </div>                                
        </div>               
    </div>                                
</form>
@push('inlinescript')
    <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
    <script type="text/javascript">$('[name="new_password"]').hidePassword(true);</script>

    <script type="text/javascript">
        $(document).ready(function(){
            var verified_paypal_email = false;
            verified_paypal_email = "{{$verified_paypal_email}}";

            var message = "{{$returnMessage}}";

            if(verified_paypal_email == true){
                swal({
                    title: 'Notification',
                    html: 'Your details have been saved. '+message,
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
                                window.location = "{{url(sprintf('%s/settings/payments',TALENT_ROLE_TYPE))}}";
                                resolve();              
                            }
                        })
                    }
                }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
            }
        });
    </script>
    <script type="text/javascript">
        (function(d, s, id){
            var js, ref = d.getElementsByTagName(s)[0]; 
            if (!d.getElementById(id)){
                js = d.createElement(s); 
                js.id = id; 
                js.async = true;
                js.src = "https://www.sandbox.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                ref.parentNode.insertBefore(js, ref); }
        }(document, "script", "paypal-js"));
    </script>
@endpush