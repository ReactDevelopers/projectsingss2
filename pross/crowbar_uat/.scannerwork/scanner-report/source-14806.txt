<div class="profileprogress-steps generalprogress-steps">
    <div class="container">
        <ul class="profileprogress-group clearfix">
            <li class="@if(in_array('setup',$steps)) {{ 'selected'}} @endif">
                <a href="{{ url(sprintf('%s/profile/setup',EMPLOYER_ROLE_TYPE)) }}" title="{{ trans('website.W0269') }}">
                    <img src="{{ asset('images/setup-profile.png') }}" />
                    <span>{{ trans('website.W0269') }}</span>
                </a>
            </li>
            <li class="@if(in_array('general',$steps)) {{ 'selected'}} @endif">
                <a href="{{ url(sprintf('%s/profile/general',EMPLOYER_ROLE_TYPE)) }}" title="{{ trans('website.W0268') }}">
                    <img src="{{ asset('images/personalinfo-icon.png') }}" />
                    <span>{{ trans('website.W0268') }}</span>
                </a>
            </li>
            <li class="@if(in_array('verify-account',$steps)) {{ 'selected'}} @endif">
                <a href="{{ url(sprintf('%s/profile/verify-account',EMPLOYER_ROLE_TYPE)) }}" title="{{ trans('website.W0270') }}">
                    <img src="{{ asset('images/verify-icon.png') }}" />
                    <span>{{ trans('website.W0270') }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
