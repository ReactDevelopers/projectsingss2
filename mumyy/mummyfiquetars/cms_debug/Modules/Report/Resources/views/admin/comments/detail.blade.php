@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('report::comments.title.detail comment') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.report.comment.index') }}">{{ trans('report::comments.title.comments') }}</a></li>
        <li class="active">{{ trans('report::comments.title.edit comment') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <div class="box-body">
                       <div class='form-group{{ $errors->has("description") ? ' has-error' : '' }}'>
                            {!! Form::label("Content", trans('Content')) !!}
                            {!! Form::textarea("Content", Input::old('content',$comment->content), ['class' => "form-control",'readonly' => "readonly", 'rows' => 10, 'placeholder' => trans('Content')]) !!}
                        </div>
                    </div>

                    <div class="box-footer">
                        <a class="btn btn-primary btn-flat" href="{{ route('admin.report.comment.index')}}">{{ trans('core::core.button.back') }}</a>
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
                    { key: 'b', route: "<?= route('admin.report.comment.index') ?>" }
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
