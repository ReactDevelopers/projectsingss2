<div class="box-body">
	<input type="text" name="email" style="display:none"> 
	<input type="password" name="password" autocomplete="new-password" style="display:none">
    <div class='form-group{{ $errors->has("first_name") ? ' has-error' : '' }}'>
	    {!! Form::label("first_name", trans('customer::customers.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("first_name", Input::old('first_name'), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.name')] ) !!}
	    {!! $errors->first("first_name", '<span class="help-block">:message</span>') !!}
	</div>
	{{--
	<div class='form-group{{ $errors->has("last_name") ? ' has-error' : '' }}'>
	    {!! Form::label("last_name", trans('customer::customers.form.last name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("last_name", Input::old('last_name'), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.last name')] ) !!}
	    {!! $errors->first("last_name", '<span class="help-block">:message</span>') !!}
	</div>
	--}}
	<div class='form-group{{ $errors->has("email") ? ' has-error' : '' }}'>
	    {!! Form::label("email", trans('customer::customers.form.email')) !!} <span class="text-danger">*</span>
	    {!! Form::text("email", Input::old('email'), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.email')] ) !!}
	    {!! $errors->first("email", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("password") ? ' has-error' : '' }}'>
	    {!! Form::label("password", trans('customer::customers.form.password')) !!} <span class="text-danger">*</span>
	    {!! Form::password("password", ['class' => "form-control", 'placeholder' => trans('customer::customers.form.password')] ) !!}
	    {!! $errors->first("password", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("phone") ? ' has-error' : '' }}'>
	    {!! Form::label("phone", trans('customer::customers.form.phone')) !!}
	    {!! Form::text("phone", Input::old('phone'), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.phone')] ) !!}
	    {!! $errors->first("phone", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('customer::customers.form.status')) !!}
	    {!! Form::select("status", Config('asgard.customer.config.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
</div>
