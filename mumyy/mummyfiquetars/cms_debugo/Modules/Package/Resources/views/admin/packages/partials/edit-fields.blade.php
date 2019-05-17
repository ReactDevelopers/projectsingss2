<div class="box-body">
    <div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
	    {!! Form::label("name", trans('package::packages.form.name')) !!} <span class="text-danger">*</span>
	    {!! Form::text("name", Input::old('name', $package->name), ['class' => "form-control", 'placeholder' => trans('package::packages.form.name')] ) !!}
	    {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("price") ? ' has-error' : '' }}'>
	    {!! Form::label("price", trans('package::packages.form.price')) !!} <span class="text-danger">*</span>
	    {!! Form::text('price', Input::old('price', $package->price), ['class' => 'form-control', 'placeholder' => trans('package::packages.form.price')]) !!}
	    {!! $errors->first("price", '<span class="help-block">:message</span>') !!}
	</div>
	{{-- <div class='form-group{{ $errors->has("type") ? ' has-error' : '' }}'>
	    {!! Form::label("type", trans('package::packages.form.type')) !!}
	    {!! Form::select('type', Config('asgard.package.config.type'), old('type', $package->type), ['class' => 'form-control']) !!}
	    {!! $errors->first("type", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("services") ? ' has-error' : '' }}'>
	    {!! Form::label("services", trans('package::packages.form.services')) !!}
	    {!! Form::textarea('services', Input::old('services', $package->services), ['class' => 'form-control', 'rows' => 5, 'placeholder' => trans('package::packages.form.services')]) !!}
	    {!! $errors->first("services", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("country_id") ? ' has-error' : '' }}'>
	    {!! Form::label("country_id", trans('package::packages.form.country')) !!}
	    {!! Form::select('country_id', $countries, old('country_id', $package->country_id), ['class' => 'form-control js-example-basic-single']) !!}
	    {!! $errors->first("country_id", '<span class="help-block">:message</span>') !!}
	</div>
	<div class='form-group{{ $errors->has("status") ? ' has-error' : '' }}'>
	    {!! Form::label("status", trans('package::packages.form.status')) !!}
	    {!! Form::select("status", Config('constant.status'), old("status", $package->status), ['class' => "form-control"]) !!}
	    {!! Form::hidden("id", old("id", $package->id), ['class' => "form-control"]) !!}
	    {!! $errors->first("status", '<span class="help-block">:message</span>') !!}
	</div> --}}
	<div class='form-group{{ $errors->has("feature") ? ' has-error' : '' }}'>
	    {!! Form::label("feature", trans('package::packages.form.features')) !!}
	    @if($features)
	    	@foreach($features as $item)
	    		<div class="checkbox">
                    <label class="">
                        <div class="icheckbox_flat-blue checked" style="position: relative;">
                        	<input name="feature[{{ $item->code }}]" type="checkbox" class="flat-blue" {{ $item->value == "Y" ? "checked" : "" }} value="{{ $item->code }}" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block ; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                        </div> {{ config('asgard.package.config.feature')[$item->code] }}
                    </label>
                </div>
	    	@endforeach
	    @endif
	    {!! Form::hidden("id", old("id", $package->id), ['class' => "form-control"]) !!}
	    {!! $errors->first("feature", '<span class="help-block">:message</span>') !!}
	</div>
</div>
