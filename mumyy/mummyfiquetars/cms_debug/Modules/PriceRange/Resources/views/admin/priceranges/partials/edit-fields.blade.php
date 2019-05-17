<div class="box-body">
    <div class='form-group{{ $errors->has("price_name") ? ' has-error' : '' }}'>
	    {!! Form::label("price_name", trans('pricerange::priceranges.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("price_name", Input::old('name', $pricerange->price_name), ['class' => "form-control", 'placeholder' => trans('pricerange::priceranges.form.name')] ) !!}
	    {!! $errors->first("price_name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("description", trans('pricerange::priceranges.form.description')) !!}
	    {!! Form::textarea("description", Input::old('description', $pricerange->description), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('pricerange::priceranges.form.description')]) !!}
	    {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
	</div>
<!-- 	<div class='form-group{{ $errors->has("sorts") ? ' has-error' : '' }}'>
	    {!! Form::label("sorts", trans('pricerange::priceranges.form.sort')) !!}
	    {!! Form::text("sorts", Input::old('sorts', $pricerange->sorts), ['class' => "form-control", 'placeholder' => trans('pricerange::priceranges.form.sort')]) !!}
	    {!! $errors->first("sorts", '<span class="help-block">:message</span>') !!}
	</div> -->
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('pricerange::priceranges.form.status')) !!}
	    {!! Form::select("status", Config('constant.status'), old("status", $pricerange->status), ['class' => "form-control"]) !!}
	    {!! Form::hidden('id', Input::old('id', $pricerange->id), ['class' => 'form-control']) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>
