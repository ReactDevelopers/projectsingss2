<div class="modal-dialog member_modal_style invite-msg-modal" role="add-member">
    <div class="modal-content">
        <button type="button" class="button close_modal" data-dismiss="modal"><img src="<?php echo e(asset('images/close-me.png')); ?>" /></button>
        <div >
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="<?php echo e(asset('images/key-lock.png')); ?>" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text top-space">
                    <span class="hire-title hire-text"><?php echo e(trans('website.W0978')); ?></span>
                </div>
                <form class="form-horizontal" role="inviteMember" action="<?php echo e(url(sprintf('%s/accept-transfer-ownership/process/',TALENT_ROLE_TYPE))); ?>" method="POST" accept-charset="utf-8"  onkeypress="return event.keyCode != 13">
                <div class="form-group row text-center top-space">
                    <div class="invite-circle-radio-list col-md-12 col-sm-12">
                        <div class="invite-member-availability search-box-availability">
                            <input type="password" class="form-control" name="old_password" placeholder="<?php echo e(trans('website.W0979')); ?>"  value="">
                        </div>
                    </div>
                </div>
                    <div class="clearfix"></div>
                    <div class="member_modal_btn addnotetext wrapper-button">
                        <a class="greybutton-line" data-dismiss="modal"><?php echo e(trans('website.W0355')); ?></a>
                        <button type="button" class="button" data-request="ajax-submit" data-target="[role='inviteMember']"><?php echo e(trans('website.W0974')); ?></button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
s