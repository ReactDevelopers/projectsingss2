<div class="box-body">
    <div class='form-group{{ $errors->has("first_name") ? ' has-error' : '' }}'>
	    {!! Form::label("first_name", trans('customer::customers.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("first_name", Input::old('first_name', $customer->first_name ? trim($customer->first_name . ' ' . $customer->last_name) : $customer->name), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.name')] ) !!}
	    {!! $errors->first("first_name", '<span class="help-block">:message</span>') !!}
	</div>
	{{--
	<div class='form-group{{ $errors->has("last_name") ? ' has-error' : '' }}'>
	    {!! Form::label("last_name", trans('customer::customers.form.last name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("last_name", Input::old('last_name', $customer->last_name), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.last name')] ) !!}
	    {!! $errors->first("last_name", '<span class="help-block">:message</span>') !!}
	</div>
	--}}
	<div class='form-group{{ $errors->has("email") ? ' has-error' : '' }}'>
	    {!! Form::label("email", trans('customer::customers.form.email')) !!} <span class="text-danger">*</span>
	    {!! Form::text("email", Input::old('email', str_replace('@', '&#64;', $customer->email)), ['class' => "form-control", 'readonly', 'placeholder' => trans('customer::customers.form.email')] ) !!}
	    {!! $errors->first("email", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("password") ? ' has-error' : '' }}'>
	    {!! Form::label("password", trans('customer::customers.form.password')) !!}
	    {!! Form::password("password", ['class' => "form-control", 'placeholder' => '**********'] ) !!}
	    {!! $errors->first("password", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("phone") ? ' has-error' : '' }}'>
	    {!! Form::label("phone", trans('customer::customers.form.phone')) !!}
	    {!! Form::text("phone", Input::old('phone', $phone ? $phone->phone_number : ""), ['class' => "form-control", 'placeholder' => trans('customer::customers.form.phone')] ) !!}
	    {!! $errors->first("phone", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('customer::customers.form.status')) !!}
	    {!! Form::select("status", Config('asgard.customer.config.status'), old("status", $customer->status), ['class' => "form-control"]) !!}
	    {!! Form::hidden('id', Input::old('id', $customer->id), ['class' => 'form-control']) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>
@section('custom-styles')
<style>
input[type="password"]::-webkit-input-placeholder { /* Chrome/Opera/Safari */
  color: black;
}
input[type="password"]::-moz-placeholder { /* Firefox 19+ */
  color: black;
}
input[type="password"]:-ms-input-placeholder { /* IE 10+ */
  color: black;
}
input[type="password"]:-moz-placeholder { /* Firefox 18- */
  color: black;
}
</style>
@stop