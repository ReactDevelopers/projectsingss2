<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%"></div>
                </div>
            </div>
            <div class="user-name-info">
                <p>{{ sprintf("%s %s",$user['first_name'],$user['last_name']) }}</p>
                <small class="small-info">{{trans('website.W0439')}} {{date('Y', strtotime($user['created']))}}</small>
            </div>
        </div>
        <div class="profile-right">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            {!! ___ratingstar($user['rating']) !!}
                        </span>
                        <a href="{{ url(sprintf('%s/profile/reviews',EMPLOYER_ROLE_TYPE)) }}" class="reviews-block underline">{{ $user['review'] }} {{trans('website.W0213')}}</a>
                    </div>
                </div>
                @if(!empty($user['company_name']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0096')}}</span>
                        <span class="item-description">{{$user['company_name']}}</span>
                    </div>
                @endif
                @if(!empty($user['country_name']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0201')}}</span>
                        <span class="item-description">{{$user['country_name']}}</span>
                    </div>
                @endif
                @if(!empty($user['job_posted']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0378')}}</span>
                        <span class="item-description">{{$user['job_posted']}}</span>
                    </div>
                @endif
                @if(!empty($user['paid_till_date']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0668')}}</span>
                        <span class="item-description">{{$user['paid_till_date']}}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>