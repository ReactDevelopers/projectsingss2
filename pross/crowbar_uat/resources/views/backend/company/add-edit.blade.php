@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-company" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'company/add'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="question">Company</label>
                                <input type="text" class="form-control" name="company_name" value="{{ !empty($company) ? $company->company_name : '' }}" placeholder="{{ trans('admin.A0066') }}" style="width:100%;"/>
                            </div>
                            @if(!empty($company) && !empty($company->image))
                                <div class="form-group">
                                    <img src="{{asset(str_replace('company/', 'company/resize/',$company->image))}}">
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="id_company" value="{{ !empty($company) ? ___encrypt($company->id_company) : ''}}">
                        <input type="hidden" name="company_image" value="" >
                        <button type="button" class="hide" id="company-form"  data-request="ajax-submit" data-target='[role="form-add-company"]' class="btn btn-default"></button>                                            
                    </form>
                    <form class="form-horizontal" action="{{ url(sprintf('%s/company/image',ADMIN_FOLDER)) }}" role="doc-submit" method="post" accept-charset="utf-8">
                        <div class="custom-image-upload clearfix">
                            <div class="col-md-7 top-margin-20px">
                                <div class="upload-box row">
                                    <!-- PLACE FOR DYNAMICALLY MULTIPLE ADDED IMAGE  -->
                                    <div class="col-md-6 bottom-margin-10px single-remove">
                                        <label class="btn-bs-file add-image-box">
                                            <span class="add-image-wrapper">
                                                <img src="{{ asset('images/add-icon.png') }}" />
                                                <span class="add-icon-title">{{ trans('website.W0325') }}</span>
                                                <input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" accept="image/*" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-place="prepend"  data-single="true"/>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-footer">
                        <div class="row form-group button-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row form-btn-set">
                                    <div class="col-md-12 col-sm-5 col-xs-6">
                                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                                        <button 
                                            type="button" 
                                            data-request="trigger-proposal" 
                                            data-target="#company-form" 
                                            data-copy-source='[name="documents[]"]' 
                                            data-copy-destination='[name="company_image"]' 
                                            value="Submit" 
                                            class="btn btn-default">
                                            Save
                                        </button>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </section>
@endsection
{{-- @push('inlinescript')
    <script type="text/javascript">
        @if(!empty($company->parent_industry))
            $(window).load(function(){
                $('[name="industry"]').trigger('change');
            });
        @endif
    </script>
@endpush --}}