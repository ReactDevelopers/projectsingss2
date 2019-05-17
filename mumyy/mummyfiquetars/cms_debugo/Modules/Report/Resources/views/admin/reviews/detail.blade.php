@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('report::reviews.title.detail review') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.report.review.index') }}">{{ trans('report::reviews.title.reviews') }}</a></li>
        <li class="active">{{ trans('report::reviews.title.edit review') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop
<?php
    $reportReviewTitle = ['Scams','Inappropriate Language','Spamming Advertisements/Links','Others'];
    $review_reason = [
        'contains_offensive_content' => 'Contains Offensive Content',
        'contains_copyright_violation' => 'Contains Copyright Violation',
        'contains_adult_content' => 'Contains Adult Content',
        'invades_my_privacy' => 'Invades My Privacy',
    ];
    $title = '';
    if($review->reason)
    {
        $title = $review_reason[$review->reason];
    }
    $content = $review->content;
    foreach ($reportReviewTitle as $keyTitleReport => $valueTitleReport) {
        if(is_numeric(strpos($review->content, $valueTitleReport )))
        {
            $result = explode ( $valueTitleReport , $review->content);
            $title = $valueTitleReport;
            $content1 = str_replace( '/n', '', $result[1] );
            $content2 = str_replace( 'Optional("', '', $content1 );
            $content = str_replace( '")', '', $content2 );
        }
    }
?>
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <div class="box-body">
                        <div class='form-group{{ $errors->has("title") ? ' has-error' : '' }}'>
                            {!! Form::label("Title", trans('Title')) !!}
                            {!! Form::text("Title", Input::old('content',$title), ['class' => "form-control",'readonly' => "readonly", 'rows' => 10, 'placeholder' => trans('Title')]) !!}
                        </div>
                       <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
                            {!! Form::label("Content", trans('Content')) !!}
                            {!! Form::textarea("Content", Input::old('content',$content), ['class' => "form-control",'readonly' => "readonly", 'rows' => 10, 'placeholder' => trans('Content')]) !!}
                        </div>
                    </div>

                    <div class="box-footer">
                        <a class="btn btn-primary btn-flat" href="{{ route('admin.report.review.index')}}">{{ trans('core::core.button.back') }}</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.report.review.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@stop
