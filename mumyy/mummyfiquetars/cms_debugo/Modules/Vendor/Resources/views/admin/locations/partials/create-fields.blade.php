<div class="box-body">
	<div class="row">
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("country_id") ? ' has-error' : '' }}'>
			    {!! Form::label("country_id", trans('vendor::vendors.form.country')) !!} <span class="text-danger">*</span>
			    {!! Form::select("country_id", $countriesArr, old('country_id'), ['class' => "form-control", 'id' => 'country'] ) !!}
			    {!! $errors->first("country_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("city_id") ? ' has-error' : '' }}'>
			    {!! Form::label("city_id", trans('vendor::vendors.form.city')) !!}
			    {!! Form::select("city_id", array(), old('city_id'), ['class' => "form-control",  'id' => 'city']) !!}
			    {!! $errors->first("city_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
	</div>
	<div class='form-group{{ $errors->has("zip_code") ? ' has-error' : '' }}'>
	    {!! Form::label("zip_code", trans('vendor::vendors.form.zipcode')) !!}
	    {!! Form::text("zip_code", Input::old('zip_code'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.zipcode'), 'id' => "vendor-zipcode"] ) !!}
	    {!! $errors->first("zip_code", '<span class="help-block">:message</span>') !!} 
	</div>

	<div id="phone-number-container">
		<div class="row">
			<div class="col-sm-2">
				<div class='form-group{{ $errors->has("business_code") ? ' has-error' : '' }}'>
				    {!! Form::label("business_code", trans('vendor::vendors.form.business phone code')) !!}
				    <input type="name" name="business_code" value="" class="form-control" id="vendor-phonecode" readonly>
				</div>
			</div>
			<div class="col-sm-8">
				<div class='form-group'>
				    {!! Form::label("business_phone", trans('vendor::vendors.form.business phone number')) !!}
				    <input type="number" name="business_phone[]" class="form-control" id="vendor-business-phone" placeholder="{{ trans('vendor::vendors.form.business phone number') }}">
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
	<input type="checkbox" value="1" name="is_primary"> <label>Is Primary</label>
</div>
