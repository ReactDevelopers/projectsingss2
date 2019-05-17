<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
        <div class="profile-left">
            <div class="user-profile-image">
                <div class="user-display-details">
                    <div class="user-display-image" style="background: url('{{ $talent->company_logo }}') no-repeat center center;background-size:100% 100%"></div>
                </div>
            </div>
        </div>
        <div class="profile-right no-padding">
            <div class="user-profile-details">
                <div class="item-list">
                    <div class="rating-review">
                        <span class="rating-block">
                            {!! ___ratingstar($talent->rating) !!}
                        </span>
                        <a href="{{ url(sprintf('%s/find-talents/reviews?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($talent->id_user))) }}" class="reviews-block underline">{{ $talent->total_review }} {{trans('website.W0213')}}</a>
                    </div>
                </div>
                @if(!empty($talent->interests->count()))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0660')}}</span>
                        <span class="item-description">
                            @foreach($talent->interests as $item)
                                @if($item['interest'] != 'fixed')
                                    {{sprintf('%s/%s',___currency($item['workrate'],true,true),substr($item['interest'],0,1))}}
                                @else
                                    <span class="label-green color-grey">{{$item['interest']}}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endif
                @if(!empty($talent->country_name))
                    <div class="item-list">
                        <span class="item-heading">{{trans('website.W0201')}}</span>
                        <span class="item-description">{{$talent->country_name}}</span>
                    </div>
                @endif
            </div>        
        </div>
        <div class="profile-completion-block profile-completion-list">
            <div class="clearfix"></div>
        </div>
        <div class="view-profile-name">
            <div class="user-name-info">
                <p>{{ $talent->name }}</p>
            </div>
        </div>
    </div>
</div>