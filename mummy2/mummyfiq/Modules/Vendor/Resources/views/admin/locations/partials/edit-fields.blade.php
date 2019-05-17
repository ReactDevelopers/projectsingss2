<div class="box-body">
	<div class="row">
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("country_id") ? ' has-error' : '' }}'>
			    {!! Form::label("country_id", trans('vendor::vendors.form.country')) !!} <span class="text-danger">*</span>
			    {!! Form::select("country_id", $countriesArr, old('country_id', $location && $location->country_id ? $location->country_id : 196), ['class' => "form-control", 'id' => 'country'] ) !!}
			    {!! $errors->first("country_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("city_id") ? ' has-error' : '' }}'>
			    {!! Form::label("city_id", trans('vendor::vendors.form.city')) !!}
			    {!! Form::select("city_id", $citiesArr, old('city_id', $location ? $location->city_id : ''), ['class' => "form-control",  'id' => 'city']) !!}
			    {!! $errors->first("city_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
	</div>
	<div class='form-group{{ $errors->has("zip_code") ? ' has-error' : '' }}'>
	    {!! Form::label("zip_code", trans('vendor::vendors.form.zipcode')) !!}
	    {!! Form::text("zip_code", Input::old('zip_code', $location->zip_code ? $location->zip_code : ($vendor->vendorProfile ? $vendor->vendorProfile->zip_code : '')), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.zipcode'), 'id' => "vendor-zipcode"] ) !!}
	    {!! $errors->first("zip_code", '<span class="help-block">:message</span>') !!} 
	</div>

	<?php if(count($vendorPhones)):?>
		<div id="phone-number-container">
			<?php foreach($vendorPhones as $k=>$item):?>
				<?php if($k == 0):?>
					<div class="row">
						<div class="col-sm-2">
							<div class='form-group{{ $errors->has("business_code") ? ' has-error' : '' }}'>
							    {!! Form::label("business_code", trans('vendor::vendors.form.business phone code')) !!}
							    <input type="text" name="business_code" value="{{$location ? "+".$location->country->phonecode : ""}}" class="form-control" id="vendor-phonecode" readonly>
							</div>
						</div>
						<div class="col-sm-8">
							<div class='form-group'>
							    {!! Form::label("business_phone", trans('vendor::vendors.form.business phone number')) !!}
							    <input type="text" name="business_phone[]" class="form-control business-phone" id="vendor-business-phone" placeholder="{{ trans('vendor::vendors.form.business phone number') }}" value="{{$item->phone_number}}">
							</div>
						</div>
						<div class="col-sm-2">
							<div class='form-group' style="padding-top: 30px;">
								<a href="#" id="link-add-phone_number" class="link-add-phone_number">Add new number</a>
							</div>
						</div>
					</div>
				<?php else:?>
					<div class="row new-phone-number">
						<div class="col-sm-2">
							
						</div>
						<div class="col-sm-8">
							<div class='form-group'>
							    <input type="text" name="business_phone[]" class="form-control" id="vendor-business-phone" placeholder="{{ trans('vendor::vendors.form.business phone number') }}" value="{{$item->phone_number}}">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group" style="padding-top: 5px;">
								<a href="javascript:avoid(0)" id="link-remove-phone_number" class="link-remove-phone_number">Remove</a>
							</div>
						</div>
					</div>
				<?php endif?>
			<?php endforeach?>
			<div id="append-phone_number"></div>
		</div>
	<?php else:?>
		<div id="phone-number-container">
			<div class="row">
				<div class="col-sm-2">
					<div class='form-group{{ $errors->has("business_code") ? ' has-error' : '' }}'>
					    {!! Form::label("business_code", trans('vendor::vendors.form.business phone code')) !!}
					    <input type="text" name="business_code" value="{{$location ? "+".$location->country->phonecode : ""}}" class="form-control" id="vendor-phonecode" readonly>
					</div>
				</div>
				<div class="col-sm-8">
					<div class='form-group'>
					    {!! Form::label("business_phone", trans('vendor::vendors.form.business phone number')) !!}
					    <input type="text" name="business_phone[]" class="form-control business-phone" id="vendor-business-phone" placeholder="{{ trans('vendor::vendors.form.business phone number') }}">
					</div>
				</div>
				<div class="col-sm-2">
					<div class='form-group' style="padding-top: 30px;">
						<a href="#" id="link-add-phone_number" class="link-add-phone_number">Add new number</a>
					</div>
				</div>
			</div>
			<div id="append-phone_number"></div>
		</div>
	<?php endif?>
	@if(!$location->is_primary)
		<input type="checkbox" value="1" name="is_primary"> <label>Is Primary</label>
	@endif
</div>
