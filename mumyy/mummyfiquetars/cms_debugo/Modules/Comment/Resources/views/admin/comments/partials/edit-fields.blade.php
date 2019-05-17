<div class="box-body">
     <div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
	    {!! Form::label("title", trans('Title')) !!} <span class="text-danger">*</span>
	    {!! Form::text("title", Input::old('title',$comment->title), ['class' => "form-control",'readonly' => "readonly", 'placeholder' => trans('Title')] ) !!}
	</div>
   <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("Content", trans('Content')) !!}
	    {!! Form::textarea("Content", Input::old('content',$comment->content), ['class' => "form-control",'readonly' => "readonly", 'rows' => 5, 'placeholder' => trans('Content')]) !!}
	</div>
	 <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
	    {!! Form::label("Status", trans('Status')) !!}
	    <select name="status" class="change-active form-control">
            @if($comment->status == 1)
            <option value="1" selected="selected">Active</option>
            <option value="0">InActive</option>
            @else
            <option value="1">Active</option>
            <option value="0" selected="selected">InActive</option>
            @endif
        </select>
	</div>
</div>
