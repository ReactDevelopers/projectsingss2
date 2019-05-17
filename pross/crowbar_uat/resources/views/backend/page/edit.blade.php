@extends('layouts.backend.dashboard')

@section('requirejs')
    <script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.extraAllowedContent = "div(*)";
        CKEDITOR.replace('message-content');
    </script>
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form" method="post" enctype="multipart/form-data" action="{{ url($url.'/page/'.$page['id'].'/update') }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}
                        <div class="panel-body">
                            <div class="form-group @if ($errors->has('title'))has-error @endif">
                                <label for="name">Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Title" value="{{ (old('title'))?old('title'):$page['title'] }}">
                                @if ($errors->first('title'))
                                    <span class="help-block">
                                        {{ $errors->first('title')}}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group @if ($errors->has('content'))has-error @endif">
                                <label for="content">Description</label>
                                <textarea class="form-control" id="message-content" placeholder="Content" name="content">{{ $page['content'] }}</textarea>
                                @if ($errors->first('content'))
                                    <span class="help-block">
                                        {{ $errors->first('content')}}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="panel-footer">
                            <a href="{{url($url.'/pages')}}" class="btn btn-default">Back</a>
                            <button type="submit" class="btn btn-default">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
