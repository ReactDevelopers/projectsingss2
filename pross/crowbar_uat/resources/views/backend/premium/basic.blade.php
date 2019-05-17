@extends('layouts.backend.dashboard')

@section('requirecss')
    <link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/crop.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body box-profile">
                        <div class="image-circle">
                            <div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="190" data-height="190" data-folder="{{TALENT_PROFILE_PHOTO_UPLOAD}}" data-record="0" data-column="profile" style="background: url('{{ $picture }}') no-repeat center center;background-size:190px 190px;"></div>
                        </div>
                        <h3 class="profile-username text-center">{{$user['name']}}</h3>
                        <p class="text-muted text-center">{{ucfirst($user['type'])}}</p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Registered On</b> <a class="pull-right">{{___d(date('Y-m-d', strtotime($user['created'])))}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url('administrator/users/employer/update?user_id='.$encrypt_user_id) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group @if ($errors->has('first_name'))has-error @endif">
                                <label for="name">First Name</label>
                                <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ (old('first_name'))?old('first_name'):$user['first_name'] }}">
                                @if ($errors->first('first_name'))
                                    <span class="help-block">
                                        {{ $errors->first('first_name')}}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group @if ($errors->has('last_name'))has-error @endif">
                                <label for="name">Last Name</label>
                                <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ (old('last_name'))?old('last_name'):$user['last_name'] }}">
                                @if ($errors->first('last_name'))
                                    <span class="help-block">
                                        {{ $errors->first('last_name')}}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group @if ($errors->has('email'))has-error @endif">
                                <label for="name">Email</label>
                                <input readonly="readonly" type="text" class="form-control" name="email" placeholder="Email" value="{{ (old('email'))?old('email'):$user['email'] }}">
                                @if ($errors->first('email'))
                                    <span class="help-block">
                                        {{ $errors->first('email')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('country'))has-error @endif">
                                <label for="name">Country</label>
                                @php
                                if(old('country')){
                                    $country = old('country');
                                }
                                else{
                                    $country = $user['country'];
                                }
                                @endphp
                                <select class="form-control" name="country" id="country" data-url="{{ url('ajax/state-list') }}" placeholder="Country">
                                    @foreach($countries as $c)
                                        <option {{$country==$c->id_country?' selected="selected"':''}} value="{{$c->id_country}}">{{$c->country_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first('country'))
                                    <span class="help-block">
                                        {{ $errors->first('country')}}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group @if ($errors->has('state'))has-error @endif">
                                <label for="name">State</label>
                                @php
                                if(old('state')){
                                    $state = old('state');
                                }
                                else{
                                    $state = $user['state'];
                                }
                                @endphp
                                <input type="hidden" id="on_state" value="{{$state}}" />
                                <select class="form-control" name="state" id="state" placeholder="State" data-url="{{ url('ajax/city-list') }}">
                                    <option value="">Select State/ Province</option>
                                    @foreach($states as $c)
                                        <option {{$state==$c->id_state?' selected="selected"':''}} value="{{$c->id_state}}">{{$c->state_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first('state'))
                                    <span class="help-block">
                                        {{ $errors->first('state')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('city'))has-error @endif">
                                <label for="name">City</label>
                                @php
                                if(old('city')){
                                    $city = old('city');
                                }
                                else{
                                    $city = $user['city'];
                                }
                                @endphp
                                <input type="hidden" id="on_city" value="{{$city}}" />
                                <select class="form-control" name="city" id="city" placeholder="State">
                                    <option value="">Select City</option>
                                    @foreach($cities as $c)
                                        <option {{$city==$c->id_city?' selected="selected"':''}} value="{{$c->id_city}}">{{$c->city_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first('city'))
                                    <span class="help-block">
                                        {{ $errors->first('city')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('postal_code'))has-error @endif">
                                <label for="name">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" placeholder="Postal Code" value="{{ (old('postal_code'))?old('postal_code'):$user['postal_code'] }}">
                                @if ($errors->first('postal_code'))
                                    <span class="help-block">
                                        {{ $errors->first('postal_code')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('industry'))has-error @endif">
                                <label for="name">Industry</label>
                                @php
                                if(old('industry')){
                                    $industry = old('industry');
                                }
                                else{
                                    $industry = $user['industry'];
                                }
                                @endphp
                                <select name="industry" class="form-control" data-request="option" data-url="{{ url('ajax/industry-subindustry-list') }}">
                                    {!!___dropdown_options($industries_name,sprintf(trans('website.W0059'),trans('website.W0068')),$industry,false)!!}
                                </select>
                                @if ($errors->first('industry'))
                                    <span class="help-block">
                                        {{ $errors->first('industry')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('subindustry'))has-error @endif">
                                <label for="name">Sub Industry</label>
                                @php
                                if(old('subindustry')){
                                    $subindustry = old('subindustry');
                                }
                                else{
                                    $subindustry = $user['subindustry'];
                                }
                                @endphp
                                <div class="custom-dropdown">
                                    <select name="subindustry" class="form-control" data-request="option" data-url="{{ url('ajax/subindustry-skills-list') }}">
                                        {!!___dropdown_options($subindustries_name,sprintf(trans('website.W0060'),trans('website.W0068')),$subindustry,false)!!}
                                    </select>
                                </div>
                                @if ($errors->first('subindustry'))
                                    <span class="help-block">
                                        {{ $errors->first('subindustry')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('skill'))has-error @endif">
                                <label for="name">Skill</label>
                                <select id="indus-skill" data-request="tags" multiple="" class="form-control" name="skill[]" placeholder="Skill">
                                    @foreach($all_skill as $s)
                                    <option value="{{$s['skill_name']}}"{{in_array($s['skill_name'], $user_skill) ? ' selected="selected"' : ''}}>{{$s['skill_name']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first('skill'))
                                    <span class="help-block">
                                        {{ $errors->first('skill')}}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group @if ($errors->has('experience'))has-error @endif">
                                <label for="name">Expertise Level</label>
                                @php
                                if(old('expertise')){
                                    $expertise = old('expertise');
                                }
                                else{
                                    $expertise = $user['expertise'];
                                }
                                @endphp

                                <div class="radio radio-inline">
                                    <input type="radio" id="expert-novice" class="" name="expertise"
                                placeholder="Expertise" value="novice"{{$expertise == 'novice' ? ' checked="checked"' : ''}}>
                                    <label for="expert-novice">Novice</label>
                                </div>

                                <div class="radio radio-inline">
                                    <input type="radio" id="expert-proficient" class="" name="expertise"
                                placeholder="Expertise" value="proficient"{{$expertise == 'proficient' ? ' checked="checked"' : ''}}>
                                    <label for="expert-proficient">Proficient</label>
                                </div>

                                <div class="radio radio-inline">
                                    <input type="radio" id="expert-expert" class="" name="expertise"
                                placeholder="Expertise" value="expert"{{$expertise == 'expert' ? ' checked="checked"' : ''}}>
                                    <label for="expert-expert">Expert</label>
                                </div>

                                @if ($errors->first('expertise'))
                                    <span class="help-block">
                                        {{ $errors->first('expertise')}}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group @if ($errors->has('experience'))has-error @endif">
                                <label for="name">No. of Years(in Years)</label>
                                <input type="text" class="form-control" name="experience" placeholder="Experience" value="{{ (old('experience'))?old('experience'):$user['experience'] }}">
                                @if ($errors->first('experience'))
                                    <span class="help-block">
                                        {{ $errors->first('experience')}}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>

                    <form class="form-horizontal" role="doc-submit" action="{{url('administrator/premium/doc-submit?id_user='.$id_user)}}" method="POST" accept-charset="utf-8">
                        <div class="panel-body">
                            <label for="name">Attach Documents</label>

                            <div class="upload-box">
                                @foreach($certificate_attachments as $att)
                                    @php
                                    $id_file = ___encrypt($att['id_file']);
                                    @endphp
                                    <div class="single-upload-wrapper">
                                        <div class="uploaded-docx clearfix" id="files-{{$att['id_file']}}">
                                            <a href="{{asset('download/file?file_id='.$id_file)}}" class="download-docx">
                                                <img src="{{asset('images/attachment-icon.png')}}">
                                                <div class="upload-info">
                                                    <p>{{$att['filename']}}</p>
                                                    <span>{{$att['size']}}</span>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0);" data-id-user="{{$id_user}}" data-url="{{url('ajax/delete-user-document')}}" title="Delete" data-file-id="{{$att['id_file']}}" class="delete-docx">
                                                <img src="{{asset('images/close-icon-md.png')}}">
                                            </a>
                                        </div>
                                        
                                    </div>
                                @endforeach
                            </div>

                            <div class="upload-image-wrapper">
                                <div class="upload-docx">
                                    <label for="custom-image">{{trans('website.W0113')}}</label>
                                    <input type="file" id="custom-image" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-target='[role="doc-submit"]'/>
                                </div>
                                
                            </div>
                            <span class="upload-hint">{{trans('website.W0114')}}</span>

                        </div>
                    </form>

                    <div class="panel-footer">
                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                        <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('inlinecss')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
@push('inlinescript')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/cropper.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    var on_country = $('#country').val();
    var on_state = $('#on_state').val();
    var on_city = $('#on_city').val();
    if(on_country > 0){
        var url = $('#country').data('url');
        if(on_country > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: on_country}
            })
            .done(function(data) {
                $('#state').html(data);
                $('#state').val(on_state);
                $('#city').html('<option value="">Select City</option>');

                if(on_city > 0){
                    var url = $('#state').data('url');
                    $.ajax({
                    method: "POST",
                    url: url,
                    data: { record_id: on_state}
                    })
                    .done(function(data) {
                        $('#city').html(data);
                        $('#city').val(on_city);
                    });
                }
            });
        }
    }

    var on_industry = $('#on_industry').val();
    if(on_industry > 0){
        var url = $('#industry').data('url');
        if(on_industry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: on_industry}
            })
            .done(function(data) {
                $('#subindustry').html(data);
            });
        }
    }

    var on_subindustry = $('#subindustry').val();
    if(on_subindustry > 0){
        var url = $('#subindustry').data('url');
        if(on_subindustry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: on_subindustry}
            })
            .done(function(data) {
                $('#indus-skill').html(data);
            });
        }
    }

    $(".cropper").SGCropper({
        viewMode: 1,
        aspectRatio: "2/3",
        cropBoxResizable: false,
        formContainer:{
            actionURL:"{{ url(sprintf('ajax/crop?imagename=image&user_id=%s',$user['id_user'])) }}",
            modelTitle:"{{ trans('website.W0261') }}",
            modelSuggestion:"{{ trans('website.W0263') }}",
            modelDescription:"{{ trans('website.W0264') }}",
            modelSeperator:"{{ trans('website.W0265') }}",
            uploadLabel:"{{ trans('website.W0266') }}",
            fieldLabel:"",
            fieldName: "image",
            btnText:"{{ trans('website.W0262') }}",
            defaultImage: base_url+"/images/product_sample.jpg",
            loaderImage: base_url+"/images/loader.gif",
        }
    });
    $('#country').change(function(){
        var id_country = $('#country').val();
        var url = $('#country').data('url');
        if(id_country > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: id_country}
            })
            .done(function(data) {
                $('#state').html(data);
                $('#city').html('<option value="">Select City</option>');
            });
        }
    });
    $('#state').change(function(){
        var id_state = $('#state').val();
        var url = $('#state').data('url');

        if(id_state > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: id_state}
            })
            .done(function(data) {
                $('#city').html(data);
            });
        }
    });

    $('#industry').change(function(){
        var industry = $('#industry').val();
        var url = $('#industry').data('url');
        if(industry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: industry}
            })
            .done(function(data) {
                $('#subindustry').html(data);
            });
        }
    });

    $('#subindustry').change(function(){
        var subindustry = $('#subindustry').val();
        var url = $('#subindustry').data('url');
        if(subindustry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: subindustry}
            })
            .done(function(data) {
                $('#indus-skill').html(data);
            });
        }
    });

    // $(document).on('change','[data-request="doc-submit"]', function(){
    //     $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
    //     var $this = $(this);
    //     var $target         = $this.data('target');
    //     var $url            = $($target).attr('action');
    //     var $method         = $($target).attr('method');
    //     var $data           = new FormData($($target)[0]);
    //     var after_upload    = $this.data('after-upload');
    //     $.ajax({
    //         url  : $url,
    //         data : $data,
    //         cache : false,
    //         type : $method,
    //         dataType : 'json',
    //         contentType : false,
    //         processData : false,
    //         success : function($response){
    //             if($response.status==true){
    //                 if($this.data('place') == 'prepend'){
    //                     $($this.data('toadd')).prepend($response.data);
    //                 }else{
    //                     $($this.data('toadd')).append($response.data);
    //                 }
    //                 if($this.data('single') === true){
    //                     $(after_upload).hide();
    //                 }
    //             }else{
    //                 if ($response.data) {
    //                     /*TO DISPLAY FORM ERROR USING .has-error class*/
    //                     show_validation_error($response.data);
    //                 }
    //             }
    //             $this.val('');
    //             $('#popup').hide();
    //         }
    //     });
    // });

    $('.delete-docx').click(function(){
        var res = confirm('Do you realy want to delete the document?');

        if(res){
            var id_user = $(this).data('id-user');
            var url = $(this).data('url');
            var file_id = $(this).data('file-id');
            $.ajax({
            method: "POST",
            url: url,
            data: {id_file: file_id, id_user: id_user}
            })
            .done(function(data) {
                $('#files-'+file_id).remove();
            });
        }
    });
});
</script>
@endpush
