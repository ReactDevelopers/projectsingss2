<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox">
        <div class="user-profile-image">
            <div class="user-display-details">
                <div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="192" data-height="192" data-folder="{{TALENT_PROFILE_PHOTO_UPLOAD}}" data-record="0" data-column="profile" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%"></div>
            </div>
        </div>
        <div class="profile-completion-block profile-completion-list">
            <div class="clearfix"></div>
            <div class="completion-bar">
                <span style="width: {{ ___decimal($user['profile_percentage_count']) }}%;">
                    <span class="percentage-completed floated-percent">{{ ___decimal($user['profile_percentage_count']) }}%</span>
                </span>
            </div>
        </div>
        <div class="user-name-info">
            <p>{{ sprintf("%s %s",$user['first_name'],$user['last_name']) }}</p>
        </div>        
    </div>
   
</div>