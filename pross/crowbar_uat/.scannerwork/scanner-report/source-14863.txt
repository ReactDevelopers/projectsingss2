<div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
    <div class="profile-left">
        <div class="user-profile-image">
            <div class="user-display-details">
                <a href="{{url('employer/project/proposals/talent?proposal_id='.___encrypt($proposal['proposal_id']).'&project_id='.___encrypt($proposal['project_id']))}}"" class="user-display-image" style="background: url('{{ $proposal['company_logo'] }}') no-repeat center center;background-size:100% 100%; display: block;"></a>
            </div>
            @if(!empty($proposal->project_status != 'closed'))
                @if(!empty($proposal->chat) && in_array($proposal->chat->chat_initiated,['employer','employer-accepted']))
                    <a href="javascript:void(0);" class="profile-chat-link" data-request="chat-initiate" data-user="{{ $proposal->chat->id_chat_request }}" data-url="{{ url(sprintf('%s/chat',EMPLOYER_ROLE_TYPE)) }}">
                       <img src="{{ asset('images/profile-chat.png') }}">
                    </a>
                @elseif($proposal->proposal_status != 'rejected')
                    <a href="javascript:void(0);" class="profile-chat-link" data-request="inline-ajax" data-url="{{url(sprintf('%s/chat/employer-chat-request?sender=%s&receiver=%s&project_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($proposal['company_id']),___encrypt($proposal['talent_id']),___encrypt($proposal['project_id'])))}}">
                       <img src="{{ asset('images/profile-chat.png') }}">
                    </a>
                @endif
            @endif
        </div>
    </div>
    <div class="profile-right no-padding">
        <div class="user-profile-details">
            <div class="item-list">
                <div class="rating-review">
                    <span class="rating-block">
                        {!! ___ratingstar($proposal['rating']) !!}
                    </span>
                    <a href="{{ url(sprintf('%s/profile/reviews',TALENT_ROLE_TYPE)) }}" class="reviews-block underline">{{ $proposal['total_review'] }} {{trans('website.W0213')}}</a>
                </div>
            </div>
            <div class="item-list">
                <span class="item-heading">{{trans('website.W0660')}}</span>
                <span class="item-description">
                    @if($proposal['employment'] != 'fixed')
                        {!!sprintf('%s/%s',___format($proposal['quoted_price'],true,true),substr($proposal['employment'],0,-2)).'<br>'!!}
                    @else
                        <span class="label-green color-grey">{{$proposal['employment']}}</span>
                    @endif
                </span>
            </div>
            @if($proposal['country_name'])
                <div class="item-list">
                    <span class="item-heading">{{trans('website.W0201')}}</span>
                    <span class="item-description">{{$proposal['country_name']}}</span>
                </div>
            @endif
            <div class="item-list">
                <span class="label-green color-grey">{{ $proposal->current_proposals_status }}</span>
            </div>            
        </div>        
    </div>
    <div class="view-profile-name">
        <div class="user-name-info">
            <p><a href="{{url('employer/project/proposals/talent?proposal_id='.___encrypt($proposal['proposal_id']).'&project_id='.___encrypt($proposal['project_id']))}}">{{ $proposal['name'] }}</p>
            <small class="small-info">{{trans('website.W0439')}} {{$proposal['member_since']}}</small>
        </div>
        <div class="profile-expertise-column">
            @if(!empty($proposal['last_viewed']))
                <span class="last-viewed-icon active"></span>
            @endif

            @if($proposal['is_saved'] == DEFAULT_YES_VALUE)
                <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="{{url(sprintf('%s/save?talent_id=%s',EMPLOYER_ROLE_TYPE,$proposal['id_user']))}}"></a>
            @else
                <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="{{url(sprintf('%s/save?talent_id=%s',EMPLOYER_ROLE_TYPE,$proposal['id_user']))}}"></a>
            @endif

            @if(!empty($proposal['expertise']))
                <span class="label-green color-grey">{{ expertise_levels($proposal['expertise']) }}</span>
            @endif
            @if(!empty($proposal['experience']))
                <span class="experience">{{ sprintf("%s %s",$proposal['experience'],trans('website.W0669')) }}</span>
            @endif
        </div>
    </div>
</div>