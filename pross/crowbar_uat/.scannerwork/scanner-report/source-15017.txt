    @extends('layouts.front.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset('/js/custom.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="testimonials">
            <div class="container">
                <div class="text-center">
                    <h4 class="light-heading">How others leverage hiring on Crowbar</h4>
                </div>
                <div class="testimonialCarousel slick-slider">
                    <div class="item">
                        <div class="slide-content">
                            <div class="customerImage">
                                <img src="images/customer1.jpg" class="img-responsive" alt="">
                            </div>
                            <div class="testimonialContent">
                                <p>I have found quality assignments on Crowbar and I enjoy the flexible hours which helps me spend more time with my young family.</p>
                                <h5>Joyce Tan</h5>
                                <span class="italic-text">Lawyer</span>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="slide-content">
                            <div class="customerImage">
                                <img src="images/customer2.jpg" class="img-responsive" alt="">
                            </div>
                            <div class="testimonialContent">
                                <p>Being a startup we are always working on tight deadlines and constantly need help of expert professionals – lawyers, accountants, digital marketeers. Crowbar helps us ramp up seamlessly by hiring the right person at the right time and at the right cost.</p>
                                <h5>Marianne Smith</h5>
                                <span class="italic-text">Startup Co-Founder</span>
                            </div>                                
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- @if(!empty($testimonials->count()))
            <div class="testimonials">
                <div class="container">
                    <div class="text-center">
                        <h4 class="light-heading">How others leverage hiring on Crowbar</h4>
                    </div>
                    <div class="testimonialCarousel slick-slider">
                        @foreach($testimonials as $val)
                            <div class="item">
                                <div class="slide-content">
                                    <div class="customerImage">
                                        <img src="{{asset($val->image)}}" class="img-responsive" alt="">
                                    </div>
                                    <div class="testimonialContent">
                                        <p>{{$val->description}}</p>
                                        <h5>{{$val->name}}n</h5>
                                        <span class="italic-text">{{$val->profession}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach                        
                    </div>
                </div>
            </div>
        @endif --}}

        @if(!empty($tagged_industries->count()))
            <div class="contentWrapper">
                <div class="job-options">                
                    <div class="container-fluid">
                        <h4 class="light-heading text-center white-text">Leverage opportunities for various industries</h4>
                        <ul class="job-options-list">
                            @foreach($tagged_industries as $item)
                                <li>
                                    <a href="search-job?industry={{$item->name}}">
                                        {{-- <span><img src="{{ asset($item->image) }}" alt="image"></span> --}}
                                        <span><img src="{{ !empty($item->image)? asset($item->image):asset('images/ge-icon1.png') }}" alt="image"></span>
                                        <h5>{{ $item->name }}</h5>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    @endsection

    @section('temp-popup')
        <div class="modal fade upload-modal-box add-payment-cards" id="temp-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <form class="form-horizontal" role="existingjob" action="{{url('temp-data')}}" method="post" accept-charset="utf-8">
                <div class="modal-dialog temp-popup-reg" role="document">
                    <div class="modal-content">
                        <h3 class="form-heading m-b-10px no-padding"></h3>
                        <form method="POST" role="signup" action="" class="form-horizontal login-form" autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{ Request::get('token') }}" name="remember_token" />
                        <div class="form-group">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">
                            Coming soon! Please register your interest below:
                            </label>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">Name</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input name="temp_name" value="" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">Email</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input name="temp_email" value="" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">Company</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input name="temp_company" value="" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">Contact</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input name="temp_contact" value="" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">What types of professionals do you want to hire? (Any 3)</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="custom-dropdown single-tag-selection temp-data-profession">
                                    <select name="temp_professions[]" style="max-width: 400px;" class="form-control" data-request="temp-tags" data-placeholder="Select Professions" multiple="multiple">
                                        {!!___dropdown_options(___cache('industries_name'),sprintf(trans('website.W0060'),trans('website.W0068')),null,false)!!}
                                    </select>
                                    <div class="js-example-tags-container"></div>
                                </div>
                            </div>
                        </div>
                    
                                                        
                        <div class="form-group submit-form-btn text-center">
                            <div class="col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-sm redShedBtn" data-request="ajax-submit" data-target="[role='existingjob']">Submit</button>
                                <button type="button" class="btn btn-sm redShedBtn" data-dismiss="modal">Close</button>
                                
                            </div>
                        </div>                              
                    </form>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade upload-modal-box add-payment-cards" id="temp-me-2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <h3 class="form-heading m-b-10px no-padding"></h3>
                    <div class="form-group">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label" style="text-align: center;">
                        Thanks for your interest!
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label" style="text-align: center;">
                        We will be in touch soon.
                        </label>
                    </div>
                        
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    @endsection    


    @push('inlinescript')
        <script type="text/javascript">
            (function($){
                $(window).on("load",function(){
                    $('.searchbar-wrap').fadeIn(100);
                });
            })(jQuery);

            $(document).ready(function(){
                // $('#disabled-signup').click(function(){
                //     $('#temp-me').modal('show');
                // });

                var isMobile1 = window.orientation > -1;
                isMobile1 = isMobile1 ? 'Mobile' : 'Not mobile';
                if(isMobile1 == 'Not mobile'){
                    console.log("isMobile1----"+isMobile1);
                    setTimeout(function(){ 
                        // $('#temp-me').modal('show');
                    }, 1000);
                }

                if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    // alert('It is mobile');
                }
                else{
                    // alert('It is web');
                }
            });

        </script>
    @endpush
