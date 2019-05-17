@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('version::versions.title.version') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('version::versions.title.version') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop


@section('content')
    {!! Form::open(['route' => ['admin.version.version.update'], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <div class="box-body">
                        @if(isset($configs) && !empty($configs))
                            @foreach($configs as $key =>$config)
                                <div class='form-group{{ $errors->has($config['title']) ? ' has-error' : '' }}'>
                                    {!! Form::label($config['title'], trans('version::versions.form')[$config['title']]) !!} @if($config['required'])<span class="text-danger">*</span> @endif
                                    {!! Form::$config['view']($config['title'], $item->$config['title'], ['class' => "form-control", 'placeholder' => trans('version::versions.form')[$config['title']]] ) !!}
                                    {!! $errors->first($config['title'], '<span class="help-block">:message</span>') !!}
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
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
                    { key: 'b', route: "<?= route('admin.version.version.index') ?>" }
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
