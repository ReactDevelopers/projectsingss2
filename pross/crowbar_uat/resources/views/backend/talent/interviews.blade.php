@foreach($questionList as $key => $item)
<div class="box-body" style="display: block;">
    {{ $item->question_type }}
    <span class="pull-right badge bg-blue">{{ $item->response_total }}/{{ $item->ques_total }}</span>
</div>
@if(!empty($item->question))
    <div class="box-footer box-comments" style="display: block;">
    @foreach($item->question as $child_key => $child_item)
        <div class="box-comment">
            <div class="comment-text">
                <span class="username">
                    {{ $child_item->question }}
                    <span class="pull-right badge bg-blue">{{ $child_item->question_rate }}/{{ QUESTION_RATE_LENGTH }}</span>
                </span>
                {{ $child_item->question_comment }}
            </div>
        </div>
    @endforeach
    </div>
@endif
@endforeach
