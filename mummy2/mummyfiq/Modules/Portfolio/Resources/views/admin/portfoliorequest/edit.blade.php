@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('portfolio::portfoliorequest.title.edit portfolio request') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.portfolio.portfolio.index') }}">{{ trans('portfolio::portfolios.title.portfolios') }}</a></li>
        <li class="active">{{ trans('portfolio::portfoliorequest.title.edit portfolio request') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.portfolio.portfoliorequest.update', $portfolio->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#general_tab" data-toggle="tab">{{ trans('portfolio::portfolios.tabs.general') }}</a></li>
                    <li class=""><a href="#image_tab" data-toggle="tab">{{ trans('portfolio::portfolios.tabs.image') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="general_tab">
                        <?php $i = 0; ?>
                        @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                            <?php $i++; ?>
                            <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                                @include('portfolio::admin.portfolios.partials.edit-fields', ['lang' => $locale])
                            </div>
                        @endforeach
                    </div>
                    <div class="tab-pane" id="image_tab">
                        <div class="form-group">
                            @include('portfolio::admin.portfoliorequest.partials.file-link-multiple', [
                                'entityClass' => 'Modules\\\\Portfolio\\\\Entities\\\\Portfolio',
                                'entityId' => $portfolio->id,
                                'zone' => 'image',
                            ])
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! Form::hidden('previousUrl', $previousUrl) !!}
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                        <!-- <button class="btn btn-default btn-flat" name="button" type="reset">{{ trans('core::core.button.reset') }}</button> -->
                        <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.portfolio.portfolio.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('footer')
    <!-- <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp; -->
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@section('scripts')
    {!! Theme::script('js/custom/select-vendor-category.js') !!}
    {{-- {!! Theme::script('js/custom/select-category.js') !!} --}}
    <script type="text/javascript">
        $( document ).ready(function() {
            $(".form-data-category").select2();
            $(".form-data-subcategory").select2();
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.portfolio.portfolio.index') ?>" }
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
