<div class="modal-dialog" role="document">
    <div class="modal-content signup-up-popup text-center">
        <form class="form-horizontal" role="invitetocrowbar" action="{{url('talent/accept-reject-transfer-save')}}" method="post" accept-charset="utf-8">
            {{-- <div class="login-inner-wrapper login-section-popup clearfix">
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="login-type-radio">
                        	<div class="form-group row text-center top-space">
                                <div class="invite-circle-radio-list">
                                    <div class="invite-member-availability">
                                        <input type="password" name="password" placeholder="{{trans('website.W0979')}}"  value="">
                                        <input type="hidden" name="user_id" value="{{$user_id}}">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="member_modal_btn addnotetext">
                                <a class="greybutton-line" data-dismiss="modal">{{trans('website.W0355')}}</a>
                                <button type="button" class="button" data-request="ajax-submit" data-target="[role='inviteMember']">{{trans('website.W0974')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}{{-- 
            <div class="button-group  clearfix" align="center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-btn-set form-top-padding">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <button type="button" id="proceed_user_type" class="button" value="Submit">Proceed</button>
                            <button type="button" class="button" value="Proceed" data-request="ajax-submit" data-target="[role=&quot;invitetocrowbar&quot;]">{{trans('website.W0229')}}</button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-12 col-sm-12 col-xs-12">
                {{-- <div class="talent_icon text-center">
                    <img src="{{asset('images/add-note.png')}}" />
                </div>  --}}           
                <div class="col-sm-12 text-center member_modal_text top-space">
                    <span class="hire-title"><h5>{{trans('website.W0986')}}</h5></span>
                </div>
                {{-- <div class="form-group row text-center top-space">
                    <div class="invite-circle-radio-list">
                        <div class="invite-member-availability">
                            <input type="password" name="password" placeholder="{{trans('website.W0979')}}"  value="">
                            <input type="hidden" name="user_id" value="{{$user_id}}">
                        </div>
                    </div>
                </div> --}}
                <div class="clearfix"></div>
                <div class="member_modal_btn addnotetext">
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" name="confirmation" value="" id="setvalue">
                    <button type="button" class="greybutton-line set" data-request="ajax-submit" data-target="[role='invitetocrowbar']" data-id="reject">{{trans('website.W0220')}}</button>
                    <button type="button" class="button set" data-request="ajax-submit" data-target="[role='invitetocrowbar']" data-id="accept">{{trans('website.W0221')}}</button>
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
    $('.set').on('click',function(){
        $('#setvalue').val($(this).attr('data-id'));
    });
</script>