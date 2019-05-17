<div class="box-body">
	<div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
	    {!! Form::label("name", trans('category::categories.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("name", Input::old('name'), ['class' => "form-control", 'placeholder' => trans('category::categories.form.name')] ) !!}
	    {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("description", trans('category::categories.form.description')) !!}
	    {!! Form::textarea("description", Input::old('description'), ['class' => "form-control", 'rows' => 5, 'placeholder' => trans('category::categories.form.description')]) !!}
	    {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
	</div>
<!-- 	<div class='form-group{{ $errors->has("sorts") ? ' has-error' : '' }}'>
	    {!! Form::label("sorts", trans('category::categories.form.sort')) !!}
	    {!! Form::text('sorts', Input::old('sorts'), ['class' => 'form-control', 'placeholder' => trans('category::categories.form.sort')]) !!}
	    {!! $errors->first("sorts", '<span class="help-block">:message</span>') !!}
	</div> -->
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('category::categories.form.status')) !!}
	    {!! Form::select("status", Config('asgard.category.config.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
	<div class="form-group {{ $errors->has("medias_single") ? ' has-error' : '' }}">
		@include('media::admin.fields.new-file-link-single', [
		    'zone' => 'image'
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
	</div>
</div>
