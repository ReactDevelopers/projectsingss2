<div class="clearfix profile-upper-wrap">
    <div class="col-md-3 col-sm-4 col-xs-12 left-sidebar">
        <div class="user-info-wrapper">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="192" data-height="192" data-folder="{{TALENT_PROFILE_PHOTO_UPLOAD}}" data-record="0" data-column="profile" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%">
                    </div>
                </div>
            </div>        
        </div>
    </div>
    <div class="col-md-9 col-sm-8 col-xs-12 right-sidebar">
        <div class="user-view-block">
            <h2>{{ sprintf("%s %s",$user['first_name'],$user['last_name']) }}</h2>            
            <h5 class="member-residence">
                {{ $user['company_name'] }}
                @if(!empty($user['country']))
                    <span>({{___cache('countries',$user['country'])}})</span>
                @endif
            </h5>
            <span class="membership-time">{{trans('website.W0439')}} {{___d(date('Y-m-d',strtotime($user['created'])))}}</span>
        </div>
        <div class="skill-tags">
            <div class="rating-review">
                <span class="rating-block">
                    {!! ___ratingstar($user['rating']) !!}
                </span>
                <a href="{{ url(sprintf('%s/profile/reviews',EMPLOYER_ROLE_TYPE)) }}" class="reviews-block underline">{{ $user['review'] }} {{trans('website.W0213')}}</a>
            </div>
            <div class="skills-tag">
                {!! added_skills($user['certificates']) !!}
            </div>            
        </div>
    </div>
</div>
