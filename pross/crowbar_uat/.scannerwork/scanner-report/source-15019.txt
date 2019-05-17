@extends('layouts.front.main')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
        <link href="{{ asset('css/easy-responsive-tabs.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.nstSlider.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery-ui.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/easyResponsiveTabs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery.nstSlider.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{$title}}</h4>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
                <div class="" id="temp-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			        <form class="form-horizontal" role="existingjob" action="{{url('temp-data')}}" method="post" accept-charset="utf-8">
			           
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
			                            
			                                                     
			                </form>
			                    <div class="clearfix"></div>
			                </div>
			            </div>
			        </form>
			    </div>
            </div>
        </div>
    @endsection

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

    @push('inlinescript')
        <script type="text/javascript">$('[name="password"]').hidePassword(true); $('[name="crowbar_password"]').hidePassword(true);</script>
    @endpush