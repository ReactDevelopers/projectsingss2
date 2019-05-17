@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group">
                            <h2 >Article Title</h2>
                            <h3>{{$article['title']}}</h3>
                        </div>
                        @if(!empty($comment))
                            <div class="form-group">
                                <h4 >Comment</h4>
                                @foreach($comment as $key => $value)
                                    <ul class="">
                                        <li>
                                            {{$value->answer_desp}}<br/>
                                            <a href="javascript:;"  data-url="{{url('administrator/forum/comment/delete/'.$value->id_article_answer)}}" data-request="status" data-ask="Do you really want to delete this comment?"  class="btn btn-danger badge">Delete</a>
                                            @if(count($value->has_child_answer) > 0)
                                                @foreach($value->has_child_answer as $ckey => $cvalue)
                                                    <ul>
                                                        <li>
                                                            {{($cvalue->answer_desp)}}
                                                            <br/>
                                                            <a href="javascript:;" data-url="{{url('administrator/forum/comment/delete/'.$cvalue->id_article_answer)}}" data-request="status" data-ask="Do you really want to delete this comment?"  class="btn btn-danger badge" >Delete</a>
                                                        </li>
                                                    </ul>
                                                @endforeach
                                            @endif
                                        </li>
                                    </ul>
                                @endforeach
                            </div>
                        @else
                            <div class="form-group">
                                <p>No Records Found</p>
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer">
                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="hire-me" >
            <div >
            </div>
        </div>
    </section>
@endsection