<div class="greyBar-Heading grey-wrapper">
    <div class="container">
        <div class="row">
	        <div class="col-md-12">
                <h4>{{trans('website.W0127')}}</h4>
            </div>                	
        </div>
    </div>
</div>
<div class="contentWrapper">  
	<section class="login-section">
		<div class="container">
			<div class="login-inner-wrapper m-t-10px">
				<div class="row has-vr">
					<form method="POST" action="{{ url('/authenticate?back='.$back) }}" class="form-horizontal login-form m-auto" autocomplete="off">
						{{ csrf_field() }}
						<div class="message">
							{{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
						</div>
						@if(isset($talent_id))
							<input type="hidden" name="talent_id" value="{{$talent_id}}">
						@endif
						@if(isset($ownershiptoken))
							<input type="hidden" name="owner" value="{{$owner}}">
							<input type="hidden" name="ownershiptoken" value="{{$ownershiptoken}}">
						@endif
						<div class="login-wrapper-light">
							<div class="form-group has-feedback{{ $errors->has(LOGIN_EMAIL) ? ' has-error' : '' }}">
								<label class="col-md-12 col-sm-12 col-xs-12 control-label">Email Address</label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<input name="{{ LOGIN_EMAIL }}" value="{{ old(LOGIN_EMAIL,(!empty(${LOGIN_EMAIL}))?${LOGIN_EMAIL}:'') }}" type="text" class="form-control" autofocus>
									@if ($errors->has(LOGIN_EMAIL))
										<span class="help-block">{{ $errors->first(LOGIN_EMAIL) }}</span>
									@endif
								</div>
							</div>
							<div class="form-group has-feedback toggle-social{{ $errors->has(LOGIN_PASSWORD) ? ' has-error' : '' }}">
								<label class="col-md-12 col-sm-12 col-xs-12 control-label">Password</label>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<input name="{{ LOGIN_PASSWORD }}" value="{{ old(LOGIN_PASSWORD,(!empty(${LOGIN_PASSWORD}))?${LOGIN_PASSWORD}:'') }}" type="password" class="form-control" value="{{ old(LOGIN_PASSWORD) }}">
									@if ($errors->has(LOGIN_PASSWORD))
										<span class="help-block">{{ $errors->first(LOGIN_PASSWORD) }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="form-group remember-me-group">
							<div class="col-md-6 col-sm-6 col-xs-6 hide">
								<div class="checkbox small-checkbox">                
									<input name="{{ LOGIN_REMEMBER }}" type="checkbox" id="terms-check" value="1" @if(!empty(${LOGIN_PASSWORD})) checked @endif>
									<label for="terms-check">
										<span class="check"></span>
										{{trans('website.W0126')}}
									</label>
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-6 hide"></div>
						</div>                                  
						<div class="form-group">                                        
							<div class="col-md-8 col-sm-12 col-xs-12 text-right">
								<a href="{{ url('/forgot/password') }}" class="forgot-pass-link m-t-5px">{{trans('website.W0133')}}</a>
							</div>
							<div class="col-md-4 col-sm-12 col-xs-12 text-right">
								<button type="submit" class="btn btn-sm redShedBtn">{{trans('website.W0127')}}</button>
							</div>
						</div>                                
					</form>
				</div>                        
			</div>                    
		</div>
	</section>
</div>