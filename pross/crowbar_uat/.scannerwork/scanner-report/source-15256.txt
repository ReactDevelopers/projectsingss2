@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="name">Question:</label>
                            <br />{{$ques->question_description}}

                            <div>
                                <a href="javascript:;" id="main-reply" class="add-reply btn-link btn-reply">Add Reply</a>
                                @if($ques->status == 'pending')
                                <span id="id-app-ans">
                                    |
                                    <a href="javascript:;" data-id_question="{{$ques->id_question}}" id="ques-approve" class="add-reply">Approve</a>
                                </span>
                                @endif

                            </div>
                        </div>

                        <div id="main-reply-area" style="display: none;">
                            <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/forum/question/reply/'.$id_question,ADMIN_FOLDER)) }}">
                                <input type="hidden" name="_method" value="PUT">
                                {{ csrf_field() }}
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="name">Reply</label>
                                        <textarea class="form-control" name="answer_description" placeholder="Reply"></textarea>
                                    </div>
                                    <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-link btn-reply">Submit</button>
                                </div>
                            </form>
                        </div>
                        @if(!empty($answer->count()))
                            <div class="form-group">
                                <label for="name">Answer: </label>
                                <br />
                                @foreach($answer as $ans)
                                    <div class="reply-display-area-{{$ans->id_answer}}">
                                        {{$ans->answer_description}}
                                    </div>
                                    <div class="reply-display-area-{{$ans->id_answer}}">
                                        <span class="reply-heading">Reply by: <span>{{$ans->person_name}}</span></span>
                                        <span class="reply-heading">Reply on: <span>{{$ans->created}}</span></span>
                                    </div>
                                    <div class="action reply-display-area-{{$ans->id_answer}}">
                                        <a href="javascript:;" class="btn-link btn-reply add-reply" onclick="addReply({{$ans->id_answer}}, {{$ans->id_question}})">Add Reply</a>
                                        &nbsp;|&nbsp;
                                        @if($ans->has_child == 1)
                                        <a href="javascript:;" onclick="loadReply({{$ans->id_answer}}, {{$ans->id_question}})">View Reply</a>
                                        &nbsp;|&nbsp;
                                        @endif
                                        @if($ans->status == 'Pending')
                                            <span id="anrwer-{{$ans->id_answer}}">
                                            <a href="javascript:;" class="approve-reply" data-id_answer="{{$ans->id_answer}}">Approve</a>
                                            |
                                            </span>
                                        @endif
                                        <a href="javascript:;" class="delete-reply" onclick="deleteReply({{$ans->id_answer}}, {{$ans->id_question}})">Delete</a>
                                    </div>
                                    <div id="text-reply-area-{{$ans->id_answer}}" style="display: none;">
                                        <div class="form-group" id="text-reply-section-{{$ans->id_answer}}">
                                            <label for="name">Reply</label>
                                            <textarea class="form-control" name="answer_description" id="answer_description_{{$ans->id_answer}}" placeholder="Reply"></textarea>
                                        </div>
                                        <button onclick="insertReply({{$ans->id_answer}}, {{$ans->id_question}})" type="button" class="btn btn-link btn-reply">Add Reply</button>
                                    </div>
                                    <hr class="reply-display-area-{{$ans->id_answer}}">
                                    <div id="reply-area-{{$ans->id_answer}}"></div>
                                @endforeach
                            </div>
                        @endif
                        <div class="paginationSection">
                            <nav aria-label="Page navigation example">
                                {{ $answer->links()}}
                            </nav>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="add-reply" value="{{url('administrator/forum/answer/add')}}" />
    <input type="hidden" id="list-reply" value="{{url('administrator/forum/answer/reply')}}" />
    <input type="hidden" id="delete-reply" value="{{url('administrator/forum/answer/delete')}}" />
    <input type="hidden" id="approve-question" value="{{url('administrator/forum/question/update')}}" />
    <input type="hidden" id="approve-answer" value="{{url('administrator/forum/answer/update')}}" />
@endsection

@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
    $('#main-reply').click(function(){
        $('#main-reply-area').toggle();
    });

    $(document.body).on('click', '.approve-reply', function(){
        var id_answer = $(this).data('id_answer');
        var url = $('#approve-answer').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_answer > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_answer: id_answer}
            })
            .done(function(data) {
                $('#anrwer-'+id_answer).hide();
                swal({
                    title: '',
                    html: data.message,
                    showLoaderOnConfirm: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick:false,
                    confirmButtonText: 'Okay',
                    cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                    confirmButtonColor: '#0FA1A8',
                    cancelButtonColor: '#CFCFCF'
                });
            });
        }
    });

    $(document.body).on('click', '#ques-approve', function(){
        var id_question = $(this).data('id_question');
        var url = $('#approve-question').val();
        var isconfirm = confirm('Do you really want to continue with this action?');

        if(isconfirm && id_question > 0){
            $.ajax({
                method: "POST",
                url: url,
                data: { id_question: id_question}
            })
            .done(function(data) {
                $('#id-app-ans').hide();
                swal({
                    title: '',
                    html: data.message,
                    showLoaderOnConfirm: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick:false,
                    confirmButtonText: 'Okay',
                    cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                    confirmButtonColor: '#0FA1A8',
                    cancelButtonColor: '#CFCFCF'
                });
            });
        }
    });
});
function addReply(id_reply, id_ques){
    $('#text-reply-area-'+id_reply).toggle();
}
function insertReply(id_reply, id_ques){
    var add_reply_url = $('#add-reply').val();
    var answer_description = $('#answer_description_'+id_reply).val();
    if(answer_description.length <= 0){
        $('#text-reply-section-'+id_reply).addClass('has-error');
        $('#text-reply-section-'+id_reply).append('<div class="help-block">The reply field is required.</div>');
    }
    else{
        $('#text-reply-section-'+id_reply).removeClass('has-error');
    }
    if(id_reply > 0 && answer_description.length > 0){
        $.ajax({
        method: "POST",
        url: add_reply_url,
        data: { id_reply: id_reply, id_ques: id_ques, answer_description: answer_description}
        })
        .done(function(data) {
            $('#text-reply-area-'+id_reply).toggle();
            $('#answer_description_'+id_reply).val('');
            loadReply(id_reply, id_ques)
        });
    }
}
function loadReply(id_reply, id_ques){
    var reply_list_url = $('#list-reply').val();
    if(id_reply > 0){
        $.ajax({
        method: "POST",
        url: reply_list_url,
        data: { id_reply: id_reply, id_ques: id_ques}
        })
        .done(function(data) {
            $("#reply-area-"+id_reply).html(data);
        });
    }
}
function deleteReply(id_reply, id_ques){
    var isconfirm = confirm('Do you really want to continue with this action?');
    if(isconfirm){
        var reply_delete_url = $('#delete-reply').val();
        if(id_reply > 0){
            $.ajax({
            method: "POST",
            url: reply_delete_url,
            data: { id_reply: id_reply, id_ques: id_ques}
            })
            .done(function(data) {
                $(".reply-display-area-"+id_reply).remove();
            });
        }
    }
}
</script>
@endpush
