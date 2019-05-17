<div class="footerWrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-7 col-xs-12 footer-left-sec">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">{{trans('website.W0923')}}</p>
                            <ul>
                                <li><a href="{{ url('/page/how-it-works') }}">{{trans('website.W0502')}}</a></li>
                                <li><a href="{{ url('/page/how-it-works?section=get-hired') }}">{{trans('website.W0503')}}</a></li>
                                <li><a href="{{ url('/page/how-it-works?section=hire-talent') }}">{{trans('website.W0504')}}</a></li>
                                @if(\Cache::get('configuration')['display_pricing_page'] === 'yes')
                                    @if(empty(\Auth::guard('web')->check()) || \Auth::guard('web')->user()->type !== 'talent')
                                        <li><a href="{{ url('/page/pricing') }}">{{trans('website.W0505')}}</a></li>
                                    @endif
                                @endif
                                @if(0)
                                    <li><a href="{{ url('/page/community') }}">{{trans('website.W0506')}}</a></li>
                                @endif
                                <li><a href="{{ url('/page/faq') }}">{{trans('website.W0732')}}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">{{trans('website.W0512')}}</p>
                            <ul>
                                <li><a target="_blank" href="{{\Cache::get('configuration')['social_linkedin_url']}}"/>{{trans('website.W0120')}}</a></li>
                                <li><a target="_blank" href="{{\Cache::get('configuration')['social_facebook_url']}}"/>{{trans('website.W0116')}}</a></li>
                                <li><a target="_blank" href="{{\Cache::get('configuration')['social_instagram_url']}}"/>{{trans('website.W0131')}}</a></li>
                                <li><a target="_blank" href="{{\Cache::get('configuration')['social_googleplus_url']}}"/>{{trans('website.W0121')}}</a></li>
                                <li><a target="_blank" href="{{\Cache::get('configuration')['social_twitter_url']}}"/>{{trans('website.W0119')}}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">{{trans('website.W0513')}}</p>
                            <ul>
                                <li><a href="{{ url('/page/dispute') }}">{{trans('website.W0514')}}</a></li>
                                <li><a href="{{ url('/page/secure-payment') }}">{{trans('website.W0515')}}</a></li>
                                <li><a href="{{ url('/page/terms-and-conditions') }}">{{trans('website.W0516')}}</a></li>
                                <li><a href="{{ url('/page/privacy-policy') }}">{{trans('website.W0517')}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-offset-1 col-md-4 col-sm-5 col-xs-12 footer-right-sec">
                <div class="footerNav">
                    <p class="navTitle">{{trans('website.W0518')}}</p>
                    <div class="downloadAppLinks">
                        <a href="{{\Cache::get('configuration')['ios_download_app_url']}}">
                            <button type="button" class="appStoreBtn"></button>
                        </a>
                        <a href="{{\Cache::get('configuration')['android_download_app_url']}}">
                            <button type="button" class="playStoreBtn"></button>
                        </a>
                    </div>
                    <div class="copyright">
                        <p>{{\Cache::get('configuration')['copyright_text']}}</p>
                    </div>              
                </div>
            </div>
        </div>
    </div>
</div>
@push('inlinescript')
    <script src="{{ asset('js/chat/socket.io') }}.js"></script>
    <script>
        var socket = new io.connect(
            '{!!env('SOCKET_CONNECTION_URL')!!}:{!!env('SOCKET_CONNECTION_POST')!!}', {
            'reconnection': true,
            'transports': ['websocket'],
            'reconnectionDelay': 2000,
            'reconnectionDelayMax' : 5000,
            'secure':false
        });

        @if(!empty(\Auth::guard('web')->check()))
            var notification = new notification({
                socket: socket,
                user: {!! \Auth::guard('web')->user()->id_user !!},
                user_type: "{!! \Auth::guard('web')->user()->type !!}",
                notification_text: "{{ trans('general.M0290') }}",
                no_notification_text: "{{ trans('general.M0291') }}"
            });

            notification.initiate('[data-target="notification-list"]','[data-target="notification-count"]');
            notification.unread_messages('[data-target="chat-count"]');


            if(typeof socket != 'undefined'){
                socket.on("send.notification.action.{!! \Auth::guard('web')->user()->id_user !!}", function($response){
                    $('[data-request="job-actions"]').trigger('doubleclick');           
                });
            }
        @endif
    </script>
@endpush
