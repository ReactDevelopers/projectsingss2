<!-- @extends('layouts.master') -->

@section('content-header')
    <h1>
        {{ trans('vendor::vendors.title.edit vendor') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.vendor.vendor.index') }}">{{ trans('vendor::vendors.title.vendors') }}</a></li>
        <li class="active">{{ trans('vendor::vendors.title.edit vendor') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.vendor.vendor.update', $vendor->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                {{-- @include('partials.form-tab-headers') --}}
                 <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1-1" data-toggle="tab">{{ trans('vendor::vendors.tabs.profile') }}</a></li>
                    <li class=""><a href="{{ route('admin.vendor.location.index', $vendor->id) }}">{{ trans('vendor::vendors.tabs.location') }}</a></li>
                    {{-- <li class=""><a href="#" data-toggle="tab">{{ trans('vendor::vendors.tabs.portfolios') }}</a></li>
                    <li class=""><a href="#tab_3-3" data-toggle="tab">{{ trans('vendor::vendors.tabs.infomation') }}</a></li> --}}
                </ul>
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('vendor::admin.vendors.partials.edit-fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        {!! Form::hidden('previousUrl', $previousUrl) !!}
                        {!! Form::hidden('vendor_id', $vendor->id, ['id' => 'vendor_id']) !!}
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
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
            $("#state").trigger("change");  
            $(".js-example-basic-single").select2();
            // if(!$("#state").val()){
            //     $("#country").trigger("change" );
            // }
            // $("#category").trigger("change");                        
        });
        
        $(document).on("change", "#city",function(e){
            e.preventDefault();
            var vendor_id = $("#vendor_id").val();
            var city_id = $("#city").val();

            fetch_location_detail(city_id, vendor_id);
        });
        
        $(document).on("change", "#country",function(e){
            e.preventDefault();
            var vendor_id = $("#vendor_id").val();
            var country_id = $("#country").val();

            fetch_city(country_id, vendor_id);
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

        function fetch_city(country_id, vendor_id) 
        {
            $.ajax({
                 type: 'post',
                 url: '{{ route('api.vendor.getCity') }}',
                 data: {
                   'country_id':country_id,
                   'vendor_id':vendor_id,
                   '_token': '{{ csrf_token() }}',
                 },
                 dataType: 'json',
                 success: function (response) {
                    var cities = phonecodes = html = "";
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
                    for (x in data.phonecodes) {
                          if(x == data.phonecode){
                              html = '<option value="'+ x +'" selected>'+ data.phonecodes[x] +'</option>';
                          }else{
                              html = '<option value="'+ x +'">'+ data.phonecodes[x]+'</option>';
                          }
                          phonecodes += html;
                    }
                    // $("#vendor-business-phone").val(data.business_phone);
                    $("#vendor-zipcode").val(data.zip_code);
                    // $("#vendor-phonecode").html(phonecodes);
                    $("#city").html(cities);
                        
                    var vendor_id = $("#vendor_id").val();
                    fetch_location_detail(city_id, vendor_id);
                 }
            });
        }

        function fetch_location_detail(city_id, vendor_id) 
        {
            $.ajax({
                 type: 'post',
                 url: '{{ route('api.vendor.getVendorLocation') }}',
                 data: {
                   'city_id':city_id,
                   'vendor_id':vendor_id,
                   '_token': '{{ csrf_token() }}',
                 },
                 dataType: 'json',
                 success: function (response) {
                    var x;
                    var i = 0;
                    var data = response.data;
                    var phone_number ="";
                    var item = "";
                    var phonecodes = "";

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
                    if(data.business_phone.length){
                        for( i = 0; i < data.business_phone.length; i++ ){
                            item = data.business_phone[i];
                            if(i == 0){
                                phone_number += '<div class="row">' + 
                                                    '<div class="col-sm-2">' + 
                                                        '<div class="form-group">' + 
                                                            '<label for="business_code">Business Phone Code</label>' +
                                                            '<select class="form-control" id="vendor-phonecode" name="business_code">' +
                                                                phonecodes +
                                                            '</select>' +
                                                        '</div>' + 
                                                    '</div>' + 
                                                    '<div class="col-sm-8">' + 
                                                        '<div class="form-group">' +
                                                            '<label for="business_phone">Business Phone Number</label>' +
                                                            '<input class="form-control" placeholder="Business Phone Number" id="vendor-business-phone" name="business_phone[]" type="number" value="' + item.phone_number + '">' +
                                                        '</div>' + 
                                                    '</div>' + 
                                                    '<div class="col-sm-2">' + 
                                                        '<div class="form-group" style="padding-top: 30px;">' + 
                                                            '<a href="#" id="link-add-phone_number" class="link-add-phone_number">Add new number</a>' +
                                                        '</div>' + 
                                                    '</div>' + 
                                                '</div>';
                            }else{
                                phone_number += '<div class="row new-phone-number">' +
                                                    '<div class="col-sm-2">' +
                                                    '</div>' +
                                                    '<div class="col-sm-8">' +
                                                        '<div class="form-group">' +
                                                            '<input class="form-control" placeholder="Business Phone Number" id="vendor-business-phone" name="business_phone[]" type="number" value="' + item.phone_number + '">' +
                                                        '</div>' +
                                                    '</div>' +
                                                    '<div class="col-sm-2">' +
                                                        '<div class="form-group" style="padding-top: 5px;">' +
                                                            '<a href="javascript:avoid(0)" id="link-remove-phone_number" class="link-remove-phone_number">Remove</a>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>';
                            }
                        }
                        phone_number += '<div id="append-phone_number"></div>';
                    }else{
                        phone_number += '<div class="row">' + 
                                            '<div class="col-sm-2">' + 
                                                '<div class="form-group">' + 
                                                    '<label for="business_code">Business Phone Code</label>' +
                                                    '<select class="form-control" id="vendor-phonecode" name="business_code">' +
                                                        phonecodes +
                                                    '</select>' +
                                                '</div>' + 
                                            '</div>' + 
                                            '<div class="col-sm-8">' + 
                                                '<div class="form-group">' +
                                                    '<label for="business_phone">Business Phone Number</label>' +
                                                    '<input class="form-control" placeholder="Business Phone Number" id="vendor-business-phone" name="business_phone[]" type="number" value="">' +
                                                '</div>' + 
                                            '</div>' + 
                                            '<div class="col-sm-2">' + 
                                                '<div class="form-group" style="padding-top: 30px;">' + 
                                                    '<a href="#" id="link-add-phone_number" class="link-add-phone_number">Add new number</a>' +
                                                '</div>' + 
                                            '</div>' + 
                                        '</div>';
                        phone_number += '<div id="append-phone_number"></div>';
                    }

                    // $("#vendor-business-phone").val(data.business_phone);
                    $("#phone-number-container").html(phone_number);
                    $("#vendor-zipcode").val(data.zip_code);
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
