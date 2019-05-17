@include('employer.viewprofile.includes.sidebar-tabs',$user)                    
<div class="inner-profile-section">
    <div class="view-information no-padding">
        <h2>{{ trans('website.W0269') }}</h2>
    </div>
    <div class="view-information white-wrapper">
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0247') }}</label>
            @if(!empty($user['company_profile']))
                <label class="info-label col-md-8">{{ ucfirst($user['company_profile']) }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0096') }}</label>
            @if(!empty($user['company_name']))
                <label class="info-label col-md-8">{{ $user['company_name'] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        @if($user['company_profile'] == 'individual')
            <div class="form-group clearfix">
                <label class="control-label col-md-4">{{ trans('website.W0200') }}</label>
                @if(!empty($user['company_work_field']))
                    <label class="info-label col-md-8">{{ ___cache("skills",$user['company_work_field']) }}</label>
                @else
                    <label class="info-label col-md-8">{{ N_A }}</label>
                @endif
            </div>
            @if(0)
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">{{ trans('website.W0251') }}</label>
                    <div class="col-md-8 js-example-tags-container">
                        @if(!empty($user['certificates']))
                            <ul>{!! ___tags($user['certificates'],'<li class="tag-selected"><a href="'.url(sprintf('%s/profile/edit/setup',EMPLOYER_ROLE_TYPE)).'" class="destroy-tag-selected">×</a>%s</li>',' ') !!}</ul>
                        @else
                            {{ N_A }}
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="form-group clearfix">
                <label class="control-label col-md-4">{{ trans('website.W0248') }}</label>
                @if(!empty($user['contact_person_name']))
                    <label class="info-label col-md-8">{{ $user['contact_person_name'] }}</label>
                @else
                    <label class="info-label col-md-8">{{ N_A }}</label>
                @endif
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-md-4">{{ trans('website.W0249') }}</label>
                @if(!empty($user['company_website']))
                    <label class="info-label col-md-8">{{ $user['company_website'] }}</label>
                @else
                    <label class="info-label col-md-8">{{ N_A }}</label>
                @endif
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-md-4">{{ trans('website.W0200') }}</label>
                @if(!empty($user['company_work_field']))
                    <label class="info-label col-md-8">{{ ___cache("skills",$user['company_work_field']) }}</label>
                @else
                    <label class="info-label col-md-8">{{ N_A }}</label>
                @endif
            </div>            
            <div class="form-group clearfix">
                <label class="control-label col-md-4">{{ trans('website.W0252') }}</label>
                @if(!empty($user['company_biography']))
                    <label class="info-label col-md-8">{{ $user['company_biography'] }}</label>
                @else
                    <label class="info-label col-md-8">{{ N_A }}</label>
                @endif
            </div>
        @endif
    </div>
    <div class="view-information">
        <h2>{{ trans('website.W0197') }}<a href="{{url(sprintf('%s/profile/edit/general',EMPLOYER_ROLE_TYPE))}}" title="Edit" class="edit-me hide"><img src="{{asset('images/edit-icon.png')}}" /></a></h2>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0122') }}</label>
            @if(!empty($user['mobile']))
                <label class="info-label col-md-8">{{ $user['country_code'].' '.$user['mobile'] }}@if($user['is_mobile_verified'] == DEFAULT_YES_VALUE)<img src="{{asset('images/completed-step.png')}}" alt="verified" /> @endif</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0245') }}</label>
            @if(!empty($user['other_mobile']))
                <label class="info-label col-md-8">{{ $user['other_country_code'].' '.$user['other_mobile'] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0246') }}</label>
            @if(!empty($user['website']))
                <label class="info-label col-md-8">{{ $user['website'] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0054') }}</label>
            @if(!empty($user['address']))
                <label class="info-label col-md-8">{{ $user['address'] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ sprintf(trans('website.W0055'),'') }}</label>
            @if(!empty($user['country']))
                <label class="info-label col-md-8">{{ !empty(\Cache::get('countries')[$user['country']]) ? \Cache::get('countries')[$user['country']] : N_A }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ sprintf(trans('website.W0056'),'') }}</label>
            @if(!empty($user['state']))
                <label class="info-label col-md-8">{{ \Cache::get('states')[$user['state']] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-md-4">{{ trans('website.W0057') }}</label>
            @if(!empty($user['postal_code']))
                <label class="info-label col-md-8">{{ $user['postal_code'] }}</label>
            @else
                <label class="info-label col-md-8">{{ N_A }}</label>
            @endif
        </div>
    </div>
</div>