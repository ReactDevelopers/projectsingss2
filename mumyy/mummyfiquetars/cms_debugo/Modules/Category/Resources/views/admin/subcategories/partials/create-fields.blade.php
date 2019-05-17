<div class="box-body">
	<div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
	    {!! Form::label("name", trans('category::subcategories.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("name", Input::old('name'), ['class' => "form-control", 'placeholder' => trans('category::subcategories.form.name')] ) !!}
	    {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("description", trans('category::subcategories.form.description')) !!}
	    {!! Form::textarea("description", Input::old('description'), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('category::subcategories.form.description')]) !!}
	    {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
	</div>
{{-- 	<div class='form-group{{ $errors->has("category_id") ? ' has-error' : '' }}'> 
	    {!! Form::label("category_id", trans('category::subcategories.form.parent category')) !!} <span class="text-danger">*</span>
	    {!! Form::select("category_id", $categories, old("category_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("category_id", '<span class="help-block">:message</span>') !!}
	</div> --}}
<!-- 	<div class='form-group{{ $errors->has("sorts") ? ' has-error' : '' }}'>
	    {!! Form::label("sorts", trans('category::subcategories.form.sort')) !!}
	    {!! Form::text('sorts', Input::old('sorts'), ['class' => 'form-control', 'placeholder' => trans('category::subcategories.form.sort')]) !!}
	    {!! $errors->first("sorts", '<span class="help-block">:message</span>') !!}
	</div> -->
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('category::subcategories.form.status')) !!}
	    {!! Form::select("status", Config('asgard.category.config.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>
</div>
