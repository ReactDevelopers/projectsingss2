<div class="box-body">
	<input type="text" name="email" style="display:none"> 
	<input type="password" name="password" autocomplete="new-password" style="display:none">
    <div class='form-group{{ $errors->has("first_name") ? ' has-error' : '' }}'>
	    {!! Form::label("first_name", trans('vendor::vendors.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("first_name", Input::old('first_name'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.name')] ) !!}
	    {!! $errors->first("first_name", '<span class="help-block">:message</span>') !!}
	</div>
	{{-- 
	<div class='form-group{{ $errors->has("last_name") ? ' has-error' : '' }}'>
	    {!! Form::label("last_name", trans('customer::customers.form.last name')) !!}
	    {!! Form::text("last_name", Input::old('last_name'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.last name')] ) !!}
	    {!! $errors->first("last_name", '<span class="help-block">:message</span>') !!}
	</div>
	--}}
	<div class='form-group{{ $errors->has("email") ? ' has-error' : '' }}'>
	    {!! Form::label("email", trans('vendor::vendors.form.email')) !!} <span class="text-danger">*</span>
	    {!! Form::text("email", Input::old('email', ''), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.email')] ) !!}
	    {!! $errors->first("email", '<span class="help-block">:message</span>') !!}
	</div>
<!-- 	<div class='form-group{{ $errors->has("phone") ? ' has-error' : '' }}'>
	    {!! Form::label("phone", trans('vendor::vendors.form.phone')) !!}
	    {!! Form::text("phone", Input::old('phone'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.phone')] ) !!}
	    {!! $errors->first("phone", '<span class="help-block">:message</span>') !!}
	</div> -->
	<div class='form-group{{ $errors->has("password") ? ' has-error' : '' }}'>
	    {!! Form::label("password", trans('vendor::vendors.form.password')) !!} <span class="text-danger">*</span>
	    {!! Form::password("password", ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.password')] ) !!}
	    {!! $errors->first("password", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('vendor::vendors.form.status')) !!}
	    {!! Form::select("status", Config('asgard.vendor.config.status'), old("status"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
	<div class="form-group{{ $errors->has("medias_single") ? ' has-error' : '' }}">
		@include('media::admin.fields.new-file-link-single', [
		    'zone' => 'image',
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
	</div>  
	<div class='form-group{{ $errors->has("website") ? ' has-error' : '' }}'>
	    {!! Form::label("website", trans('vendor::vendors.form.website')) !!}
	    {!! Form::text("website", Input::old('website'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.website')] ) !!}
	    {!! $errors->first("website", '<span class="help-block">:message</span>') !!}
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("category_id") ? ' has-error' : '' }}'>
			    {!! Form::label("category_id", trans('vendor::vendors.form.business category')) !!} <span class="text-danger">*</span>
			    {!! Form::select("category_id", ["" => ""] + $categories, old('category_id'), ['class' => "form-control js-example-basic-single", 'id' => 'category'] ) !!}
			    {!! $errors->first("category_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("sub_category_id") ? ' has-error' : '' }}'>
			    {!! Form::label("sub_category_id", trans('vendor::vendors.form.business sub category')) !!}
			    {!! Form::select("sub_category_id", ["" => ""] + $subCategories, old('sub_category_id'), ['class' => "form-control js-example-basic-single", 'id' => 'sub_category'] ) !!}
			    {!! $errors->first("sub_category_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
	</div>
	<div class='form-group{{ $errors->has("business_name") ? ' has-error' : '' }}'>
	    {!! Form::label("business_name", trans('vendor::vendors.form.business name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("business_name", Input::old('business_name'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.business name')] ) !!}
	    {!! $errors->first("business_name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("about") ? ' has-error' : '' }}'>
	    {!! Form::label("about", trans('vendor::vendors.form.business about')) !!}
	    {!! Form::textarea("about", Input::old('about'), ['class' => "form-control", 'rows' => '3', 'placeholder' => trans('vendor::vendors.form.business about')] ) !!}
	    {!! $errors->first("about", '<span class="help-block">:message</span>') !!}
	</div>
		<div class='form-group{{ $errors->has("business_address") ? ' has-error' : '' }}'>
	    {!! Form::label("business_address", trans('vendor::vendors.form.business address')) !!} <span class="text-danger">*</span>
	    {!! Form::textarea("business_address", Input::old('business_address'), ['class' => "form-control", 'rows' => '3', 'placeholder' => trans('vendor::vendors.form.business address')] ) !!}
	    {!! $errors->first("business_address", '<span class="help-block">:message</span>') !!}
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class='form-group{{ $errors->has("country_id") ? ' has-error' : '' }}'>
			    {!! Form::label("country_id", trans('vendor::vendors.form.country')) !!} <span class="text-danger">*</span>
			    {!! Form::select("country_id", $countries, old('country_id', 196), ['class' => "form-control", 'id' => 'country', 'onchange' => 'fetch_city(this.value);'] ) !!}
			    {!! $errors->first("country_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
		{{--
		<div class="col-sm-4">
			<div class='form-group{{ $errors->has("states_id") ? ' has-error' : '' }}'>
			    {!! Form::label("states_id", trans('vendor::vendors.form.state')) !!} <span class="text-danger">*</span>
			    {!! Form::select("states_id", array(), old('states_id'), ['class' => "form-control js-example-basic-single", 'id' => 'state', 'onchange' => 'fetch_city(this.value);'] ) !!}
			    {!! $errors->first("states_id", '<span class="help-block">:message</span>') !!}
			</div>
		</div>
		--}}
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
	    {!! Form::text("zip_code", Input::old('zip_code'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.zipcode')] ) !!}
	    {!! $errors->first("zip_code", '<span class="help-block">:message</span>') !!}
	</div>
	<div id="phone-number-container">
		<div class="row">
			<div class="col-sm-2">
				<div class='form-group{{ $errors->has("business_code") ? ' has-error' : '' }}'>
				    {!! Form::label("business_code", trans('vendor::vendors.form.business phone code')) !!}
				    {!! Form::select("business_code", $vendorPhonecodes, old('business_code'),['class' => "form-control", 'id' => "vendor-phonecode" ] ) !!}
				    {!! $errors->first("business_code", '<span class="help-block">:message</span>') !!}
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
	
{{-- 	<div class='form-group{{ $errors->has("business_phone2") ? ' has-error' : '' }}'>
	    {!! Form::label("business_phone2", trans('vendor::vendors.form.business phone number 2')) !!}
	    {!! Form::text("business_phone2", Input::old('business_phone2'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.business phone number 2')] ) !!}
	    {!! $errors->first("business_phone2", '<span class="help-block">:message</span>') !!}
	</div>
		<div class='form-group{{ $errors->has("business_phone3") ? ' has-error' : '' }}'>
	    {!! Form::label("business_phone3", trans('vendor::vendors.form.business phone number 3')) !!}
	    {!! Form::text("business_phone3", Input::old('business_phone3'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.business phone number 3')] ) !!}
	    {!! $errors->first("business_phone3", '<span class="help-block">:message</span>') !!}
	</div> --}}
	<div class='form-group{{ $errors->has("social_media_link_facebook") ? ' has-error' : '' }}'>
	    {!! Form::label("social_media_link_facebook", trans('vendor::vendors.form.social media facebook')) !!}
	    {!! Form::text("social_media_link_facebook", Input::old('social_media_link_facebook'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.social media facebook')] ) !!}
	    {!! $errors->first("social_media_link_facebook", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("social_media_link_twitter") ? ' has-error' : '' }}'>
	    {!! Form::label("social_media_link_twitter", trans('vendor::vendors.form.social media twitter')) !!}
	    {!! Form::text("social_media_link_twitter", Input::old('social_media_link_twitter'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.social media twitter')] ) !!}
	    {!! $errors->first("social_media_link_twitter", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("social_media_link_instagram") ? ' has-error' : '' }}'>
	    {!! Form::label("social_media_link_instagram", trans('vendor::vendors.form.social media instagram')) !!}
	    {!! Form::text("social_media_link_instagram", Input::old('social_media_link_instagram'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.social media instagram')] ) !!}
	    {!! $errors->first("social_media_link_instagram", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("social_media_link_pinterest") ? ' has-error' : '' }}'>
	    {!! Form::label("social_media_link_pinterest", trans('vendor::vendors.form.social media pinterest')) !!}
	    {!! Form::text("social_media_link_pinterest", Input::old('social_media_link_pinterest'), ['class' => "form-control", 'placeholder' => trans('vendor::vendors.form.social media pinterest')] ) !!}
	    {!! $errors->first("social_media_link_pinterest", '<span class="help-block">:message</span>') !!}
	</div>
</div>

