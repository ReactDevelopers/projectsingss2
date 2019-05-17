<section class="community-members">
	<?php if(!empty($request_list)): ?>
		<h6>My network</h6>
		<ul class="allRequests">
			<?php $__currentLoopData = $request_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
				<li>
					<div class="request-content-box clearfix">
						<div class="profile-img-cell">
							<span class="requestprofile-img">
						        <img src="<?php echo e($val['picture']); ?>">							
							</span>
						</div>
				     	<div class="requestprofile-details">
				         	<div class="contentbox-header-title">
				            	<h3 class="member-detail-link"><a href="<?php echo e(url(sprintf("%s/view/%s",TALENT_ROLE_TYPE,___encrypt($val['id_user'])))); ?>"><?php echo e($val['name']); ?></a></h3>
				            	<?php if(!empty($val['industry_name']) && !empty($val['country'])): ?>
				            	<span class="company-name"><?php echo e($val['industry_name'].' ('.$val['country'].')'); ?></span>
				            	<?php endif; ?>
				            	<span class="company-name">Member since <?php echo e(date('d F Y',strtotime($val['created']))); ?></span>
				            	<?php if(!empty($val['note'])): ?>
				            		<p>Note- <?php echo e($val['note']); ?></p>
				            	<?php endif; ?>
				         	</div>
				      	</div>
					    <div class="requestprofile-actions">
					    	<ul class="requestprofile-actionList">
					    		<li>
									<button type="button" data-request="accept-member-req" data-value="ignore" name="later" data-url="<?php echo e(url(sprintf('%s/acceptmember?member_id=%s&user_id=%s&status=%s',TALENT_ROLE_TYPE,$val['member_id'],$val['user_id'],'rejected'))); ?>" class="greybutton-line">Ignore</button>					    			
					    		</li>
					    		<li>
									<button type="button" data-request="accept-member-req" data-value="accept" name="save" class="button" data-url="<?php echo e(url(sprintf('%s/acceptmember?member_id=%s&user_id=%s&status=%s',TALENT_ROLE_TYPE,$val['member_id'],$val['user_id'],'accepted'))); ?>">Accept</button>
					    		</li>
					    			
					    	</ul>
					    </div>
					</div>
				</li>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
		</ul>
	<?php endif; ?>
	<div class="">
		<h6>People you may know</h6>
		<div class="no-table datatable-listing">
	        <?php echo $html->table();; ?>

	    </div>
	</div>
</section>
<?php $__env->startPush('inlinescript'); ?>
	<script type="text/javascript" src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    <script type="text/javascript">
		$(document).on('click','[data-request="accept-member-req"]',function(e){
		    $('#popup').show();
		    var $this       = $(this);
		    var $value      = $this.data('value'); 
		    var $url        = $this.data('url');

		    $.ajax({
		        url: $url, 
		        type: 'get', 
		        success: function($response){
		            $('#popup').hide();

		            if ($response.status === true){
		                if(!$response.nomessage){
	                        swal({
	                            title: $alert_message_text,
	                            html: $response.message,
	                            showLoaderOnConfirm: false,
	                            showCancelButton: false,
	                            showCloseButton: false,
	                            allowEscapeKey: false,
	                            allowOutsideClick:false,
	                            customClass: 'swal-custom-class',
	                            confirmButtonText: $close_botton_text,
	                            cancelButtonText: $cancel_botton_text,
	                            preConfirm: function (res) {
	                                return new Promise(function (resolve, reject) {
	                                    if (res === true) {
	                                        if($response.redirect){
	                                            window.location = $response.redirect;
	                                        }              
	                                    }
	                                    resolve();
	                                })
	                            }
	                        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
                    	}
		            }

		        },error: function(error){
		            
		        }
		    }); 
		});
    </script>
<?php $__env->stopPush(); ?>