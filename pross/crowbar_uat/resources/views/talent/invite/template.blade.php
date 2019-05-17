<div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
    <div class="profile-left">
        <div class="user-profile-image">
            <div class="user-display-details">
                <div class="user-display-image" style="background: url('{{ $employer_detail['company_logo'] }}') no-repeat center center;background-size:100% 100%"></div>
            </div>
        </div>
    </div>
    <div class="profile-right no-padding">
        <div class="user-profile-details">
            <div class="item-list">
                <span class="item-heading">{{trans('website.W0096')}}</span>
                <span class="item-description">
                    @if(!empty($employer_detail['company_name']))
                        {{$employer_detail['company_name']}}
                    @else
                        {{N_A}}
                    @endif
                </span>
            </div>

            <div class="item-list">
                <span class="item-heading">{{trans('website.W0201')}}</span>
                <span class="item-description">
                    @if(!empty($employer_detail['country_name']))
                        {{$employer_detail['country_name']}}
                    @else
                        {{N_A}}
                    @endif
                </span>
            </div>

            <div class="item-list">
                <span class="item-heading">{{trans('website.W0226')}}</span>
                <span class="item-description">
                    @if(!empty($employer_detail['projects_count']))
                        {{$employer_detail['projects_count']}}
                    @else
                        {{N_A}}
                    @endif
                </span>
            </div>
        </div>        
    </div>
    <div class="profile-completion-block profile-completion-list"></div>
    <div class="view-profile-name">
        <div class="user-name-info no-padding">
            <p>{{ $employer_detail['name'] }}</p>
            <small class="small-info">{{trans('website.W0439')}} {{$employer_detail['member_since']}}</small>
        </div>
    </div>
</div>