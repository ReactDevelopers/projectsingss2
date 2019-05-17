<div class="login-inner-wrapper">
    <form class="form-horizontal" role="talent_step_three" action="{{url(sprintf('%s/profile/step/process/three',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8">
        {{ csrf_field() }}
        @if(!empty($edit))<input type="hidden" name="process" value="edit">@endif
        <div class="form-group">
            <h4 class="form-sub-heading">{{ trans('website.W0207') }}</h4>
            <div class="custom-dropdown">
                <select name="subindustry[]" style="max-width: 400px;"  class="form-control" data-request="tags-true" multiple="true" data-placeholder="{{ trans('website.W0799') }}">
                    {!!___dropdown_options(array_combine(array_column($subindustries_name, 'name'), array_column($subindustries_name, 'name')),trans('website.W0799'),array_column($user['subindustry'],'name'),false)!!}
                </select>
                <div class="js-example-tags-container white-tags"></div>
            </div>
        </div>
        <input type="hidden" name="industry_id" value="@if(!empty($user['industry'])){{ current(array_column($user['industry'],'id_industry')) }}@else{{6}}@endif" />
    </form>        
</div>    
<div class="login-inner-wrapper">
    <div class="">
        <form class="form-horizontal" role="doc-submit" action="{{url(sprintf('%s/doc-submit',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8">
            <h4 class="form-sub-heading">
                {{trans('website.W0663')}}
                <label class="upload-label pull-right" for="certificate">{{trans('website.W0113')}}</label>
            </h4>
            <div class="attachment-group row clearfix">                               
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="upload-box">
                        @php
                            if(!empty($get_files)){
                                foreach ($get_files as $key => $value) {
                                    $url_delete = sprintf(
                                        url('ajax/%s?id_file=%s'),
                                        DELETE_DOCUMENT,
                                        $value['id_file']
                                    );
                                    echo sprintf(RESUME_TEMPLATE,
                                        $value['id_file'],
                                        url(sprintf('/download/file?file_id=%s',___encrypt($value['id_file']))),
                                        asset('/'),
                                        substr($value['filename'],0,3),
                                        $value['filename'],
                                        $url_delete,
                                        $value['id_file'],
                                        asset('/')
                                    );  
                                }
                            }
                        @endphp
                    </div>
                    <div class="fileUpload upload-docx"><input id="certificate" type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]'/></div>
                    <span class="upload-hint">{{trans('website.W0114')}}</span>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="form-group button-group">
    <div class="row form-btn-set">
        <div class="col-md-4 col-sm-4 col-xs-12">
            @if(in_array('two',$steps))
                <a href="{{ url(sprintf('%s/profile/%sstep/%s',TALENT_ROLE_TYPE,$edit_url,$steps[count($steps)-2])) }}" class="greybutton-line">{{trans('website.W0196')}}</a>
            @endif
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <a href="{{ $skip_url }}" class="greybutton-line">
                {{trans('website.W0186')}}
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <button type="button" class="button" data-request="ajax-submit" data-target='[role="talent_step_three"]' value="Save">{{trans('website.W0659')}}</button>
        </div>
    </div>
</div>