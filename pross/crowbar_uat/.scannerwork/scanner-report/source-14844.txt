<div class="jobdetails-sidebar-content top-talent-listing">
    <h2 class="form-heading">{{ trans('general.M0214') }}</h2>
    @foreach($top_talent_user as $key => $item)
        <div class="employer-header-xs clearfix">
            <div class="employer-display-xs"><img src="{{ url($item['picture']) }}"></div>
            <div class="employer-details-xs">
                <h4><a href="{{ url(sprintf('%s/find-talents/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($item['id_user']))) }}">{{ $item['name'] }}</a></h4>
                <h5 class="member-residence">Sr. Software Engineer<span>(Uttar Pradesh)</span></h5>
                <div class="rating-review">
                    <span class="rating-block">
                        {!! ___ratingstar($item['rating']) !!}
                    </span>
                </div>
                @if(0)
                    <a href="{{ url(sprintf('%s/find-talents/profile?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($item['id_user']))) }}" class="hire-me-link">
                        <img src="{{ asset('images/hire_me.png') }}"><span>{{trans('job.J00118')}}</span>
                    </a> 
                    <a href="javascript:void(0);" class="hire-me-link" data-user="{{$item['id_user']}}" data-request="inline-ajax" data-url="{{url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s',EMPLOYER_ROLE_TYPE,___encrypt(\Auth::user()->id_user),___encrypt($item['id_user'])))}}" >
                        <img src="{{ asset('images/message-profile-icon.png') }}" width="23"><span>{{trans('website.W0295')}}</span>
                    </a>
                @endif

            </div>
        </div>
    @endforeach
</div>