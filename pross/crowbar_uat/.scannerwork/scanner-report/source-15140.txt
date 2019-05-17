<div class="modal-dialog member_modal_style" role="add-member">
    <div class="modal-content">
        <div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="{{asset('images/invite-to-cb.png')}}" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text">
                    <span class="invite-to-cb">Invite people to join Crowbar</span>
                </div>
                <div class="clearfix"></div>
                <div class="member_modal_btn addnotetext">
                    <form class="form-horizontal" role="invitetocrowbar" action="{{url(sprintf('%s/invite-to-crowbar',TALENT_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
                    	<div class="form-group">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label for="name">Name</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input type="text" class="form-control" name="name" placeholder="Enter Name" value="{{ old('last_name') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <label for="name">Email</label>
                                </div>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input type="text" class="form-control" name="email" id="invite_to_cb_email" placeholder="Enter Email" value="{{ old('email') }}">
                                </div>
                            </div>
                        </div>
                        <a class="greybutton-line" data-dismiss="modal">{{trans('website.W0355')}}</a>
                        <button type="button" class="button" value="Submit" data-request="ajax-submit" data-target="[role=&quot;invitetocrowbar&quot;]">{{trans('website.W0229')}}</button>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>