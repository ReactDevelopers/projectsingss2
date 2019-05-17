@include('talent.viewprofile.includes.sidebar-tabs',$user)                    
<div class="login-inner-wrapper edit-inner-wrapper">
    <div class="inner-profile-section addNewProjects">
        <form class="form-horizontal" action="{{ url(sprintf('%s/profile/portfolio/__add',TALENT_ROLE_TYPE)) }}" role="submit_porfolio" method="post" accept-charset="utf-8">
            <div class="form-group">
                <label class="col-md-7 control-label">{{ trans('website.W0329') }}</label>
                <div class="col-md-7">
                    <input type="text" name="portfolio" placeholder="{{ trans('website.W0330') }}" value="{{ $portfolio['portfolio']}}" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-7 control-label">{{ trans('website.W0331') }}</label>
                <div class="col-md-7">
                    <textarea type="text" name="description" placeholder="{{ trans('website.W0332') }}" class="form-control" />{{ $portfolio['description'] }}</textarea>
                </div>
            </div>
            <div class="form-group top-margin-20px">
                <div class="col-md-7">
                    <input type="hidden" name="portfolio_id" value="{{ $portfolio['id_portfolio'] }}">
                    <input type="hidden" name="portfolio_docs" id="portfolio_docs">
                    <input type="hidden" name="removed_portfolio" >
                    <button class="hide" id="portflio-form" type="button" data-request="ajax-submit" data-target='[role="submit_porfolio"]' name="submit" class="button" value="Submit">
                        {{trans('job.J0029')}}
                    </button> 
                </div>                               
            </div>                               
        </form>

        <form class="form-horizontal" role="doc-submit" action="{{url(sprintf('%s/port-doc-submit',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8">
            <label class="control-label">Document</label>
                @php
                    $style = "";
                    if(!empty($portfolio['file'])){
                        $style = "display:none";
                    }elseif(count($portfolio['file']==0)){
                        $style = "";
                    }
                @endphp
                <label class="upload-label pull-right" for="portfolio" id="for_portfolio" style="{{$style}}">{{trans('website.W0113')}}</label>
            <div class="attachment-group row clearfix">                               
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="upload-box">
                        @foreach($portfolio['file'] as $item)
                            @php
                                $url_delete = sprintf(
                                    url('ajax/%s?id_file=%s'),
                                    DELETE_DOCUMENT,
                                    $item['id_file']
                                );
                            @endphp
                            {!!
                                sprintf(NEW_PORTFOLIO_TEMPLATE,
                                        $item['id_file'],
                                        url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                                        asset('/'),
                                        $item['extension'],
                                        $item['filename'],
                                        $url_delete,
                                        $item['id_file'],
                                        asset('/'),
                                        $item['id_file']
                                );
                            !!}    
                        @endforeach
                    </div>
                    <div class="fileUpload upload-docx">
                        <input id="portfolio" type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-single="true"/>
                    </div>
                    <span class="upload-hint">Attachments can be PDF's, TXT's, DOC's, XLS's OR JPG's, GIF's, PNG's.</span>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row form-group button-group">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row form-btn-set">
            <div class="col-md-7 col-sm-7 col-xs-6">
                <a href="{{ url()->previous() }}" class="greybutton-line" value="Cancel">{{trans('job.J0028')}}</a>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-6">
                <button id="doc-button" type="button" data-request="trigger-proposal" data-target="#portflio-form" data-copy-source='[name="documents"]' data-copy-destination='[name="portfolio_docs"]' class="button" value="Submit">
                    {{trans('website.W0058')}}
                </button>
            </div>
        </div>
    </div>
</div>

@push('inlinescript')
    <script>
        $(document).on('click','[data-request="remove-local-document"]', function(){
            $('.single-remove').show();
        });

        $(document).ready(function(){

            var value = $('input[name="document"]').val();
            console.log("this is value------ "+value);

        });
    </script>
@endpush