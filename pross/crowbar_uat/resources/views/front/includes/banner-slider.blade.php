<div class="welcome-section">
    <div class="container">
        <div class="tile-sec">
            <div class="tile-registeration-form">
                <form method="POST" role="signup" action="{{ url('/signup/none/process') }}" class="form-horizontal login-form" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ Request::get('token') }}" name="remember_token" />
                    <h2 class="light-heading white-color">Sign up – It’s free!</h2>
                    <div class="form-group has-feedback{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0142')}}@if ($errors->has('first_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('first_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="first_name" value="{{ old('first_name',(!empty($social['social_first_name'])?$social['social_first_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('first_name')}}">
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0143')}}@if ($errors->has('last_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('last_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="last_name" value="{{ old('last_name',(!empty($social['social_last_name'])?$social['social_last_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('last_name')}}">
                        </div>
                    </div>
                    <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0144')}}@if ($errors->has('email') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('email')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="email" value="{{ old('email',(!empty($social['social_email'])?$social['social_email']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('email')}}">
                        </div>
                    </div>
                    <div class="form-group has-feedback toggle-social{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0145')}} @if ($errors->has('password') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('password')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="password" type="password" class="form-control" autocomplete="off"  data-toggle="tooltip" title="{{$errors->first('password')}}">
                        </div>
                    </div>

                    {{-- <div class="form-group">
                        <div class="col-sm-12 col-xs-12">
                            <div class="login-type-radio">
                                <div class="grouped-radio">
                                    <label class="get-hired">
                                        <input type="radio" name="type" value="talent" {{!empty(old('type')) ? (old('type') == "talent"? "checked='checked'": '' ) : "checked='checked'"}} data-request="show-target" data-show="true" data-target="#coupon_code" data-form_action="{{ url('/signup/talent/process') }}" data-form_role='[role="signup"]'>
                                        <span>{{trans('website.W0651')}}</span>
                                    </label>                                        
                                </div>
                                <div class="grouped-radio">
                                    <label class="hire-talent">
                                        <input type="radio" name="type" value="employer" {{ old('type') == "employer" ? "checked='checked'" : '' }} data-request="show-target" data-show="false" data-target="#coupon_code" data-form_action="{{ url('/signup/employer/process') }}" data-form_role='[role="signup"]'>
                                        <span>{{trans('website.W0650')}}</span>
                                    </label>                                        
                                </div>
                            </div>                                
                        </div>
                    </div> --}}

                    <div class="form-group">
                        <div class="col-sm-12 col-xs-12">
                            <div class="login-type-radio">
                                <div class="grouped-radio">
                                    <label class="get-hired">
                                        <input type="radio" name="work_type" value="individual" {{!empty(old('work_type')) ? (old('work_type') == "individual"? "checked='checked'": '' ) : "checked='checked'"}} data-request="show-hide" data-true-condition=".normal-section" data-false-condition=".company-name-section" data-condition="individual">
                                        <span>{{trans('website.W0942')}}</span>
                                    </label>                                        
                                </div>
                                <div class="grouped-radio">
                                    <label class="hire-talent">
                                        <input type="radio" name="work_type" value="company" {{ old('work_type') == "company" ? "checked='checked'" : '' }} data-request="show-hide" data-true-condition=".company-name-section" data-false-condition=".normal-section" data-condition="company">
                                        <span>{{trans('website.W0943')}}</span>
                                    </label>                                        
                                </div>
                            </div>                                
                        </div>
                    </div>  

                    <div class="company-name-section">
                        <div class="form-group has-feedback{{ $errors->has('company_name') ? ' has-error' : '' }}">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">{{trans('website.W0944')}} @if($errors->has('company_name') )<span class="error-help" data-toggle="tooltip" title="{{$errors->first('company_name')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></span>@endif</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input name="company_name" value="{{ old('company_name',(!empty($social['company_name'])?$social['company_name']:''))}}" type="text" class="form-control" data-toggle="tooltip" title="{{$errors->first('company_name')}}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 col-xs-12">
                            <p class="policy-text">{!!trans('website.W0662')!!}</p>
                        </div>
                    </div>                                
                    <div class="form-group submit-form-btn text-center">
                        <div class="col-sm-12 col-xs-12">
                            <input type="hidden" name="social_key" value="{{ (!empty($social['social_key']))?$social['social_key']:"" }}" />
                            <input type="hidden" name="social_id" value="{{ (!empty($social['social_id']))?$social['social_id']:"" }}" />
                            <input type="hidden" name="name" value="{{ (!empty($social['social_name']))?$social['social_name']:"" }}" />
                            <input type="hidden" name="picture" value="{{ (!empty($social['social_picture']))?$social['social_picture']:"" }}" />
                            <input type="hidden" name="country" value="{{ (!empty($social['social_country']))?$social['social_country']:"" }}" />
                            <input type="hidden" name="gender" value="{{ (!empty($social['social_gender']))?$social['social_gender']:"" }}" />
                            {{-- Sign Up link --}}
                            {{-- <a class="btn btn-sm redShedBtn" data-target="#radio-signup" data-request="ajax-modal" data-url="{{url('signup/selectType')}}" href="javascript:void(0);">{{trans('website.W0151')}}</a> --}}
                            {{-- Sign Up button --}}
                            <button type="submit" class="btn btn-sm redShedBtn" id="do_signup">{{trans('website.W0151')}}</button>
                        </div>
                    </div>
                    <ul class="signup-Options">
                        <li><a href="{{ asset('/login/linkedin') }}" class="linkedin-option"><span><img src="{{ asset('images/linkedin-white.png') }}"></span></a></li>
                        <li><a href="{{ asset('/login/facebook') }}" class="facebook-option"><span><img src="{{ asset('images/facebook-white.png') }}"></span></a></li>
                        <li><a href="{{ asset('/login/instagram') }}" class="instagram-option"><span><img src="{{ asset('images/instagram-white.png') }}"></span></a></li>
                        <li><a href="{{ asset('/login/twitter') }}" class="twitter-option"><span><img src="{{ asset('images/twitter-white.png') }}"></span></a></li>
                    </ul>                                
                </form>
                <a class="hide" data-target="#select-type" data-request="ajax-modal" data-url="{{ url('social/signup') }}" href="javascript:void(0);">Select Type</a>
            </div>
            <div class="modal fade upload-modal-box add-payment-cards" id="radio-signup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
            @if(!empty($banner['home']->count()))
                <div class="tiles-wrapper">
                    <ul class="tiles-list">
                        <li class="big-tiles">
                            <span class="tile-image" style="background-image:url({{asset('uploads/banner/'.$banner['home'][0]->banner_image)}});"></span>
                        </li>
                        <li>
                            @if(!empty($banner['home'][1]->banner_text))
                                @php 
                                    $banner_text = explode("\n", $banner['home'][1]->banner_text);

                                    if(!empty($banner_text[0])){
                                        $upper_title = trans('website.'.trim($banner_text[0]));
                                    }else{
                                        $upper_title = trans('website.W0735');
                                    }

                                    if(!empty($banner_text[1])){
                                        $lower_title = trans('website.'.trim($banner_text[1]));
                                    }else{
                                        $lower_title = trans('website.W0736');
                                    }
                                @endphp
                                <span class="tile-image-gradient">{{$upper_title}}<br><small>{{$lower_title}}</small></span>
                            @endif
                        </li>
                        <li>
                            <span class="tile-image" style="background-image:url({{asset('uploads/banner/'.$banner['home'][2]->banner_image)}});"></span>
                        </li>
                        <li class="big-tiles">
                            <span class="tile-image" style="background-image:url({{asset('uploads/banner/'.$banner['home'][3]->banner_image)}});"></span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>   
</div>
<div>
    <div class="container">
        <ul class="options-listings">
            <li>
                <a href="javascript:void(0);">
                    <span class="options-image">
                        <img src="{{ asset('images/about-icon_001.png') }}" alt="image" />
                    </span>
                    <h4><span class="option-number">1 </span>Search talent</h4>
                    <p>Post a Job, define the scope and connect with the right talent.</p>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);">
                    <span class="options-image">
                        <img src="{{ asset('images/about-icon_002.png') }}" alt="image" />
                    </span>
                    <h4><span class="option-number">2 </span>Hire talent</h4>
                    <p>Browse profiles, reviews and proposals. Then interview to select your favourite.</p>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);">
                    <span class="options-image">
                        <img src="{{ asset('images/about-icon_004.png') }}" alt="image" />
                    </span>
                    <h4><span class="option-number">3 </span>Work</h4>
                    <p>Leverage Crowbar platform to chat, share files and collaborate on desktop or mobile app.</p>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);">
                    <span class="options-image">
                        <img src="{{ asset('images/about-icon_003.png') }}" alt="image" />
                    </span>
                    <h4><span class="option-number">4 </span>Pay</h4>
                    <p>Only pay for work you authorize. All payments are done through Crowbar escrow account.</p>
                </a>
            </li>
        </ul>
    </div>
</div>
@includeIf('front.includes.how-it-works') 
@push('inlinescript')
<style>.swal2-container .select2{display:none;}</style>
    <script type="text/javascript">

            $('#disabled-signup').click(function(){
                var isMobile = window.orientation > -1;
                isMobile = isMobile ? 'Mobile' : 'Not mobile';
                if(isMobile == 'Mobile'){
                    window.location.href='{{url('/tiasignup')}}';
                }
            });


        $('[name="password"]').hidePassword(true);
        $(document).ready(function(){
            @if(old('type') == "employer")
                $('[data-request="show-target"]').trigger('change');
            @endif
        });
        $(document).on('change','[data-request="show-target"]',function(){
            var $this           = $(this);
            var $form_action    = $this.data('form_action');
            var $target         = $this.data('target');
            var $show           = $this.data('show');
            var $form_role      = $this.data('form_role');
            var $value          = $this.val();

            // if($show == true){
            //     $($target).show();
            //     // $this.closest('[role="signup"]').find('.signup-Options').hide();
            //     $('.tile-registeration-form .submit-form-btn button').css('margin-top','26px');
            // }else{
            //     $($target).hide();
            //     // $this.closest('[role="signup"]').find('.signup-Options').show();
            //     $('.tile-registeration-form .submit-form-btn button').css('margin-top','35px');
            // }
            
            $($form_role).attr('action',$form_action);
        });
        var $message = '';
        
        @if(!empty($alert))
            var $message = '{{str_replace("×","",strip_tags($alert))}}';
        @elseif(!empty($errors->has('alert')))
            var $message = '{{str_replace("×","",strip_tags($errors->has('alert')))}}';
        @elseif(\Session::has('alert'))
            var $message = '{{str_replace("×","",strip_tags(\Session::get('alert')))}}';
        @endif

        @if(\Session::has('select_type'))
            setTimeout(function(){
                $('[data-request="ajax-modal"]').trigger('click');
            },1500);
        @endif

        if($message){
            swal({
                title: $alert_message_text,
                html: $message,
                showLoaderOnConfirm: false,
                showCancelButton: false,
                showCloseButton: false,
                allowEscapeKey: false,
                allowOutsideClick:false,
                customClass: 'swal-custom-class',
                confirmButtonText: $close_botton_text,
                cancelButtonText: $no_thanks_botton_text,
                preConfirm: function (res) {
                    return new Promise(function (resolve, reject) {
                        if (res === true) {
                            resolve();              
                        }
                    })
                }
            }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
        }
    </script>
@endpush
