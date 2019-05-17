<div class="box-body">
    <div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
	    {!! Form::label("name", trans('package::packageservices.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("name", Input::old('name', $packageservice->name), ['class' => "form-control", 'placeholder' => trans('package::packageservices.form.name')] ) !!}
	    {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("description", trans('package::packageservices.form.description')) !!}
	    {!! Form::textarea("description", Input::old('description', $packageservice->description), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('package::packageservices.form.description')]) !!}
	    {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('package::packageservices.form.status')) !!}
	    {!! Form::select("status", Config('constant.status'), old("status", $packageservice->status), ['class' => "form-control"]) !!}
	    {!! Form::hidden('id', Input::old('id', $packageservice->id), ['class' => 'form-control']) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>
