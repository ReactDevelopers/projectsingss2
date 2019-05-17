<div class="box-body">
	<div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
	    {!! Form::label("title", trans('advertisement::advertisements.form.title')) !!} <span id="span-required" class="text-danger">*</span>
	    {!! Form::text('title', Input::old('title'), ['class' => 'form-control', 'placeholder' => trans('advertisement::advertisements.form.title')]) !!}
	    {!! $errors->first("title", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("description", trans('advertisement::advertisements.form.description')) !!}
	    {!! Form::textarea('description', Input::old('description'), ['class' => 'form-control', 'rows' => '3', 'placeholder' => trans('advertisement::advertisements.form.description')]) !!}
	    {!! $errors->first("description", '<span class="help-block">:message</span>') !!}
	</div>
	<div class="form-group {{ $errors->has("medias_single") ? ' has-error' : '' }}">
		@include('media::admin.fields.new-file-link-single', [
		    'zone' => 'image'
		])
		{!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("adv_id") ? ' has-error' : '' }}'>
	    {!! Form::label("adv_id", trans('advertisement::advertisements.form.type')) !!}
	    {!! Form::select("adv_id", $types, old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("adv_id", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("link") ? ' has-error' : '' }}' id="adv-link">
	    {!! Form::label("link", trans('advertisement::advertisements.form.link')) !!} <span id="span-required" class="text-danger">*</span>
	    {!! Form::text('link', Input::old('link'), ['class' => 'form-control', 'placeholder' => trans('advertisement::advertisements.form.link')]) !!}
	    {!! $errors->first("link", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("by") ? ' has-error' : '' }}' id="adv-by">
	    {!! Form::label("by", trans('advertisement::advertisements.form.by')) !!} <span id="span-required" class="text-danger">*</span>
	    {!! Form::text('by', Input::old('by'), ['class' => 'form-control', 'placeholder' => trans('advertisement::advertisements.form.by')]) !!}
	    {!! $errors->first("by", '<span class="help-block">:message</span>') !!}
	</div>
	{{--  	
	<div class='form-group{{ $errors->has("sorts") ? ' has-error' : '' }}'>
	    {!! Form::label("sorts", trans('advertisement::advertisements.form.sort')) !!}
	    {!! Form::text('sorts', Input::old('sorts'), ['class' => 'form-control', 'placeholder' => trans('advertisement::advertisements.form.sort')]) !!}
	    {!! $errors->first("sorts", '<span class="help-block">:message</span>') !!}
	</div> 
	--}}
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('advertisement::advertisements.form.status')) !!}
	    {!! Form::select("status", Config('asgard.advertisement.config.status'), old("adv_id"), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div>
</div>


