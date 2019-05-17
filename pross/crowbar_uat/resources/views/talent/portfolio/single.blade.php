@include('talent.viewprofile.includes.sidebar-tabs',$user)                    
<div class="login-inner-wrapper edit-inner-wrapper">
    <div class="inner-profile-section addNewProjects clearfix">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group portfolio-title">
                    {{ $portfolio['portfolio']}}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="upload-box row">
                <!-- PLACE FOR DYNAMICALLY MULTIPLE ADDED IMAGE  -->
                @foreach($portfolio['file'] as $item)
                    {!!
                    sprintf(NEW_VIEW_PORTFOLIO_TEMPLATE,
                            $item['id_file'],
                            url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                            asset('/'),
                            $item['extension'],
                            $item['filename']
                    );
                    !!}
                @endforeach
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">                                 
            <div class="form-group">
                <label class="control-label">{{ trans('website.W0331') }}</label>
                {{ $portfolio['description'] }}
            </div> 
        </div> 
    </div>
</div>

@push('inlinecss')
    <style>
        .add-image-delete{display:none;}
        .col-md-6.bottom-margin-10px{width: 100%;}
    </style>
@endpush