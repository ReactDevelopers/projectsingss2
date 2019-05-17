<div class="box-body">
   <div class='form-group{{ $errors->has("comment") ? ' has-error' : '' }}'>
	    {!! Form::label("Content", trans('Content')) !!}
	    {!! Form::textarea("Content", Input::old('content',$comment->comment), ['class' => "form-control",'readonly' => "readonly", 'rows' => 10, 'placeholder' => trans('Content')]) !!}
	</div>
	<div class='form-group'>
	    {!! Form::label("Status", trans('Status')) !!}
	    <select class="form-control" name="status">
	    	@if($comment->status == 1)
	    		<option value="0">InActive</option>
	    		<option value="1" selected="selected">Active</option>
	    	@else
	    		<option value="0" selected="selected">InActive</option>
	    		<option value="1">Active</option>
	    	@endif
	    	
	    </select>
	</div>
</div>
