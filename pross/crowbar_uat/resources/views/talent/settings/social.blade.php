<div class="login-inner-wrapper setting-wrapper social-connect">
    <p class="p-b-15">{{trans('website.W0666')}}</p>
    <div class="message">
        {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
    </div>
    <div class="row form-group">
        <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
            <label class="control-label">
                <img src="{{ asset('images/instagram.png') }}" />&nbsp;&nbsp;
                <span class="social-type-name">
                    {{sprintf(trans('website.W0115'),trans('website.W0131'))}}
                </span>
            </label>
        </div>
        <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
            <div class="checkbox pull-right bootstrap-toggle-button">
                <label>
                    <input id="instagram" type="checkbox" @if(!empty($user['instagram_id'])) checked @endif data-toggle="toggle" data-url="{{ asset('/login/instagram') }}" value="instagram_id" />
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row form-group">
        <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
            <label class="control-label">
                <img src="{{ asset('images/facebook.png') }}" />&nbsp;&nbsp;
                <span class="social-type-name">
                    {{sprintf(trans('website.W0115'),trans('website.W0116'))}}
                </span>
            </label>
        </div>
        <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
            <div class="checkbox pull-right bootstrap-toggle-button">
                <label>
                    <input id="facebook" type="checkbox" @if(!empty($user['facebook_id'])) checked @endif data-toggle="toggle" data-url="{{ asset('/login/facebook') }}" value="facebook_id" />
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row form-group">
        <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
            <label class="control-label">
                <img src="{{ asset('images/t-w-i-t-t-e-r-small-icon.png') }}" />&nbsp;&nbsp;
                <span class="social-type-name">
                    {{sprintf(trans('website.W0115'),trans('website.W0119'))}}
                </span>
            </label>
        </div>
        <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
            <div class="checkbox pull-right bootstrap-toggle-button">
                <label>
                    <input id="twitter" type="checkbox" @if(!empty($user['twitter_id'])) checked @endif data-toggle="toggle" data-url="{{ asset('/login/twitter') }}" value="twitter_id" />
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row form-group">
        <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
            <label class="control-label">
                <img src="{{ asset('images/linkedin.png') }}" />&nbsp;&nbsp;
                <span class="social-type-name">
                    {{sprintf(trans('website.W0115'),trans('website.W0120'))}}
                </span>
            </label>
        </div>
        <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
            <div class="checkbox pull-right bootstrap-toggle-button">
                <label>
                    <input id="linkedin" type="checkbox" @if(!empty($user['linkedin_id'])) checked @endif data-toggle="toggle" data-url="{{ asset('/login/linkedin') }}" value="linkedin_id" />
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row form-group">
        <div class="col-md-7 col-sm-8 col-xs-6 social-link-wrapper">
            <label class="control-label">
                <img src="{{ asset('images/gplus.png') }}" />&nbsp;&nbsp;
                <span class="social-type-name">
                    {{sprintf(trans('website.W0115'),trans('website.W0121'))}}
                </span>
            </label>
        </div>
        <div class="col-md-5 col-sm-4 col-xs-6 social-btn-wrapper">
            <div class="checkbox pull-right bootstrap-toggle-button">
                <label>
                    <input id="googleplus" type="checkbox" @if(!empty($user['googleplus_id'])) checked @endif data-toggle="toggle" data-url="{{ asset('/login/googleplus') }}" value="googleplus_id" />
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>

@push('inlinescript')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>
    <script type="text/javascript">
        $(function() {
            $(document).on('change','#instagram, #facebook, #twitter, #linkedin, #googleplus',function(e) {
                var $this = $(this);
                var $isChecked = $this.prop('checked');

                if($isChecked === true){
                    window.location = $this.data('url');
                }else{
                    $.post('{{url("talent/__socialsettings")}}?socialkey='+$this.val()); 
                }
            })
        });
    </script>
@endpush