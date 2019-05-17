@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="col-md-12">
                        <h2>{{ $title }}</h2>
                    </div>           
                    <form role="form-add-faq" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'faq/category/add'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group hide">
                                <label for="type">Language</label>
                                <select class="form-control" name="language">
                                    <option value="en" selected="selected">en</option>
                                </select>
                            </div>                         
                            <div class="form-group">
                                <label for="question">Topic</label>
                                <select name="topic" class="form-control">
                                    @foreach($topic as $key => $value)
                                        <option value="{{$value['id_faq']}}" {{ (!empty($faq)&&($value['id_faq'] == $faq->parent)) ? 'selected=selected' : '' }}>{{$value['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="question">Title</label>
                                <input type="text" class="form-control" name="title" value="{{ !empty($faq) ? $faq->title : '' }}" placeholder="Title" style="width:100%;"/>
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



