<div class="row mainContentWrapper">
    @includeIf('talent.review.emp_sidebar')
    <div class="col-md-8 col-sm-8 right-sidebar">
        <div>
            <ul class="user-profile-links">
                <li class="resp-tab-item">
                    <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                        {{trans('website.W0678')}}
                    </a>
                </li>
                <li class="active">
                    <a href="{{url('talent/project/submit/reviews?job_id='.___encrypt($project->id_project))}}">
                        {{trans('website.W0719')}}
                    </a>
                </li>
                <li class="resp-tab-item">
                    <a href="{{url('talent/project/received/reviews?job_id='.___encrypt($project->id_project))}}">
                        Received Reviews
                    </a>
                </li>
            </ul>
            <div class="clearfix"></div>
            <div class="job-detail-final">
                <form role="submit-ratings" action="{{ url(sprintf('%s/project/submit/reviews?job_id=%s',TALENT_ROLE_TYPE, ___encrypt($project->id_project))) }}" method="POST" class="form-horizontal">
                    <div class="messages"></div>
                    <div class="login-inner-wrapper" style="padding: 0 20px;">
                        <div class="review-form">
                            <h4 class="overall-rating-text">{{trans('job.J0093')}}</h4>
                            <div class="ratingStars">
                                <input id="input-1" name="review_average" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                            </div>
                            <ul class="all-ratings">
                                <li>
                                    <h5>{{trans('job.J0092')}}</h5>
                                    <div class="ratingStars">
                                        <input id="input-2" name="category_two" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                    </div>
                                </li>
                                <li>
                                    <h5>{{trans('job.J00128')}}</h5>
                                    <div class="ratingStars">
                                        <input id="input-3" name="category_three" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                    </div>
                                </li>
                                <li>
                                    <h5>{{trans('job.J00129')}}</h5>
                                    <div class="ratingStars">
                                        <input id="input-4" name="category_four" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                    </div>
                                </li>
                                <li>
                                    <h5>{{trans('job.J00130')}}</h5>
                                    <div class="ratingStars">
                                        <input id="input-5" name="category_five" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                    </div>
                                </li>
                                <li>
                                    <h5>{{trans('job.J00131')}}</h5>
                                    <div class="ratingStars">
                                        <input id="input-6" name="category_six" class="rating rating-loading" data-show-clear="false" data-show-caption="true">
                                    </div>
                                </li>
                            </ul>
                            <div class="form-group"><div class="col-md-12"><input type="hidden" name="rating"></div></div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <textarea name="description" class="form-control" placeholder="{{ trans('job.J0083')}}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group button-group">
                        <div class="col-md-6 col-sm-offset-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ url(sprintf('%s/project/details?job_id=%s',TALENT_ROLE_TYPE, ___encrypt($project->id_project))) }}" class="button-line">{{ trans('job.J0084')}}</a>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="button" data-request="ajax-submit" data-target="[role='submit-ratings']" value="Submit">{{ trans('website.W0013') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
        $("#input-1").rating("create", {disabled:true, showClear:false});
        $("#input-2,#input-3,#input-4,#input-5,#input-6").rating().on("rating.change", function(event, value, caption) {
            var input_2 = parseFloat($('#input-2').rating('get')), input_3 = parseFloat($('#input-3').rating('get')), input_4 = parseFloat($('#input-4').rating('get')), input_5 = parseFloat($('#input-5').rating('get')), input_6 = parseFloat($('#input-6').rating('get'));
            
            $("#input-1").rating("update", ((input_2+input_3+input_4+input_5+input_6)/25)*5);
        });
    </script>
@endpush
