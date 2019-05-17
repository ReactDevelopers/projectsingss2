@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/forum/question/reply/'.$id_question,ADMIN_FOLDER)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Question: </label>
                                {{$ques->question_description}}
                            </div>
                            <div class="form-group">
                                <label for="name">Reply</label>
                                <textarea class="form-control" name="answer_description" placeholder="Reply">{{ old('answer_description') }}</textarea>
                            </div>

                        </div>
                        <div class="panel-footer">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('inlinescript')
<script type="text/javascript">

</script>
@endpush
