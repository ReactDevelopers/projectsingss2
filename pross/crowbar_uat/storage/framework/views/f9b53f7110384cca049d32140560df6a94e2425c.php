<section class="community-members">
	<div class="clearfix">
	    <div>
            <div class="member_circle_fillter">               
                <div id="invite_member" style="display:none;">
                    <a id="invite_to_cb" data-target="#add-member" data-request="ajax-modal-cb-invite" data-url="<?php echo e(url(sprintf('%s/invite-to-crowbar',TALENT_ROLE_TYPE))); ?>" href="javascript:void(0);">Not a member? Invite.</a>
                </div>
                <div class="checkbox checkbox-big">                
                    <input type="checkbox" id="my_circle" name="my_circle" value="1">
                    <label for="my_circle"><span class="check"></span><?php echo e(trans('website.W0922')); ?></label>
                </div>
	    	</div>
	        <div class="no-table datatable-listing">
	            <?php echo $html->table();; ?>

	        </div>   
	    </div>
	</div>
</section>
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">
        var value = '';
        $(document).on('change','#my_circle',function(){
            if($(this).prop('checked')){
                value = "yes";
            }else{
                value = "no";
            }
            LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                data.circle = value;
            }); 
            window.LaravelDataTables.dataTableBuilder.draw();
        });
        $(document).ajaxStop(function($response,$data) {
            var $totalproposal = $('#dataTableBuilder').text().split(" ");
            var x = $("[type='search']").val();
            if(x){
                $totalproposal[5] == undefined ? $('#invite_member').show() : $('#invite_member').hide() ;
            }
        });
    </script>
<?php $__env->stopPush(); ?>