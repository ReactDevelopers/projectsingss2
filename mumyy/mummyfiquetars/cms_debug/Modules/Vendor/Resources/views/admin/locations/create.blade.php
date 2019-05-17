@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('vendor::locations.title.create location') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.vendor.location.index', $vendor->id) }}">{{ trans('vendor::locations.title.location') }}</a></li>
        <li class="active">{{ trans('vendor::locations.title.create location') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.vendor.location.store', $vendor->id], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('vendor::admin.locations.partials.create-fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        {!! Form::hidden('vendor_id', $vendor->id, ['id' => 'vendor_id']) !!}
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.create') }}</button>
                        <!-- <button class="btn btn-default btn-flat" name="button" type="reset">{{ trans('core::core.button.reset') }}</button> -->
                        <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.vendor.location.index', $vendor->id)}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
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
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.vendor.vendor.index') ?>" }
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
    <script type="text/javascript">

        $(document).ready(function(){      
            // $("#state").trigger("change");  
            $(".js-example-basic-single").select2();
            $("#country").trigger("change" );   
        });
    
        $(document).on("change", "#country",function(e){
            e.preventDefault();
            var country_id = $("#country").val();

            fetch_city(country_id);
            fetch_phonecode(country_id);
        });

        function fetch_city(country_id) 
        {
            $.ajax({
                 type: 'post',
                 url: '{{ route('api.vendor.getCity') }}',
                 data: {
                   'country_id':country_id,
                   '_token': '{{ csrf_token() }}',
                 },
                 dataType: 'json',
                 success: function (response) {
                    var cities = html = "";
                    var x;
                    var i = 0;
                    var data = response.data;

                    for (x in data.cities) {
                        if(i == 0) {
                            var city_id = x;
                        }
                        cities += "<option value="+x+">"+data.cities[x]+"</option>";
                        i++;
                    }

                    $("#city").html(cities);
                 }
            });
        }
        function fetch_phonecode(country_id) 
        {
            $.ajax({
                 type: 'post',
                 url: '{{ route('api.vendor.getLocationPhonecode') }}',
                 data: {
                   'country_id':country_id,
                   '_token': '{{ csrf_token() }}',
                 },
                 dataType: 'json',
                 success: function (response) {
                    var phonecode = "";
                    if(response.status){
                        phonecode = response.data;

                        $('#vendor-phonecode').val("+" + phonecode);
                    }
                    else{
                        console.log(response.message);
                    }
                 }
            });
        }
        $(document).on("click", "#link-add-phone_number", function(e){
            e.preventDefault();
            var html = '<div class="row new-phone-number">' +
                            '<div class="col-sm-2">' +
                            '</div>' +
                            '<div class="col-sm-8">' +
                                '<div class="form-group">' +
                                    '<input type="number" name="business_phone[]" class="form-control" placeholder="Business Phone Number" id="vendor-business-phone">' +
                                '</div>' +
                            '</div>' +
                            '<div class="col-sm-2">' +
                                '<div class="form-group" style="padding-top: 5px;">' +
                                    '<a href="javascript:avoid(0)" id="link-remove-phone_number" class="link-remove-phone_number">Remove</a>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
            $('#append-phone_number').append(html);
        });

        $(document).on("click", ".link-remove-phone_number", function(e){
            e.preventDefault();
            $(this).parents(".new-phone-number").remove();
            //$(this).closest(".link-remove-phone_number").parents(".row").remove();
        });
    </script>
@stop
