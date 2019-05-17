<div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
    <div class="profile-left">
        <div class="user-profile-image">
            <div class="user-display-details">
                <div class="user-display-image" style="background: url('{{ $talent_detail['company_logo'] }}') no-repeat center center;background-size:100% 100%"></div>
            </div>
        </div>
    </div>
    <div class="profile-right no-padding">
        <div class="user-profile-details">
            <div class="item-list">
                <div class="rating-review">
                    <span class="rating-block">
                        {!! ___ratingstar($talent_detail['rating']) !!}
                    </span>
                    <a href="{{ url(sprintf('%s/profile/reviews',TALENT_ROLE_TYPE)) }}" class="reviews-block underline">{{ $talent_detail['total_review'] }} {{trans('website.W0213')}}</a>
                </div>
            </div>
            @if(!empty($talent_detail['interests']))
                <div class="item-list">
                    <span class="item-heading">{{trans('website.W0660')}}</span>
                    <span class="item-description">
                        @foreach($talent_detail['interests'] as $item)
                            @if($item['interest'] != 'fixed')
                                {!!sprintf('%s/%s',___currency($item['workrate'],true,true),substr($item['interest'],0,-2)).'<br>'!!}
                            @else
                                <span class="label-green color-grey">{{$item['interest']}}</span>
                            @endif
                        @endforeach
                    </span>
                </div>
            @endif
            @if(!empty($talent_detail['country_name']))
                <div class="item-list">
                    <span class="item-heading">{{trans('website.W0201')}}</span>
                    <span class="item-description">{{$talent_detail['country_name']}}</span>
                </div>
            @endif
        </div>        
    </div>
    <div class="view-profile-name">
        <div class="user-name-info">
            <p>{{ $talent_detail['name'] }}</p>
        </div>
        <div class="profile-expertise-column">
            @if(!empty($talent_detail['expertise']))
                <span class="label-green color-grey">{{ expertise_levels($talent_detail['expertise']) }}</span>
            @endif
            @if(!empty($talent_detail['expertise']))
                <span class="experience">{{ sprintf("%s %s",$talent_detail['experience'],trans('website.W0669')) }}</span>
            @endif

            @if($status == 'accepted')
                <a data-request="accept-decline" data-url="{{ url(sprintf('%s/invitation-status?invite_id=%s&status=disconnect',EMPLOYER_ROLE_TYPE,___encrypt($id_invite))) }}" class="label-green color-grey">{{trans('website.W0704')}}</a>
            @else
                <a data-request="accept-decline" data-url="{{ url(sprintf('%s/invitation-status?invite_id=%s&status=accept',EMPLOYER_ROLE_TYPE,___encrypt($id_invite))) }}" class="label-green">{{trans('website.W0221')}}</a>        
                <a data-request="accept-decline" data-url="{{ url(sprintf('%s/invitation-status?invite_id=%s&status=decline',EMPLOYER_ROLE_TYPE,___encrypt($id_invite))) }}" class="label-green color-grey">{{trans('website.W0220')}}</a>
            @endif
        </div>      
    </div>
</div>