<form role="settings" method="POST" action="{{ url(sprintf('%s/connect-with-talent',TALENT_ROLE_TYPE)) }}" class="form-horizontal" autocomplete="off">
    {{ csrf_field() }}
    <div class="login-inner-wrapper setting-wrapper">
        {{-- <p class="p-b-15">{{trans('website.W0987')}}</p> --}}
        <div class="message">
            {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
        </div>
        <div class="forgot-password-inner">
            <div class="form-group has-feedback toggle-social{{ $errors->has('invite_code') ? ' has-error' : '' }}">
                <label class="control-label col-md-8 col-sm-12 col-xs-12">{{ trans('website.W0987') }}</label>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <input name="invite_code" value="{{ old('invite_code') }}" type="text" class="form-control" placeholder="{{ trans('website.W0987') }}" />
                    @if ($errors->has('invite_code'))
                        <span class="help-block">{{ $errors->first('invite_code') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">
                <div class="col-md-5 col-sm-5 col-xs-6">
                    <button type="button" data-request="ajax-submit" data-target='[role="settings"]' class="btn btn-sm redShedBtn pull-right">{{trans('website.W0058')}}</button>
                </div>
            </div>      
        </div>
    </div>
</form>