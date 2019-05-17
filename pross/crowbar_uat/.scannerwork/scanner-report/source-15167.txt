<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('{{ $project->employer->company_logo }}') no-repeat center center;background-size:100% 100%"></div>
                </div>
                @if(!empty($project->chat) && in_array($project->chat->chat_initiated,['employer','employer-accepted']))
                    <a href="javascript:void(0);" class="profile-chat-link"  data-request="chat-initiate" data-user="{{ $project->chat->id_chat_request }}" data-url="{{ url(sprintf('%s/chat',TALENT_ROLE_TYPE)) }}">
                       <img src="{{ asset('images/profile-chat.png') }}">
                    </a>
                @endif
            </div>
        </div>
        <div class="profile-right no-padding">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            {!! ___ratingstar($project->employer->rating) !!}
                        </span>
                        <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}" class="reviews-block underline">{{ $project->employer->reviews_count }} {{trans('website.W0213')}}</a>
                    </div>
                </div>

                <div class="item-list">
                    <span class="item-heading">{{trans('website.W0096')}}</span>
                    <span class="item-description">
                        @if(!empty($project->employer->company_name))
                            {{$project->employer->company_name}}
                        @else
                            {{N_A}}
                        @endif
                    </span>
                </div>

                <div class="item-list">
                    <span class="item-heading">{{trans('website.W0201')}}</span>
                    <span class="item-description">
                        @if(!empty($project->employer->country_name))
                            {{$project->employer->country_name}}
                        @else
                            {{N_A}}
                        @endif
                    </span>
                </div>

                @if(0)
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0226')}}</span>
                        <span class="item-description">
                            @if(!empty($project->employer->projects_count))
                                {{$project->employer->projects_count}}
                            @else
                                {{N_A}}
                            @endif
                        </span>
                    </div>

                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0228')}} {{trans('website.W0229')}}</span>
                        <span class="item-description">
                            @if(!empty($project->employer->hirings_count))
                                {{$project->employer->hirings_count}}
                            @else
                                {{N_A}}
                            @endif
                        </span>
                    </div>

                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0668')}}</span>
                        <span class="item-description">
                            @if(!empty($project->employer->transaction))
                                {{___format($project->employer->transaction->total_paid_by_employer,true,true,true)}}
                            @else
                                {{N_A}}
                            @endif
                        </span>
                    </div>
                @endif
            </div>        
        </div>
        <div class="profile-completion-block profile-completion-list">
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p><a href="{{url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))}}">{{ $project->employer->name }}</a></p>
                <small class="small-info">{{trans('website.W0439')}} {{$project->employer->member_since}}</small>
            </div>
            @if(0)
            <div class="profile-expertise-column">
                @if($project->employer->is_saved === DEFAULT_YES_VALUE)
                    <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="{{url('talent/save/employer?employer_id='.$project->employer->id_user)}}"></a>
                @else
                    <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="{{url('talent/save/employer?employer_id='.$project->employer->id_user)}}"></a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>