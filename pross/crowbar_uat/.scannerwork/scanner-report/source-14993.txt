<div class="modal-dialog" role="document">
    <div class="modal-content signup-up-popup text-center">
        <form class="form-horizontal" role="invitetocrowbar" action="{{url('select-profile-save')}}" method="post" accept-charset="utf-8">
            <div class="login-inner-wrapper login-section-popup clearfix">
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="login-type-radio">
                        	{{-- Employer Radio --}}
    	                        <div class="grouped-radio">
    	                            <label class="hire-talent">
    	                                <input type="radio" name="type" value="employer" checked="checked" data-request="show-target" data-show="false" data-target="#coupon_code" data-form_action="{{ url('/signup/employer/process') }}" data-form_role='[role="signup"]'>
    	                                <span></span>
    	                            </label>
    	                        </div>
                        	{{-- Employer Radio --}}
                        	{{-- Talent Radio --}}
    	                        <div class="grouped-radio">
    	                            <label class="get-hired">
    	                                <input type="radio" name="type" value="talent"  data-request="show-target" data-show="true" data-target="#coupon_code" data-form_action="{{ url('/signup/talent/process') }}" data-form_role='[role="signup"]'>
    	                                <span></span>
    	                            </label>
    	                        </div>
                        	{{-- Talent Radio --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-group  clearfix" align="center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-btn-set form-top-padding">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            {{-- <button type="button" id="proceed_user_type" class="button" value="Submit">Proceed</button> --}}
                            <button type="button" class="button" value="Proceed" data-request="ajax-submit" data-target="[role=&quot;invitetocrowbar&quot;]">{{trans('website.W0229')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
    </div>
</div>
<script type="text/javascript">
    $('#proceed_user_type').on('click',function(){
    	$('#do_signup').trigger('click');
    });
</script>