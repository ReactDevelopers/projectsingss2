<div class="box-body">
    <div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
	    {!! Form::label("title", trans('banner::banners.form.title')) !!} <span class="text-danger">*</span>
	    {!! Form::text("title", Input::old('title', $banner->title), ['class' => "form-control", 'placeholder' => trans('banner::banners.form.title')] ) !!}
	    {!! $errors->first("title", '<span class="help-block">:message</span>') !!}
	</div>

	{{--<div class="form-group{{ $errors->has("medias_single") ? ' has-error' : '' }}">
		@include('media::admin.fields.file-link', [
		    'entityClass' => 'Modules\\\\Banner\\\\Entities\\\\Banner',
		    'entityId' => $banner->id,
		    'zone' => 'image'
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
	</div>--}}
	
	@if(!empty($picture->imageablesid))
	<div class='form-group'>
		<div class='form-group'>
			<label for="image" class="labelhide">Image:</label>
	    	<div class="clearfix"></div>
			<div class="jsThumbnailImageWrapper jsSingleThumbnailWrapper">
	            <figure data-id="{{$picture->imageablesid}}">
	                <img src="{{ ($banner) ? ($picture ? MediaService::getImage($picture->path) : URL::to('/') . '/assets/messagedia/no-image.png' ) : URL::to('/') . '/assets/media/no-image.png' }}" alt=""/>
	                <a class="jsRemoveSimpleLink" href="#" data-id="{{$picture->imageablesid}}">
	                    <i class="fa fa-times-circle removeIcon"></i>
	                </a>
	            </figure>
		    </div>
		<i>Image size upto 2 mb max.</i>
		</div>
	</div>
    <input type="hidden" name="medias_single[image]" value="{{$picture->image}}">
	@endif
	<div class="form-group browse-image {{ $errors->has("medias_single") ? ' has-error' : '' }} " style="display: none;">
		@include('media::admin.fields.new-file-link-single', [
		    'zone' => 'image'
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
	</div>
	<input type="hidden" id="oldimageablesid" name="oldimageablesid" value="">

	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('banner::banners.form.status')) !!}:
	    {!! Form::select("status", Config('asgard.banner.config.status'), old("status", $banner->status), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("type") ? ' has-error' : '' }}'>
	    {!! Form::label("type", trans('banner::banners.form.type')) !!}:
	    {!! Form::select("type", Config('asgard.banner.config.type'), old("type", $banner->type), ['class' => "form-control status"]) !!}
	    {!! $errors->first("type", '<span class="help-block">:message</span>') !!}
	    <input type="hidden" name="oldtype" id="oldtype" value="{{$banner->type}}">
	</div>

	<div id="external_link">
		<div class='form-group{{ $errors->has("link") ? ' has-error' : '' }}'>
		    {!! Form::label("link", trans('banner::banners.form.link')) !!} <span class="text-danger">*</span>
		    {!! Form::text("link", Input::old('link', $banner->link), ['class' => "form-control", 'placeholder' => trans('banner::banners.form.link')] ) !!}
		    {!! $errors->first("link", '<span class="help-block">:message</span>') !!}
		</div>
	</div>

	<div id="predefined_filters">
		<div class='form-group{{ $errors->has("country") ? ' has-error' : '' }}'>
			{!! Form::label("country", trans('banner::banners.form.country')) !!} <span class="text-danger">*</span>
			<input type="checkbox" id="countrycheckbox" name="countrycheckbox" {{ old('countrycheckbox') ? 'checked' : '' }}>Select All
			<select multiple id="country" class='form-control country' name="country[]">
		        @foreach($countriesArr as $k => $country)
				<?php
				if(old('country'))
				$value = old('country');
				else
				$value = $bco;
				?>
		        <option value="{!!$k!!}" {{ (collect($value)->contains($k)) ? 'selected':'' }} >{!!$country!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("country", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("category") ? ' has-error' : '' }}'>
			{!! Form::label("category", trans('banner::banners.form.category')) !!} <span class="text-danger">*</span><input type="checkbox" id="catcheckbox" name="catcheckbox" {{ old('catcheckbox') ? 'checked' : '' }}>Select All
			<select multiple id="category" class='form-control category' name="category[]">
		        @foreach($categoryArr as $k => $category)
		        <?php
				if(old('category'))
				$value = old('category');
				else
				$value = $cat;
				?>
		        <option value="{!!$k!!}" {{ (collect($value)->contains($k)) ? 'selected':'' }} >{!!$category!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("category", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("subcategory") ? ' has-error' : '' }}'>
			{!! Form::label("category", trans('banner::banners.form.subcategory')) !!} 
			<!-- <span class="text-danger">*</span> -->
			<input type="checkbox" name="subcatcheckbox" id="subcatcheckbox" {{ old('subcatcheckbox') ? 'checked' : '' }} >Select All
			<select multiple id="subcategory" class='form-control subcategory' name="subcategory[]">
		        @foreach($subcategoryArr as $k => $subcategory)
		        <?php
				if(old('subcategory'))
				$value = old('subcategory');
				else
				$value = $subcat;
				?>
		        <option value="{!!$k!!}" {{ (collect($value)->contains($k)) ? 'selected':'' }} >{!!$subcategory!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("subcategory", '<span class="help-block">:message</span>') !!}
		</div>

		<div class='form-group{{ $errors->has("vendor") ? ' has-error' : '' }}'>
			{!! Form::label("vendor", trans('banner::banners.form.vendor')) !!} <span class="text-danger">*</span>
			<input type="checkbox" name="vendorcheckbox" id="vendorcheckbox" {{ old('vendorcheckbox') ? 'checked' : '' }}>Select All
			<select multiple id="vendor" class='form-control vendor' name="vendor[]">
		        @foreach($vendorArr as $k => $vendor)
		        <?php
				if(old('vendor'))
				$value = old('vendor');
				else
				$value = $bv;
				?>
		        <option value="{!!$k!!}" {{ (collect($value)->contains($k)) ? 'selected':'' }} >{!!$vendor!!}</option>
		        @endforeach
		    </select>
		    {!! $errors->first("vendor", '<span class="help-block">:message</span>') !!}
		</div>

		<?php $selected_keywords = old('keywords') ? old('keywords') : $bk; ?>

		<div class='form-group  input_fields_wrap {{ $errors->has("keywords") ? ' has-error' : '' }}'>
			{!! Form::label("keywords", trans('banner::banners.form.keywords')) !!} 
			<!-- <span class="text-danger">*</span> -->
		    <button class="add_field_button btn btn-info">Add More Fields</button>
		    
		    <?php if(!empty($selected_keywords)) {
				  foreach((array) $selected_keywords as $index => $key){?>
				  		<div style="margin-top: 5px;overflow: hidden;"><input class="form-control col-sm-6" type="text" name="keywords[]" value="{{ $key }}"><?php if($index>0) { ?><a href="javascript:void(0);" class="remove_field">Remove</a><?php } ?></div>
			<?php } } ?>
		    {!! $errors->first("keywords", '<span class="help-block">:message</span>') !!}	
		</div>

	</div>

</div>
