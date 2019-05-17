@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            @foreach($banners as $item)
                <div class="col-xs-4">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="user-block">
                                <img class="img-circle" src="{{DEFAULT_AVATAR_IMAGE}}">
                                <span class="username"><a>{{$item->banner_title}}</a></span>
                                <span class="description">Last updated - {{___d($item->updated)}}</span>
                            </div>
                            <div class="box-tools">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="banner-box upload-box-{{$item->id_banner}}">
                                @if(!empty($item->banner_image))
                                    <img src="{{asset("uploads/banner/thumbnail/{$item->banner_image}")}}">
                                @endif
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="row">
                                <div class="col-xs-2">
                                    <form action="{{ url(sprintf('%s/banner/image',ADMIN_FOLDER)) }}" role="banner-submit-{{$item->id_banner}}" method="post" accept-charset="utf-8">
                                        {{ csrf_field() }}
                                        <label class="btn-bs-file add-image-box">
                                            <span class="add-image-wrapper" style="padding: 3px;display: block;">
                                                <img src="{{ asset('images/add-icon.png') }}"/>
                                                <input type="file" name="file" class="hide" data-request="banner-submit" data-toadd =".upload-box-{{$item->id_banner}}" data-after-upload=".single-remove" data-target='[role="banner-submit-{{$item->id_banner}}"]' data-place="prepend" data-single="true" data-field="#image_name_{{$item->id_banner}}"/>
                                                <input type="hidden" name="_method" value="PUT">
                                            </span>
                                        </label>
                                    </form>
                                </div>
                                <div class="col-xs-7">
                                    <form role="add-talent-{{$item->id_banner}}" id="main-form" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/banner/edit/%s',ADMIN_FOLDER,$item->id_banner)) }}">
                                        <input type="hidden" id="admin-banner-delete" value="{{ url(sprintf('%s/banner/image/delete',ADMIN_FOLDER)) }}">
                                        <input type="hidden" name="id_banner" value="{{$item->id_banner}}">
                                        <input type="hidden" id="image_name_{{$item->id_banner}}" name="image_name">
                                        <input type="hidden" name="_method" value="PUT">
                                        {{ csrf_field() }}
                                        <textarea class="form-control" type="text" name="banner_text" rows="1">{{$item->banner_text}}</textarea>
                                    </form>
                                </div>
                                <div class="col-xs-3">
                                    <button type="button" class="btn btn-default pull-right" data-request="ajax-submit" data-target='[role="add-talent-{{$item->id_banner}}"]'><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('inlinescript')
<script src="{{ asset('js/cropper.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

$(document).on('change','[data-request="banner-submit"]', function(){
    $('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
    var $this = $(this);
    var $target         = $this.data('target');
    var $field          = $this.data('field');
    var $url            = $($target).attr('action');
    var $method         = $($target).attr('method');
    var $data           = new FormData($($target)[0]);
    var after_upload    = $this.data('after-upload');

    $.ajax({
        url  : $url,
        data : $data,
        cache : false,
        type : $method,
        dataType : 'json',
        contentType : false,
        processData : false,
        success : function($response){
            if($response.status==true){
                $($this.data('toadd')).html($response.data.img_html);
                $($field).val($response.data.image);
            }else{
                if ($response.data) {
                    show_validation_error($response.data);
                }
            }
            $this.val('');
            $('#popup').hide();
        }
    });
});
$(document).on('click','[data-request="remove-local-document"]', function(){
    var $data = new FormData($('#main-form')[0]);

    $.ajax({
        url  : $('#admin-banner-delete').val(),
        data : $data,
        cache : false,
        type : 'post',
        dataType : 'json',
        contentType : false,
        processData : false,
        success : function($response){
            $('#admin-banner-template').remove();
            $('').val('');
            $('.single-remove').show();
        }
    });
});
</script>
@endpush
