<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox raise-dispute-sidebar clearfix">
        <h2 class="heading-sm">{{trans('website.W0821')}}</h2>
        <ul class="raise-dispute-steps">
            <li @if(empty($project->dispute) || (!empty($project->dispute->step) && $project->dispute->step == 1)) class="current" @endif @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step > 1)) class="previous" @endif>
                <span class="step">1</span>
                <span class="step-detail">{{trans('website.W0822')}}</span>
            </li>
            <li @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step == 2)) class="current" @endif @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step > 2)) class="previous" @endif>
                <span class="step">2</span>
                <span class="step-detail">{{trans('website.W0824')}}</span>
                @if((!empty($project->dispute->step) && $project->dispute->step == 2)   && !empty($project->dispute->time_left) && $project->dispute->can_reply == DEFAULT_YES_VALUE)
                    <span class="step-timer">
                        <img class="pull-left" src="{{asset('/images/watch.png')}}" />
                        <span class="pull-right timer-box">
                            <span class="time" id="time-remaining">{{trans('website.W0842')}}</span>
                            <span class="timer" id="timer">
                                {{___hours($project->dispute->time_left)}}
                            </span>
                        </span>
                    </span>
                @endif
            </li>
            <li @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step == 3)) class="current" @endif @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step > 3)) class="previous" @endif>
                <span class="step">3</span>
                <span class="step-detail">{{trans('website.W0825')}}</span>
                @if((!empty($project->dispute->step) && $project->dispute->step == 3)   && !empty($project->dispute->time_left) && $project->dispute->can_reply == DEFAULT_YES_VALUE)
                    <span class="step-timer">
                        <img class="pull-left" src="{{asset('/images/watch.png')}}" />
                        <span class="pull-right timer-box">
                            <span class="time" id="time-remaining">{{trans('website.W0842')}}</span>
                            <span class="timer" id="timer">
                                {{___hours($project->dispute->time_left)}}
                            </span>
                        </span>
                    </span>
                @endif
            </li>
            <li @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step == 4)) class="current" @endif @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step > 4)) class="previous" @endif>
                <span class="step">4</span>
                <span class="step-detail">{{trans('website.W0826')}}</span>
                @if((!empty($project->dispute->step) && $project->dispute->step == 4)   && !empty($project->dispute->time_left) && $project->dispute->can_reply == DEFAULT_YES_VALUE)
                    <span class="step-timer">
                        <img class="pull-left" src="{{asset('/images/watch.png')}}" />
                        <span class="pull-right timer-box">
                            <span class="time" id="time-remaining">{{trans('website.W0842')}}</span>
                            <span class="timer" id="timer">
                                {{___hours($project->dispute->time_left)}}
                            </span>
                        </span>
                    </span>
                @endif
            </li>
            <li @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step == 5)) class="current" @endif @if(!empty($project->dispute) && (!empty($project->dispute->step) && $project->dispute->step > 5)) class="previous" @endif>
                <span class="step">5</span>
                <span class="step-detail">{{trans('website.W0827')}}</span>
            </li>
        </ul>
    </div>
</div>
@push('inlinescript')
    @if(!empty($project->dispute))
        <script type="text/javascript" src="{{asset('js/easytimer.js')}}"></script>
        <script type="text/javascript">
            var timer = new Timer();
            timer.start({countdown: true, startValues: {seconds: {{timetosecond($project->dispute->time_left)}}}});
            
            timer.addEventListener('secondsUpdated', function (e) {
                $('#timer').html(timer.getTimeValues().hours + 'h ' + timer.getTimeValues().minutes + 'm');
            });
        </script>
    @endif
@endpush