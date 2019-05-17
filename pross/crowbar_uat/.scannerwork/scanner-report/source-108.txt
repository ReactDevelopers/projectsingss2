<div class="modal-dialog" role="document">
    <div class="modal-content signup-up-popup text-center">
        <form class="form-horizontal" role="invitetocrowbar" action="<?php echo e(url('select-profile-save')); ?>" method="post" accept-charset="utf-8">
            <div class="login-inner-wrapper login-section-popup clearfix">
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="login-type-radio">
                        	
    	                        <div class="grouped-radio">
    	                            <label class="hire-talent">
    	                                <input type="radio" name="type" value="employer" checked="checked" data-request="show-target" data-show="false" data-target="#coupon_code" data-form_action="<?php echo e(url('/signup/employer/process')); ?>" data-form_role='[role="signup"]'>
    	                                <span></span>
    	                            </label>
    	                        </div>
                        	
                        	
    	                        <div class="grouped-radio">
    	                            <label class="get-hired">
    	                                <input type="radio" name="type" value="talent"  data-request="show-target" data-show="true" data-target="#coupon_code" data-form_action="<?php echo e(url('/signup/talent/process')); ?>" data-form_role='[role="signup"]'>
    	                                <span></span>
    	                            </label>
    	                        </div>
                        	
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-group  clearfix" align="center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-btn-set form-top-padding">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            
                            <button type="button" class="button" value="Proceed" data-request="ajax-submit" data-target="[role=&quot;invitetocrowbar&quot;]"><?php echo e(trans('website.W0229')); ?></button>
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