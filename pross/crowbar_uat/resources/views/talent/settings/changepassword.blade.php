<form role="change-password" method="POST" action="{{ url(sprintf('%s/__change-password',TALENT_ROLE_TYPE)) }}" class="form-horizontal" autocomplete="off">
    {{ csrf_field() }}
    <div class="login-inner-wrapper setting-wrapper">
        <p class="p-b-15">{{trans('website.W0714')}}</p>
        <div class="message">
            {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
        </div>
        <div class="forgot-password-inner">
            @if($user['social_account'] !== DEFAULT_YES_VALUE)
                <div class="form-group has-feedback toggle-social{{ $errors->has('old_password') ? ' has-error' : '' }}">
                    <label class="control-label col-md-8 col-sm-12 col-xs-12">{{ trans('website.W0303') }}</label>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <input name="old_password" value="{{ old('old_password') }}" type="password" class="form-control" placeholder="{{ trans('website.W0303') }}"/>
                        @if ($errors->has('old_password'))
                            <span class="help-block">{{ $errors->first('old_password') }}</span>
                        @endif
                    </div>
                </div>
            @endif
                
            <div class="form-group has-feedback toggle-social{{ $errors->has('new_password') ? ' has-error' : '' }}">
                <label class="control-label col-md-8 col-sm-12 col-xs-12">{{ trans('website.W0304') }}</label>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <input name="new_password" value="{{ old('new_password') }}" type="password" class="form-control" placeholder="{{ trans('website.W0304') }}" />
                    @if ($errors->has('new_password'))
                        <span class="help-block">{{ $errors->first('new_password') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">                                        
                <div class="col-md-5 col-sm-5 col-xs-6">
                    <button type="button" data-request="ajax-submit" data-target='[role="change-password"]' class="btn btn-sm redShedBtn pull-right">{{trans('website.W0058')}}</button>
                </div>
            </div>
        </div>
    </div>                                
</form>

@push('inlinescript')
    <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
    <script type="text/javascript">$('[name="new_password"]').hidePassword(true);</script>
@endpush