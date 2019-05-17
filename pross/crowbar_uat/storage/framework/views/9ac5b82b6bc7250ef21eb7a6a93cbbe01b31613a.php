<div class="modal-dialog" role="document">
    <div class="modal-content signup-up-popup text-center">
        <form class="form-horizontal" role="invitetocrowbar" action="<?php echo e(url('talent/accept-reject-transfer-save')); ?>" method="post" accept-charset="utf-8">
            
            <div class="col-md-12 col-sm-12 col-xs-12">
                           
                <div class="col-sm-12 text-center member_modal_text top-space">
                    <span class="hire-title"><h5><?php echo e(trans('website.W0986')); ?></h5></span>
                </div>
                
                <div class="clearfix"></div>
                <div class="member_modal_btn addnotetext">
                    <input type="hidden" name="id" value="<?php echo e($id); ?>">
                    <input type="hidden" name="confirmation" value="" id="setvalue">
                    <button type="button" class="greybutton-line set" data-request="ajax-submit" data-target="[role='invitetocrowbar']" data-id="reject"><?php echo e(trans('website.W0220')); ?></button>
                    <button type="button" class="button set" data-request="ajax-submit" data-target="[role='invitetocrowbar']" data-id="accept"><?php echo e(trans('website.W0221')); ?></button>
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