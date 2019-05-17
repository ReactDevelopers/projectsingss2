<div class="box-body">
	<div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
	    {!! Form::label("title", trans('banner::banners.form.title')) !!} <span class="text-danger">*</span>
	    {!! Form::text("title", Input::old('title'), ['class' => "form-control", 'placeholder' => trans('banner::banners.form.title')] ) !!}
	    {!! $errors->first("title", '<span class="help-block">:message</span>') !!}
	</div>
	
	<div class="form-group {{ $errors->has("medias_single") ? ' has-error' : '' }}">
		@include('media::admin.fields.new-file-link-single', [
		    'zone' => 'image'
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
		<i>Image size upto 2 mb max & dimensions (1120*500).</i>
	</div>

	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('banner::banners.form.status')) !!}
	    {!! Form::select("status", Config('asgard.banner.config.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>

	<div class='form-group{{ $errors->has("type") ? ' has-error' : '' }}'>
	    {!! Form::label("type", trans('banner::banners.form.type')) !!}
	    {!! Form::select("type", Config('asgard.banner.config.type'), old("adv_id"), ['class' => "form-control status "]) !!}
	    {!! $errors->first("type", '<span class="help-block">:message</span>') !!}
	    <input type="hidden" id="oldstatus" name="oldstatus" value="{{old('type')}}">
	</div>


	<div id="external_link">
		<div class='form-group{{ $errors->has("link") ? ' has-error' : '' }}'>
		    {!! Form::label("link", trans('banner::banners.form.link')) !!} <span class="text-danger">*</span>
		    {!! Form::text("link", Input::old('link'), ['class' => "form-control", 'placeholder' => trans('banner::banners.form.link')] ) !!}
		    {!! $errors->first("link", '<span class="help-block">:message</span>') !!}
		</div>
	</div>

	<div id="predefined_filters">
		<div class='form-group{{ $errors->has("country") ? ' has-error' : '' }}'>
			{!! Form::label("country", trans('banner::banners.form.country')) !!} <span class="text-danger">*</span>
			<!-- <input type="checkbox" id="countrycheckbox" name="countrycheckbox" {{ old('countrycheckbox') ? 'checked' : '' }}>Select All -->
			<select multiple id="country" class='form-control country' name="country[]">
		        @foreach($countriesArr as $k => $country)
		        <option value="{!!$k!!}" {{ (collect(old('country'))->contains($k)) ? 'selected':'' }} >{!!$country!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("country", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("category") ? ' has-error' : '' }}'>
			{!! Form::label("category", trans('banner::banners.form.category')) !!} <span class="text-danger">*</span>
			<!-- <input type="checkbox" id="catcheckbox" name="catcheckbox" {{ old('catcheckbox') ? 'checked' : '' }}>Select All -->
			<select multiple id="category" class='form-control category' name="category[]">
		        @foreach($categoryArr as $k => $category)
		        <option value="{!!$k!!}" {{ (collect(old('category'))->contains($k)) ? 'selected':'' }} >{!!$category!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("category", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("subcategory") ? ' has-error' : '' }}'>
			{!! Form::label("category", trans('banner::banners.form.subcategory')) !!} 
			<!-- <span class="text-danger">*</span> -->
			<!-- <input type="checkbox" name="subcatcheckbox" id="subcatcheckbox" {{ old('subcatcheckbox') ? 'checked' : '' }} >Select All -->
			<select multiple id="subcategory" class='form-control subcategory' name="subcategory[]">
		        @foreach($subcategoryArr as $k => $subcategory)
		        <option value="{!!$k!!}" {{ (collect(old('subcategory'))->contains($k)) ? 'selected':'' }} >{!!$subcategory!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("subcategory", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("vendor") ? ' has-error' : '' }}'>
			{!! Form::label("vendor", trans('banner::banners.form.vendor')) !!} <span class="text-danger">*</span>
			<!-- <input type="checkbox" name="vendorcheckbox" id="vendorcheckbox" {{ old('vendorcheckbox') ? 'checked' : '' }}>Select All -->
			<select multiple id="vendor" class='form-control vendor' name="vendor[]">
		        @foreach($vendorArr as $k => $vendor)
		        <option value="">All Selected</option>
		        <option value="{!!$k!!}" {{ (collect(old('vendor'))->contains($k)) ? 'selected':'' }} >{!!$vendor!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("vendor", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("excludevendor") ? ' has-error' : '' }}'>
			{!! Form::label("excludevendor", trans('banner::banners.form.excludevendor')) !!} <span class="text-danger">*</span>
			<!-- <input type="checkbox" name="vendorcheckbox" id="vendorcheckbox" {{ old('vendorcheckbox') ? 'checked' : '' }}>Select All -->
			<select multiple id="excludevendor" class='form-control excludevendor' name="excludevendor[]">
		        @foreach($vendorArr as $k => $vendor)
		        <option value="">All Selected</option>
		        <option value="{!!$k!!}" {{ (collect(old('vendor'))->contains($k)) ? 'selected':'' }} >{!!$vendor!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("excludevendor", '<span class="help-block">:message</span>') !!}
		</div>

		<?php $selected_keywords = old('keywords'); ?>

		<div class='form-group  input_fields_wrap {{ $errors->has("keywords") ? ' has-error' : '' }}'>
			{!! Form::label("keywords", trans('banner::banners.form.keywords')) !!} 
			<!-- <span class="text-danger">*</span> -->
			<i>Max 5 Fields</i>
		    <button class="add_field_button btn btn-info">Add More Fields</button>

		    <?php if(empty($selected_keywords)) {?>
		    		<div style="margin-top: 5px;overflow: hidden;"><input class="form-control col-sm-6" type="text" name="keywords[]" value=""></div>
			<?php } else {
				  foreach((array) $selected_keywords as $index => $key){?>
				  		<div style="margin-top: 5px;overflow: hidden;"><input class="form-control col-sm-6" type="text" name="keywords[]" value="{{ $key }}"><?php if($index>0) { ?><a href="javascript:void(0);" class="remove_field">Remove</a><?php } ?></div>
			<?php } }?>

		    {!! $errors->first("keywords", '<span class="help-block">:message</span>') !!}
		    
		</div>
	</div>

</div>
