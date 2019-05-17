 <section class="invite-section">
    <div class="container-fluid">
        <div class="send-invitation-wrapper">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <div class="invite-heading">
                        <h3>Invite User</h3>
                    </div>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <div class="searh-wrappers text-right">
                        <ul>
                            <li>
                                <input type="text" class="form-control" id="name" placeholder="Invite user by user name">
                                <span class="help-block" id="name-error"></span>
                            </li>
                            <li>
                                <input type="email" class="form-control" id="email" placeholder="Invite user by email">
                                <span class="help-block" id="email-error"></span>
                            </li>
                            <li>
                                <button class="button" id="addTalent">Add</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="invitation-block">
            <div class="row" id="selected-talent">
            	<?php if(count($invited_user) > 0): ?>
	            	<?php $__currentLoopData = $invited_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
	            		<div class="col-md-4 col-sm-4 col-xs-12">
		                    <div class="white-invitation-box">
		                        <h3><?php echo e($value['send_to_name']); ?></h3>
		                        <span><?php echo e($value['send_to_email']); ?></span>
		                        <button class="cross-link removeDiv" data-id="<?php echo e($value['id_connect']); ?>"></button>
		                    </div>
		                </div>
	            	<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
	            <?php else: ?>
	            	<div class="col-md-4 col-sm-4 col-xs-12">
	            		<h5 >No records found.</h5>
	            	</div>
            	<?php endif; ?>
            </div>
        </div>
    	<form role="send-invite" id="hiddenTalentInput" action="<?php echo e(url('talent/talent-connect/send-mail')); ?>" method="POST">
			<?php echo e(csrf_field()); ?>

			<div class="send-invitation-button">
				<?php if(count($invited_user) > 0): ?>
					<?php $__currentLoopData = $invited_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
						<div id="talent_<?php echo e($value['id_connect']); ?>">
							<input type="hidden" value="<?php echo e($value['id_connect']); ?>" name="send_to[]" >
						</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
				<?php endif; ?>
				<input data-request="ajax-submit" data-target='[role="send-invite"]' type="button" class="button" value="Send Invitiation" />
			</div>
    	</form>
    </div>
</section>
<section class="added-membes-section">
	<div class="container-fluid">
		<div class="invite-heading">
			<h3>Added members</h3>
		</div>
		<div class="row">
			<?php if(count($connected_user) > 0): ?>
				<?php $__currentLoopData = $connected_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="members-block">
    						<form role="unlink-talent"  action="<?php echo e(url('talent/talent-connect/unlink')); ?>" method="POST">
								<?php echo e(csrf_field()); ?>

								<span class="member-image">
										<img src="<?php echo e($value->user->profile_url); ?>">
								</span>
								<div class="members-content">
									<h3 class="member-name"><?php echo e($value->user->name); ?></h3>
									<label class="member-mail"><?php echo e($value->user->email); ?></label>
									<input type="hidden" name="user_id" value="<?php echo e($value->id_user); ?>">
									<input data-request="confirm-ajax-submit2" data-title="Confirm" data-ask="Do you really want to unlink this user ?" data-target='[role="unlink-talent"]'  type="button" class="chain-icon"  />
									<?php if($value->user->notice_expired!=null): ?>
										<p class="expired-class">Notice Expire On <?php echo e(date('d-M-Y',strtotime($value->user->notice_expired))); ?></p>
										<a  class="btn btn-default" href="<?php echo e(url('talent/disconnected-job-list/'.___encrypt($value->user->id_user))); ?>">Transfer Job</a>
									<?php endif; ?>
									
								</div>
							</form>
						</div>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
			<?php else: ?>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<h5 class="member-name">No Records Found</h5>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
	
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript">
    	var i = 0;
    	$('#addTalent').on('click', function(e) {
    		var err 		= 0;
			var name 		= $('#name').val();
			var email 		= $('#email').val();
			var verifyEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

			if(name == ''){
				$('#name-error').html('Please enter user name.');
				err++;
			}else{
				$('#name-error').html('');
			}
			if(email == ''){
            	$('#email-error').html('Please enter email.');
				err++;
			}else if(verifyEmail.test(String(email).toLowerCase()) != true){
            	$('#email-error').html('Enter valid email.');
    			err++;
			}else if(email == '<?php echo e(\Auth::User()->email); ?>'){
				$('#email-error').html("You can't enter your email.");
    			err++;
			}else{
            	$('#email-error').html('');
			}

			if($('#hiddenTalentInput').find('.send_to_email').length > 0){
	    		$('.send_to_email').each(function(){
	    			if($(this).val() == email){
	    				$('#email-error').html('This email already added in the list.');
						err++;
	    			}
	    		});
			}

			if(err > 0){
				return false;
			}else{
				$.ajax({
					url: '<?php echo e(url(TALENT_ROLE_TYPE.'/talent-connect/store')); ?>', 
					type: 'post', 
					data: {'name':name,'email':email}, 
					success: function($response){
						if($response.status==false){
							if($response.data.name){
								$('#name-error').html($response.data.name[0]);
							}else{
								$('#name-error').html('');
							}
							if($response.data.email){
				            	$('#email-error').html($response.data.email[0]);
							}else{
				            	$('#email-error').html('');
							}
						}else if($response.status==true){
							var talent_data = $response.data;
							$('#name').val('');
							$('#email').val('');
							$('#selected-talent').find('h5').parent().remove();
				    		$('#selected-talent').append('<div class="col-md-4 col-sm-4 col-xs-12">'+
										                    '<div class="white-invitation-box">'+
										                        '<h3>'+talent_data.send_to_name+'</h3>'+
										                        '<span>'+talent_data.send_to_email+'</span>'+
										                        '<button class="cross-link removeDiv" data-id="'+talent_data.id_connect+'"></button>'+
										                    '</div>'+
										                '</div>');
				    		$('#hiddenTalentInput').append('<div id="talent_'+talent_data.id_connect+'">'+
				    		 									'<input type="hidden" value="'+talent_data.id_connect+'" name="send_to[]" >'+
				    		 								'</div>');
						}
					}
				});
			}
		});

		$(document).on('click','.removeDiv',function(){
    		removeTalentSection($(this));
    	});
		
		function removeTalentSection($this){
			var talent_id = $this.data('id')
			$.ajax({
				url: '<?php echo e(url(TALENT_ROLE_TYPE.'/talent-connect/remove/')); ?>/'+talent_id, 
				type: 'post', 
				success: function($response){
					$this.parent().parent().remove();
					$('#talent_'+talent_id).remove();
				}
			});

		}
	</script>
<?php $__env->stopPush(); ?>


