@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <div class="col-md-12">
                        <h2>{{ $title }}</h2>
                    </div> 
                    <form role="form-add-faq" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'faq/post/add'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group hide">
                                <label for="type">Language</label>
                                <select class="form-control" name="language">
                                    <option value="en" selected="selected">en</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="question">Category</label>
                                 <select name="category" class="form-control">
                                    @foreach($category as $key => $value)
                                        <option value="{{$value['id_faq']}}" {{ (!empty($faq) &&($value['id_faq'] == $faq->parent)) ? 'selected=selected' : '' }}>{{$value['parent_title'].' - '.$value['title']}}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="form-group">
                                <label for="question">Title</label>
                                <input type="text" class="form-control" name="title" value="{{ !empty($faq) ? $faq->title : '' }}" placeholder="Title" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">Description</label>
                                <textarea class="form-control" id="description" placeholder="Description" name="description">{{ !empty($faq) ? $faq->description : '' }}</textarea>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" name="id_faq" value="{{ !empty($faq) ? ___encrypt($faq->id_faq) : ''}}">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="form-add-faq"]' class="btn btn-default">Save</button>
                        </div>
                    </form>                                      
                </div>
            </div>
        </div>
    </section>
@endsection
@section('requirejs')
    <script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.extraAllowedContent = "div(*)";
        CKEDITOR.replace('description', { 
            on:{
                'instanceReady': function(evt) {
                    evt.editor.document.on('keyup', function() {
                        document.getElementById('description').value = evt.editor.getData();
                    });

                    evt.editor.document.on('paste', function() {
                        document.getElementById('description').value = evt.editor.getData();
                    });
                }
            }
        });
    </script>
@endsection



