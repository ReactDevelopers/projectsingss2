<div class="col-md-4 col-sm-12 left-sidebar clearfix">
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
                        <a href="javascript:void(0);" class="reviews-block underline">{{ $user['review'] }} {{trans('website.W0213')}}</a>
                    </div>
                </div>
                @if(!empty($user['country_name']))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0201')}}</span>
                        <span class="item-description">{{$user['country_name']}}</span>
                    </div>
                @endif
            </div>        
        </div>
        <div class="clearfix"></div>
        <div class="profile-completion-block profile-completion-list">
            <div class="edit-bar">
                <div class="completion-bar">
                    <span style="width: {{ ___decimal($user['profile_percentage_count']) }}%;">
                        <span class="percentage-completed floated-percent">{{ ___decimal($user['profile_percentage_count']) }}%</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p>{{ sprintf("%s %s",$user['view_talent_first_name'],$user['view_talent_last_name']) }}</p>
            </div>
            <div class="profile-expertise-column">
                @if(!empty($user['expertise']))
                    <span class="label-green color-grey">{{ expertise_levels($user['expertise']) }}</span>
                @endif
                @if(!empty($user['experience']))
                    <span class="experience">{{ sprintf("%s %s",$user['experience'],trans('website.W0669')) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>