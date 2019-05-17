<div class="footerWrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-7 col-xs-12 footer-left-sec">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">About</p>
                            <ul>
                                <li><a href="{{ url('/page/how-it-works') }}">How It Works</a></li>
                                <li><a href="{{ url('/page/talent') }}">Talent</a></li>
                                <li><a href="{{ url('/page/employer') }}">Employer</a></li>
                                <li><a href="{{ url('/page/pricing') }}">Pricing</a></li>
                                <li><a href="{{ url('/page/faq') }}">FAQs</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">Get Started</p>
                            <ul>
                                <li><a href="{{ url('/signup/talent') }}">Register as talent</a></li>
                                <li><a href="{{ url('/signup/employer') }}">Register as employer</a></li>
                                <li><a href="{{ url('/login') }}">Login</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">Networks</p>
                            <ul>
                                <li><a href="{{$settings['social_linkedin_url']}}"/>LinkedIn</a></li>
                                <li><a href="{{$settings['social_facebook_url']}}"/>Facebook</a></li>
                                <li><a href="{{$settings['social_instagram_url']}}"/>Instagram</a></li>
                                <li><a href="{{$settings['social_youtube_url']}}"/>Youtube</a></li>
                                <li><a href="{{$settings['social_twitter_url']}}"/>Twitter</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="footerNav">
                            <p class="navTitle">Legal</p>
                            <ul>
                                <li><a href="{{ url('/page/dispute') }}">Dispute</a></li>
                                <li><a href="{{ url('/page/secure-payment') }}">Secure Payments</a></li>
                                <li><a href="{{ url('/page/terms-and-conditions') }}">T&C</a></li>
                                <li><a href="{{ url('/page/privacy-policy') }}">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-offset-1 col-md-4 col-sm-5 col-xs-12 footer-right-sec">
                <div class="footerNav">
                    <p class="navTitle">Download App</p>
                    <div class="downloadAppLinks">
                        <a href="{{$settings['ios_download_app_url']}}">
                            <button type="button" class="appStoreBtn"></button>
                        </a>
                        <a href="{{$settings['android_download_app_url']}}">
                            <button type="button" class="playStoreBtn"></button>
                        </a>
                    </div>
                    <div class="copyright">
                        <p>© 2016 Crowbar. All rights reserved.</p>
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

        var notification = new notification({
            socket: socket,
            user: {!! $user['id_user'] !!},
            user_type: "{!! $user['type'] !!}",
            notification_text: "{{ trans('general.M0290') }}",
            no_notification_text: "{{ trans('general.M0291') }}"
        });

        notification.initiate('[data-target="notification-list"]','[data-target="notification-count"]');
        notification.unread_messages('[data-target="chat-count"]');


        if(typeof socket != 'undefined'){
            socket.on("send.notification.action.{!! $user['id_user'] !!}", function($response){
                $('[data-request="job-actions"]').trigger('doubleclick');           
            });
        }
    </script>
@endpush
