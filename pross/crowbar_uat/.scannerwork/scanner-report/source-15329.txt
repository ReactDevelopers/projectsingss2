<div class="panel-body">
    @foreach($description_lng as $lng)
        <div class="form-group">
            <label for="name">{{$language[$lng['language']]}}</label>
            <div>
                {!! nl2br($lng['description']) !!}
            </div>
        </div>
    @endforeach
</div>
