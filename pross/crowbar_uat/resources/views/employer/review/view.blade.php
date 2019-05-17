@includeIf('employer.jobdetail.includes.talent',['talent' => $project->proposal->talent])
<div class="col-md-8 col-sm-8 right-sidebar">
    <div>
        <ul class="user-profile-links">
            <li class="resp-tab-item">
                <a href="{{url('employer/project/details?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0678')}}
                </a>
            </li>
            @if(!empty($project->proposal))
                <li class="resp-tab-item">
                    <a href="{{url('employer/project/proposals/talent?proposal_id='.___encrypt($project->proposal->id_proposal).'&project_id='.___encrypt($project->id_project))}}">
                        {{trans('website.W0722')}}
                    </a>
                </li>
            @endif
            <li class="active">
                <a href="{{url('employer/project/submit/reviews?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0721')}}
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
        <div class="job-detail-final">
            <div class="login-inner-wrapper" style="padding: 0 20px;">
                <div class="review-form">
                    <h2 class="form-heading">{{trans('job.J0095')}}</h2>                            
                    <div>
                        <h4 class="overall-rating-text">{{trans('job.J0093')}}</h4>
                        <div class="ratingStars">
                            <input id="input-1" name="review_average" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                        </div>
                        <ul class="all-ratings">
                            <li>
                                <h5>{{trans('job.J0088')}}</h5>
                                <div class="ratingStars">
                                    <input id="input-2" name="category_two" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                </div>
                            </li>
                            <li>
                                <h5>{{trans('job.J0089')}}</h5>
                                <div class="ratingStars">
                                    <input id="input-3" name="category_three" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                </div>
                            </li>
                            <li>
                                <h5>{{trans('job.J0090')}}</h5>
                                <div class="ratingStars">
                                    <input id="input-4" name="category_four" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                </div>
                            </li>
                            <li>
                                <h5>{{trans('job.J0091')}}</h5>
                                <div class="ratingStars">
                                    <input id="input-5" name="category_five" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                </div>
                            </li>
                            <li>
                                <h5>{{trans('job.J0092')}}</h5>
                                <div class="ratingStars">
                                    <input id="input-6" name="category_six" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                </div>
                            </li>
                        </ul>
                        <div class="form-group">
                            {{ @$project->reviews->description }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('inlinecss')
    <link rel="stylesheet" type="text/css" href="{{asset('css/star-rating.css')}}" />
@endpush

@push('inlinescript')
    <script src="{{asset('js/star-rating.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        /*CREATING AND DISABLING BY DEFAULT RATING STAR*/
        $("#input-1").rating("create", {disabled:true, showClear:false});
        $("#input-2").rating("create", {disabled:true, showClear:false});
        $("#input-3").rating("create", {disabled:true, showClear:false});
        $("#input-4").rating("create", {disabled:true, showClear:false});
        $("#input-5").rating("create", {disabled:true, showClear:false});
        $("#input-6").rating("create", {disabled:true, showClear:false});
        
        /*UPDATING RATING STAR*/
        $("#input-1").rating("update", {{@$project->reviews->review_average}});
        $("#input-2").rating("update", {{@$project->reviews->category_two}});
        $("#input-3").rating("update", {{@$project->reviews->category_three}});
        $("#input-4").rating("update", {{@$project->reviews->category_four}});
        $("#input-5").rating("update", {{@$project->reviews->category_five}});
        $("#input-6").rating("update", {{@$project->reviews->category_six}});
    </script>
@endpush