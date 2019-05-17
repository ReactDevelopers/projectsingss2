<div class="form-group" style="padding-left: 20px;">
    @foreach($answer as $ans)
        <div class="reply-display-area-{{$ans['id_answer']}}">
            {{$ans['answer_description']}}
        </div>
        <div class="reply-display-area-{{$ans['id_answer']}}">
            <span class="reply-heading">Reply by: <span>{{$ans['person_name']}}</span></span>
            <span class="reply-heading">Reply on: <span>{{$ans['created']}}</span></span>
        </div>
        @if($ans['status'] == 'Pending')
            <span id="anrwer-{{$ans['id_answer']}}">
                <a href="javascript:;" class="approve-reply" data-id_answer="{{$ans['id_answer']}}">Approve</a>
            </span>
        @endif
        @if(0)
            <div class="action reply-display-area-{{$ans['id_answer']}}">
                <a href="javascript:;" class="add-reply" onclick="addReply({{$ans['id_answer']}}, {{$ans['id_question']}})">Add Reply</a>
                &nbsp;|&nbsp;
                @if($ans['has_child'] == 1)
                <a href="javascript:;" onclick="loadReply({{$ans['id_answer']}}, {{$ans['id_question']}})">View Reply</a>
                &nbsp;|&nbsp;
                @endif
                
                <a href="javascript:;" onclick="deleteReply({{$ans['id_answer']}}, {{$ans['id_question']}})">Delete</a>
            </div>
        @endif
        <div id="text-reply-area-{{$ans['id_answer']}}" style="display: none;">
            <div class="form-group" id="text-reply-section-{{$ans['id_answer']}}">
                <label for="name">Reply</label>
                <textarea class="form-control" name="answer_description" id="answer_description_{{$ans['id_answer']}}" placeholder="Reply"></textarea>
            </div>
            <button onclick="insertReply({{$ans['id_answer']}}, {{$ans['id_question']}})" type="button" class="btn btn-link btn-reply">Add Reply</button>
        </div>
        <hr class="reply-display-area-{{$ans['id_answer']}}">
        <div class="reply-display-area-{{$ans['id_answer']}}" id="reply-area-{{$ans['id_answer']}}"></div>
    @endforeach
</div>
