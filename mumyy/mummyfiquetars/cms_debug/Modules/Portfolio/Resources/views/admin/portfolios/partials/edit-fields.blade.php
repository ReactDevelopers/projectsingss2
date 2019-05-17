<div class="box-body">
    <div class="form-group">
        {!! Form::label("Vendor", trans('Vendor')) !!} <span class="text-danger">*</span>
        {!! Form::select("vendor_id", $vendors, old("vendor_id", $portfolio->vendor_id), ['class' => "form-data-vendor form-control vendor", 'id' => "vendor"]) !!}
        {!! Form::hidden("url_vendor", route('api.vendor.getVendor'), ['class' => "form-control", 'id' => "url-vendor"] ) !!}
        {!! $errors->first("vendor_id", '<span class="help-block">:message</span>') !!}
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div lass='form-group{{ $errors->has("category_id") ? ' has-error' : '' }}'>
                {!! Form::label("category_id", trans('Category')) !!} <span class="text-danger">*</span>
                {!! Form::select("category_id", ["" => ""] + $categoryVendor, old("category_id", $portfolio->category_id), ['class' => "form-data-category form-control category", 'id' => "category"]) !!}
                {!! Form::hidden("url_category", route('api.category.get'), ['class' => "form-control", 'id' => "url-category"] ) !!}
                {!! Form::hidden("token", csrf_token(), ['class' => "form-control", 'id' => "token"] ) !!}
                {!! $errors->first("category_id", '<span class="help-block">:message</span>') !!}
            </div>   
        </div>
        <div class="col-sm-6">
            <div class='form-group{{ $errors->has("sub_category_id") ? ' has-error' : '' }}'>
                {!! Form::label("Sub Category", trans('Sub Category')) !!}
                {!! Form::select("sub_category_id", ["" => ""] + $subCategories, old("sub_category_id", $portfolio->sub_category_id), ['class' => "form-data-subcategory form-control sub_category", 'id' => "sub_category"]) !!}
                {!! $errors->first("sub_category_id", '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
    <div class='form-group{{ $errors->has("city") ? ' has-error' : '' }}'>
        {!! Form::label("city", trans('City')) !!} <span class="text-danger">*</span>
        {!! Form::text("city", Input::old('city',$portfolio->city), ['class' => "form-control", 'placeholder' => trans('City')] ) !!}
        {!! $errors->first("city", '<span class="help-block">:message</span>') !!}
    </div>
     <div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
        {!! Form::label("title", trans('Title')) !!} <span class="text-danger">*</span>
        {!! Form::text("title", Input::old('title',$portfolio->title), ['class' => "form-control", 'placeholder' => trans('Title')] ) !!}
        {!! $errors->first("title", '<span class="help-block">:message</span>') !!}
    </div>
   <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
        {!! Form::label("description", trans('portfolio::portfolios.form.description')) !!}
        {!! Form::textarea("description", Input::old('description',$portfolio->description), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('portfolio::portfolios.form.description')]) !!}
        {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
    </div>    
    <div class='form-group{{ $errors->has("photography") ? ' has-error' : '' }}'>
        {!! Form::label("photography", trans('portfolio::portfolios.form.photography')) !!}
        {!! Form::text("photography", Input::old('photography',$portfolio->photography), ['class' => "form-control", 'placeholder' => trans('portfolio::portfolios.form.photography')]) !!}
        {!! $errors->first("photography", '<span class="help-block">:message</span>') !!}
    </div>  
</div>  
