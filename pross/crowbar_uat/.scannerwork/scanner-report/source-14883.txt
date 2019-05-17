<form class="form-horizontal" role="existingjob" action="{{url(sprintf('%s/sendmessage?talent_id=%s',EMPLOYER_ROLE_TYPE,$talent_id))}}" method="post" accept-charset="utf-8">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="login-inner-wrapper" style="padding: 35px;">
                <div class="company-section">
	                <div class="form-group">
	                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0929')}}</label>
	                    <div class="col-md-12 col-sm-12 col-xs-12">
	                        <textarea name="send_message" class="form-control" style="height:60px;">{{trans('website.W0836')}}</textarea>
	                    </div>
	                </div>
	            </div>
            </div>
            <div class="button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-btn-set">                                    
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <button type="button" class="button" value="Submit" data-request="ajax-submit" data-target="[role=&quot;existingjob&quot;]">{{trans('website.W0013')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</form>