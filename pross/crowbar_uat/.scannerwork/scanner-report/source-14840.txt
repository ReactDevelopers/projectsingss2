<div class="clearfix profile-upper-wrap">
    <div class="col-md-3 col-sm-4 col-xs-12 left-sidebar">
        <div class="user-info-wrapper">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image"  style="background: url('{{ $talent['picture'] }}') no-repeat center center;background-size:100% 100%">
                    </div>
                </div>
            </div>        
        </div>
    </div> 
    <div class="col-md-9 col-sm-8 col-xs-12 right-sidebar">
        <div class="user-view-block">
            <h2>{{ sprintf("%s %s",$talent['first_name'],$talent['last_name']) }}</h2>            
            <h5 class="member-residence">
                @if(!empty($talent['work_experiences']))
                    {{ $talent['work_experiences'][0]['jobtitle'] }}
                    <span>({{$talent['work_experiences'][0]['state_name']}})</span>
                @elseif(!empty($talent['country']))
                    {{___cache('countries',$talent['country'])}}
                @endif
            </h5>
            <span class="membership-time">{{trans('website.W0439')}} {{___d(date('Y-m-d',strtotime($talent['created'])))}}</span>
            <br>
            <span class="membership-time">{{ $last_viewed }}</span>
        </div>
        <div class="skill-tags">
            <div class="rating-review">
                <span class="rating-block">
                    {!! ___ratingstar($talent['rating']) !!}
                </span>
                <a href="{{ url(sprintf('%s/find-talents/reviews?talent_id=%s',EMPLOYER_ROLE_TYPE,\Request::get('talent_id'))) }}" class="reviews-block underline">
                    {{ $talent['review'] }} {{trans('website.W0213')}}
                </a>
                @if(0)
                    <a class="hire-me-link" data-target="#hire-me" data-request="ajax-modal" data-url="{{url(sprintf('%s/hire/talent?talent_id=%s',EMPLOYER_ROLE_TYPE,\Request::get('talent_id')))}}">
                        <img src="{{ asset('images/hire_me.png') }}"><span>{{trans('job.J00118')}}</span>
                    </a> 
                @endif
                <a href="javascript:void(0);" class="hire-me-link" data-user="{{___decrypt(\Request::get('talent_id'))}}" data-request="inline-ajax" data-url="{{url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s',EMPLOYER_ROLE_TYPE,___encrypt(\Auth::user()->id_user),\Request::get('talent_id')))}}" >
                    <img src="{{ asset('images/message-profile-icon.png') }}" width="23"><span>{{trans('website.W0295')}}</span>
                </a>
            </div>
            <div class="skills-tag">
                {!! added_skills($talent['skills']) !!}
            </div>     
        </div>
    </div>
</div>