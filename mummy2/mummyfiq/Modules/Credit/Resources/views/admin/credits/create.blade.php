@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('credit::credits.title.create credit') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.credit.credit.index') }}">{{ trans('credit::credits.title.credits') }}</a></li>
        <li class="active">{{ trans('credit::credits.title.create credit') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.credit.credit.store'], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('credit::admin.credits.partials.create-fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.create') }}</button>
                        <!-- <button class="btn btn-default btn-flat" name="button" type="reset">{{ trans('core::core.button.reset') }}</button> -->
                        <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.credit.credit.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
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
    <script type="text/javascript">
        function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 45 && charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
        $( document ).ready(function() {
            $('#amount').keyup(function() {
                    if($(this).val())
                    {
                        document.getElementById("point").readOnly = true; 
                        var amount = $(this).val();
                        var point = amount / 10;
                        var pointToAdjust = Math.floor(point);
                        $('#point').val(pointToAdjust);
                    }
                    else
                    {
                        $('#point').val(''); 
                    }

                });
            $(".js-example-basic-single").select2();
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.credit.credit.index') ?>" }
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
