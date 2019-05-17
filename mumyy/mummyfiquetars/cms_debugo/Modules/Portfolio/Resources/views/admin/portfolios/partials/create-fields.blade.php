<div class="box-body">
  <div class='form-group{{ $errors->has("vendor_id") ? ' has-error' : '' }}''>
        {!! Form::label("Vendor", trans('Vendor')) !!} <span class="text-danger">*</span>
        <?php  if($isRedirectBack):?>
            {!! Form::text("vendor", Input::old('vendor', $vendors->first_name . " ". $vendors->last_name), ['class' => "form-control", 'readonly'] ) !!}
            {!! Form::hidden("vendor_id", old("vendor_id", $vendors->id), ['class' => "form-control vendor"]) !!}
            {!! Form::hidden("isRedirectBack", old("isRedirectBack", $isRedirectBack), ['class' => "form-control vendor"]) !!}
        <?php else:?>
            {!! Form::select("vendor_id", ["" => ""] + $vendors, "", ['class' => "form-data-vendor form-control vendor", 'id' => "vendor"]) !!}
            {!! Form::hidden("url_vendor", route('api.vendor.getVendor'), ['class' => "form-control", 'id' => "url-vendor"] ) !!}
            {!! Form::hidden("url_category_vendor", route('api.vendor.getCategories'), ['class' => "form-control", 'id' => "url-category-vendor"] ) !!}
        <?php endif?>
        {!! $errors->first("vendor_id", '<span class="help-block">:message</span>') !!}
  </div>
  <div class="row">
      <div class="col-sm-6">
          <div lass='form-group{{ $errors->has("category_id") ? ' has-error' : '' }}'>
              {!! Form::label("category_id", trans('Category')) !!} <span class="text-danger">*</span>
              {!! Form::select("category_id", array(), old("category_id"), ['class' => "form-data-category form-control category", 'id' => "category"]) !!}
              {!! Form::hidden("url_category", route('api.category.get'), ['class' => "form-control", 'id' => "url-category"] ) !!}
              {!! Form::hidden("token", csrf_token(), ['class' => "form-control", 'id' => "token"] ) !!}
              {!! $errors->first("category_id", '<span class="help-block">:message</span>') !!}
          </div>
      </div>
      <div class="col-sm-6">
            <div class='form-group{{ $errors->has("sub_category_id") ? ' has-error' : '' }}'>
              {!! Form::label("Sub Category", trans('Sub Category')) !!}
              {!! Form::select("sub_category_id", ["" => ""] + $subCategories, old("sub_category_id"), ['class' => "form-data-subcategory form-control sub_category", 'id' => "sub_category"]) !!}
              {!! $errors->first("sub_category_id", '<span class="help-block">:message</span>') !!}
            </div>
      </div>
  </div>
  <div class='form-group{{ $errors->has("city") ? ' has-error' : '' }}'>
      {!! Form::label("city", trans('City')) !!} <span class="text-danger">*</span>
      {!! Form::text("city", Input::old('city'), ['class' => "form-control", 'placeholder' => trans('City')] ) !!}
      {!! $errors->first("city", '<span class="help-block">:message</span>') !!}
  </div>
     <div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
      {!! Form::label("title", trans('Title')) !!} <span class="text-danger">*</span>
      {!! Form::text("title", Input::old('title'), ['class' => "form-control", 'placeholder' => trans('Title')] ) !!}
      {!! $errors->first("title", '<span class="help-block">:message</span>') !!}
  </div>
   <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
      {!! Form::label("description", trans('portfolio::portfolios.form.description')) !!}
      {!! Form::textarea("description", Input::old('description'), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('portfolio::portfolios.form.description')]) !!}
      {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
  </div>
  <div class='form-group{{ $errors->has("photography") ? ' has-error' : '' }}'>
      {!! Form::label("photography", trans('portfolio::portfolios.form.photography')) !!}
      {!! Form::text("photography", Input::old('photography'), ['class' => "form-control", 'placeholder' => trans('portfolio::portfolios.form.photography')]) !!}
      {!! $errors->first("photography", '<span class="help-block">:message</span>') !!}
  </div>
  <div class="form-group">
    @include('media::admin.fields.custom.new-file-link-multiple', [
        'zone' => 'image'
    ])
  </div>

</div>
