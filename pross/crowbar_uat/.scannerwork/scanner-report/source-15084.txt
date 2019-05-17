<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%"></div>
                </div>
            </div>
        </div>
        <div class="profile-right no-padding">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            {!! ___ratingstar($user['rating']) !!}
                        </span>
                        <a href="{{ url(sprintf('%s/profile/reviews',TALENT_ROLE_TYPE)) }}" class="reviews-block underline">{{ $user['review'] }} {{trans('website.W0213')}}</a>
                    </div>
                </div>
                @if(!empty($user['remuneration']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0660')}}</span>
                        <span class="item-description">
                            @foreach($user['remuneration'] as $item)
                                @if($item['interest'] != 'fixed')
                                    {{sprintf('%s/%s',___currency($item['workrate'],true,true),substr($item['interest'],0,1))}}
                                @else
                                    <span class="label-green color-grey">{{$item['interest']}}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endif
                @if(!empty($user['country_name']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0201')}}</span>
                        <span class="item-description">{{$user['country_name']}}</span>
                    </div>
                @endif
            </div>        
        </div>
        <div class="profile-completion-block profile-completion-list">
            <div class="clearfix"></div>
            <div class="edit-bar">
                <div class="completion-bar">
                    <span style="width: {{ ___decimal($user['profile_percentage_count']) }}%;">
                        <span class="percentage-completed floated-percent">{{ ___decimal($user['profile_percentage_count']) }}%</span>
                    </span>
                </div>
                <a title="{{trans('website.W0255')}}" href="{{url(sprintf('%s/profile/step/one',TALENT_ROLE_TYPE))}}"><img src="{{asset('images/big-pencil-con.png')}}"></a>
            </div>
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p>{{ sprintf("%s %s",$user['first_name'],$user['last_name']) }}</p>
            </div>
            <div class="profile-expertise-column">
                @if(!empty($user['expertise']))
                    <span class="label-green color-grey">{{ expertise_levels($user['expertise']) }}</span>
                @endif
                @if(!empty($user['expertise']))
                    <span class="experience">{{ sprintf("%s %s",$user['experience'],trans('website.W0669')) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>