<div class="headerWrapper homepage-header">
    <div class="splashHeader">        
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                @if(!empty(auth()->guard('web')->user()))
                    @includeIf('language')
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="navbar-header">
                                <a href="{{ url(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE)) }}" class="navbar-brand logo">
                                    <img src="{{ asset('images/splashLogo.png') }}" class="web-logo">
                                    <img src="{{ asset('images/responsive-logo.png') }}" class="responsive-logo">
                                </a>
                            </div>
                        </div>                
                        <div class="col-md-4 col-sm-4 col-xs-12 pull-right account-block">
                            <div class="header-innerWrapper">
                                <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                                    <ul class="nav navbar-nav">
                                        <li>
                                            <a href="{{ url(sprintf('/%s/chat',EMPLOYER_ROLE_TYPE)) }}" class="message-notification"><span data-target="chat-count" style="display: none;"></span></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" id="notification-toggle" class="notification notification-toggle"><span data-target="notification-count" style="display: none;"></span></a>
                                            <ul class="dropdown-submenu notification-submenu" data-target="notification-list">
                                                <li><img style="margin: 30px auto 0;display: inherit;height: 20px;" src="{{asset('/images/loading.gif')}}"></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a id="usermenu-toggle" href="javascript:void(0);" class="username">
                                                <span class="hidden-xs" style="display: inline-block;text-align: right;"><span class="hello-msg">Hello</span><br>{{ sprintf("%s",auth()->guard('web')->user()->first_name) }}</span>
                                                <img src="{{ asset('images/user-icon.png') }}" alt="user">
                                            </a>
                                            <ul class="dropdown-submenu usermenu-submenu">
                                                <li><a href="{{ url(sprintf('%s/profile',EMPLOYER_ROLE_TYPE))}}">{{ trans('website.W0606') }}</a></li>
                                                <li><a href="{{ url(sprintf('%s/profile/edit/one',EMPLOYER_ROLE_TYPE)) }}">{{ trans('website.W0610') }}</a></li>
                                                <li><a href="{{ url(sprintf('%s/settings',EMPLOYER_ROLE_TYPE)) }}">{{ trans('website.W0598') }}</a></li>
                                                <li><a href="{{ url(sprintf('%s/payment/card/manage',EMPLOYER_ROLE_TYPE)) }}">{{ trans('website.W0607') }}</a></li>
                                                <li><a href="{{ url(sprintf('%s/invitation-list',EMPLOYER_ROLE_TYPE)) }}">{{ trans('website.W0703') }}</a></li>
                                                <li><a href="{{ url('logout') }}">{{ trans('website.W0609') }}</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>                    
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 search-block">
                            
                        </div>                    
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-6 header-logo">
                            <a href="{{ url('/') }}" class="navbar-brand logo">
                                <img src="{{ asset('/images/splashLogo.png') }}">
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-3 col-xs-12 hideOnMobile">
                            <div class="center-links text-center">
                                <a href="{{ url('/page/how-it-works') }}">How it works</a>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-5 col-xs-6 header-login-options">
                            <div class="header-social-links">
                                <a href="{{ url('/login') }}" class="btn btn-sm redShedBtn">Login</a>
                                <div class="header-social-wrapper">
                                    <h6>Login with social media</h6>
                                    <ul class="signup-Options">
                                        <li><a href="{{ asset('/login/linkedin') }}" class="linkedin-option"><span><img src="{{ asset('images/linkedin-small-icon.png') }}"></span></a></li>
                                        <li><a href="{{ asset('/login/facebook') }}" class="facebook-option"><span><img src="{{ asset('images/facebook-small-icon.png') }}"></span></a></li>
                                        <li><a href="{{ asset('/login/instagram') }}" class="instagram-option"><span><img src="{{ asset('images/instagram-small-icon.png') }}"></span></a></li>
                                        <li><a href="{{ asset('/login/twitter') }}" class="twitter-option"><span><img src="{{ asset('images/t-w-i-t-t-e-r-small-icon.png') }}"></span></a></li>

                                        {{--<li><a href="javascript:void(0);" class="linkedin-option" onclick="swal('Sign Up is disabled temporarily.');"><span><img src="{{ asset('images/linkedin-small-icon.png') }}"></span></a></li>
                                        <li><a href="javascript:void(0);" class="facebook-option" onclick="swal('Sign Up is disabled temporarily.');"><span><img src="{{ asset('images/facebook-small-icon.png') }}"></span></a></li>
                                        <li><a href="javascript:void(0);" class="instagram-option" onclick="swal('Sign Up is disabled temporarily.');"><span><img src="{{ asset('images/instagram-small-icon.png') }}"></span></a></li>
                                        <li><a href="javascript:void(0);" class="twitter-option" onclick="swal('Sign Up is disabled temporarily.');"><span><img src="{{ asset('images/t-w-i-t-t-e-r-small-icon.png') }}"></span></a></li> --}}
                                    </ul>                             
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </nav>
        @includeIf('front.includes.banner-slider')
    </div>
</div>
