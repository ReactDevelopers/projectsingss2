<div>
    <ul class="user-profile-links">
        <li class="resp-tab-item">
            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0678')}}
            </a>
        </li>
        <li class="resp-tab-item">
            <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0679')}}
            </a>
        </li>
        <li>
            <a href="{{url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0680')}}
            </a>
        </li>
        @if(!empty($project->reviews_count))
            <li class="resp-tab-item">
                <a href="{{url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0721')}}
                </a>
            </li>
        @endif
        <li class="active">
            <a href="{{url('talent/project/dispute/details?job_id='.___encrypt($project->id_project))}}">
                {{trans('website.W0409')}}
            </a>
        </li>
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="login-inner-wrapper" style="padding: 20px;">
            @if(!empty($project->dispute))
                <div class="white-wrapper m-b-n no-padding-bottom">
                    <p class="heading"><b>{{trans('website.W0828')}}</b>: {{$project->dispute->concern->reason}}</p>
                </div>
            @endif
            @if(!empty($project->dispute) && $project->dispute->comments)
                @foreach($project->dispute->comments as $item)
                    <div class="white-wrapper m-b-15">
                        <div class="raise-dispute-box">
                            <b>{{$item->sender->name}}</b>
                            <div class="raise-dispute-message">{!!nl2br($item->comment)!!}</div>
                            <div class="raise-dispute-time">
                                {{___d($item->created)}}
                            </div>
                            <div class="raise-dispute-files">
                                @foreach($item->files as $file)
                                    @includeIf('employer.jobdetail.includes.attachment',['file' => $file, 'delete' => true])
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            @if(empty($project->dispute) || ($project->dispute->can_reply == DEFAULT_YES_VALUE && !empty($project->dispute->time_left)))
                <form role="submit-raise-dispute" action="{{ url(sprintf('%s/project/submit/dispute',TALENT_ROLE_TYPE)) }}" method="POST" class="form-horizontal">
                    {{ csrf_field() }}
                    @if(0)
                        <p class="heading-grey">
                            @if(empty($project->dispute))
                                {!!sprintf(trans('website.W0410'),$project->employer->name)!!}       
                            @else
                                @if($project->dispute->last_commented_by != auth()->user()->id_user)
                                    {!!sprintf(trans('website.receiver-'.$project->dispute->type),$project->dispute->sender->name)!!}      
                                @else
                                    {!!sprintf(trans('website.'.$project->dispute->type),$project->dispute->sender->name)!!}   
                                @endif
                            @endif 
                        </p>
                    @endif 
                    <div class="review-form">
                        @if(empty($project->dispute))
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="raise-dispute-select">
                                        <select class="form-control" name="reason">
                                            {!! ___dropdown_options(___cache('dispute_concern'),trans('website.W0067'),'-1') !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <textarea name="comment" class="form-control" data-request="live-length" placeholder="{{ trans('website.W0412') }}" data-maxlength="{{DESCRIPTION_COUNTER_LENGTH}}"></textarea>
                                <input type="hidden" name="project_id" value="{{$project->id_project}}" />
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="dispute_documents">
                </form>
                <form class="form-horizontal" role="raisedispute" action="{{url(sprintf('%s/document/raisedispute',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8">
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
                            <div class="fileUpload upload-docx"><span>{{trans('website.W0113')}}</span><input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="raisedispute"]'/></div>
                            <span class="upload-hint"></span>
                        </div>
                    </div>
                </form>
            @endif
            {{-- <p class="heading-grey">{!!sprintf(trans('website.W0724'),url('page/terms-and-conditions'))!!}</p> --}}
        </div>

        @if(empty($project->dispute) || ($project->dispute->can_reply == DEFAULT_YES_VALUE && !empty($project->dispute->time_left)))  
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <a href="{{ url(sprintf('%s/project/details?job_id=%s',TALENT_ROLE_TYPE, ___encrypt($project['id_project']))) }}" class="button-line">{{ trans('job.J0084')}}</a>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button type="button" data-request="trigger-proposal" data-target="[data-request='ajax-submit']" data-copy-source='[name="documents[]"]' data-copy-destination='[name="dispute_documents"]' class="button" value="{{trans('website.W0013')}}">
                                {{trans('website.W0013')}}
                            </button>
                            <button type="button" class="hide" data-request="ajax-submit" data-target="[role='submit-raise-dispute']" value="Submit">{{ trans('website.W0013') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>