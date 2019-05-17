@extends('layouts.backend.dashboard')

@section('content')
	<section class="content">
		<div class="message">
            {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
        </div>        
		<div class="panel">
			<form role="change-password" method="POST" action="{{ url(sprintf('%s/__change-password',ADMIN_FOLDER)) }}" class="form-horizontal" autocomplete="off">
				<div class="panel-body">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="forgot-password-inner">
                            <div class="form-group has-feedback toggle-social{{ $errors->has('old_password') ? ' has-error' : '' }}">
                                <label>{{ trans('website.W0303') }}</label>
                                <div>
                                    <input name="old_password" value="{{ old('old_password') }}" type="password" class="form-control" placeholder="{{ trans('website.W0303') }}" />
                                    @if ($errors->has('old_password'))
                                        <span class="help-block">{{ $errors->first('old_password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback toggle-social{{ $errors->has('new_password') ? ' has-error' : '' }}">
                                <label>{{ trans('website.W0304') }}</label>
                                <div>
                                    <input name="new_password" value="{{ old('new_password') }}" type="password" class="form-control" placeholder="{{ trans('website.W0304') }}" />
                                    @if ($errors->has('new_password'))
                                        <span class="help-block">{{ $errors->first('new_password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback toggle-social{{ $errors->has('confirm_password') ? ' has-error' : '' }}">
                                <label>{{ trans('website.W0305') }}</label>
                                <div>
                                    <input name="confirm_password" value="{{ old('confirm_password') }}" type="password" class="form-control" placeholder="{{ trans('website.W0302') }}" />
                                    @if ($errors->has('confirm_password'))
                                        <span class="help-block">{{ $errors->first('confirm_password') }}</span>
                                    @endif
                                </div>
                            </div>            
                        </div>
                    </div>                      
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-default">{{trans('website.W0058')}}</button>
				</div>
            </form>
		</div>
	</section>
@endsection