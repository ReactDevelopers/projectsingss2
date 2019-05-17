@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('vendor::vendors.title.create vendor') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.vendor.vendor.index') }}">{{ trans('vendor::vendors.title.vendors') }}</a></li>
        <li class="active">{{ trans('vendor::vendors.title.create vendor') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.vendor.vendor.store'], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                {{--
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1-1" data-toggle="tab">{{ trans('vendor::vendors.tabs.infomation') }}</a></li>
                    <li class=""><a href="#tab_2-2" data-toggle="tab">{{ trans('vendor::vendors.tabs.business') }}</a></li>
                    <li class=""><a href="#tab_3-3" data-toggle="tab">{{ trans('vendor::vendors.tabs.infomation') }}</a></li>
                </ul>
                --}}
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('vendor::admin.vendors.partials.create-fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.create') }}</button>
                        <!-- <button class="btn btn-default btn-flat" name="button" type="reset">{{ trans('core::core.button.reset') }}</button> -->
                        <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.vendor.vendor.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
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
            $("#category").trigger("change");                        
        });

        function fetch_category(val) 
        {
           $.ajax({
             type: 'post',
             url: '{{ route('api.category.fetch') }}',
             data: {
               'category_id':val,
               '_token': '{{ csrf_token() }}',
             },
             dataType: 'json',
             success: function (response) {
                console.log(response); 
                var text = "";
                var x;
                var i = 0;
               
                for (x in response.data) {
                    text += "<option value="+x+">"+response.data[x]+"</option>";
                    i++;
                }

                //console.log(response);
//              text = '<select class="form-control" id="childcare_type" name="'+name+'">' + text + '</select>';
                $("#sub_category").html(text);
//                 document.getElementById("childcare_type").outerHTML=text; 
             }
           });
        }
        function fetch_state(val) 
        { 
           $.ajax({
             type: 'post',
             url: '{{ route('api.vendor.getState') }}',
             data: {
               'country_id':val,
               '_token': '{{ csrf_token() }}',
             },
             dataType: 'json',
             success: function (response) {
                console.log(response); 
                var text = textCities = "";
                var x;
                $state_id = response.state_id;
                for (x in response.data) {
                    text += "<option value="+x+">"+response.data[x]+"</option>";
                }
                for (x in response.dataCities) {
                    textCities += "<option value="+x+">"+response.dataCities[x]+"</option>";
                }
                $("#state").html(text);
                $("#city").html(textCities);
             }
           });
        }

        function fetch_city(val) 
        {
            $.ajax({
                 type: 'post',
                 url: '{{ route('api.vendor.getCity') }}',
                 data: {
                   'country_id':val,
                   '_token': '{{ csrf_token() }}',
                 },
                 dataType: 'json',
                 success: function (response) {
                    var cities = phonecodes = html = "";
                    var x;
                    var i = 0;
                    var item = "";
                    var data = response.data;

                    for (x in data.cities) {
                        cities += "<option value="+x+">"+data.cities[x]+"</option>";
                        i++;
                    }

                    if(data.phonecodes.length){
                        for( i = 0; i < data.phonecodes.length; i++ ){
                            item = data.phonecodes[i];
                            if(item.phonecode == data.phonecode){
                                phonecodes += '<option value="'+ item.phonecode +'" selected>+'+ item.phonecode +'</option>';
                            }else{
                                phonecodes += '<option value="'+ item.phonecode +'">+'+ item.phonecode +'</option>';
                            }
                        }
                    }

                    $("#vendor-phonecode").html(phonecodes);
                    console.log(phonecodes);
                    $("#city").html(cities);
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
