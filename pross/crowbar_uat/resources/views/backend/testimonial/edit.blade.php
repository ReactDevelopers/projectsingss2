@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-industry" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'testimonial/edit'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="question">Name</label>
                                <input type="text" class="form-control" name="name" value="{{$testimonial->name}}" placeholder="Enter name" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">Profession</label>
                                <input type="text" class="form-control" name="profession" value="{{$testimonial->profession}}" placeholder="Enter Profession" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">Description</label>
                                <input type="text" class="form-control" name="description" value="{{$testimonial->description}}" placeholder="Enter description" style="width:100%;"/>
                            </div>
                            @if(!empty($testimonial) && !empty($testimonial->image))
                                <div class="form-group">
                                    <img src="{{asset(str_replace('testimonial/', 'testimonial/resize/',$testimonial->image))}}">
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="id" value="{{$testimonial->id}}">
                        <input type="hidden" name="action" value="submit">
                        <input type="hidden" name="industry_image" value="" >
                        <button class="hide" id="industry-form" type="button" data-request="ajax-submit" data-target='[role="form-add-industry"]' name="submit" class="button" value="Submit">
                            {{trans('job.J0029')}}
                        </button>                                            
                    </form>
                    <form class="form-horizontal" action="{{ url(sprintf('%s/testimonial/image',ADMIN_FOLDER)) }}" role="doc-submit" method="post" accept-charset="utf-8">
                        <div class="custom-image-upload clearfix">
                            <div class="col-md-7 top-margin-20px">
                                <div class="upload-box row">
                                    <!-- PLACE FOR DYNAMICALLY MULTIPLE ADDED IMAGE  -->
                                    <div class="col-md-6 bottom-margin-10px single-remove">
                                        <label class="btn-bs-file add-image-box">
                                            <span class="add-image-wrapper">
                                                <img src="{{ asset('images/add-icon.png') }}" />
                                                <span class="add-icon-title">{{ trans('website.W0325') }}</span>
                                                <input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-place="prepend"  data-single="true"/>
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
                                            data-target="#industry-form" 
                                            data-copy-source='[name="documents[]"]' 
                                            data-copy-destination='[name="industry_image"]' 
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



