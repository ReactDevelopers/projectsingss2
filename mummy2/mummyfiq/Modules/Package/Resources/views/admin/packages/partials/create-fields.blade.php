<div class="box-body">
    <div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
	    {!! Form::label("name", trans('package::packages.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("name", Input::old('name'), ['class' => "form-control", 'placeholder' => trans('package::packages.form.name')] ) !!}
	    {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("price") ? ' has-error' : '' }}'>
	    {!! Form::label("price", trans('package::packages.form.price')) !!} <span class="text-danger">*</span>
	    {!! Form::text('price', Input::old('price'), ['class' => 'form-control', 'placeholder' => trans('package::packages.form.price')]) !!}
	    {!! $errors->first("price", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("type") ? ' has-error' : '' }}'>
	    {!! Form::label("type", trans('package::packages.form.type')) !!}
	    {!! Form::select('type', Config('asgard.package.config.type'), old('type'), ['class' => 'form-control']) !!}
	    {!! $errors->first("type", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("services") ? ' has-error' : '' }}'>
	    {!! Form::label("services", trans('package::packages.form.services')) !!}
	    {!! Form::textarea('services', Input::old('services'), ['class' => 'form-control', 'rows' => 5, 'placeholder' => trans('package::packages.form.services')]) !!}
	    {!! $errors->first("services", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("country_id") ? ' has-error' : '' }}'>
	    {!! Form::label("country_id", trans('package::packages.form.country')) !!}
	    {!! Form::select('country_id', $countries, old('country_id'), ['class' => 'form-control js-example-basic-single']) !!}
	    {!! $errors->first("country_id", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('package::packages.form.status')) !!}
	    {!! Form::select("status", Config('constant.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>
