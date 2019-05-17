<?php $__env->startSection('content'); ?>
	<div class="contentWrapper job-details-section">
	    <div class="container">
			<h4 class="form-heading blue-text heading-inbox">
				<?php echo e(trans('website.W0556')); ?>

				<span class="heading-count" data-target="chat-count"></span>
				<span id="list-toggle-btn" class="chat-list-toggle"><?php echo e(trans('website.W0557')); ?></span>
			</h4>
	    	<div id="chat-body" class="row">
				<div class="chat-holder" style="display: none;">
					<div class="col-md-3 col-sm-4 col-xs-12 names-holder" id="chat-left-box">
						<div class="chat-list-wrapper">
							<div class="search-box">
								<div class="afterlogin-searchbox">
						            <div class="form-group">
						                <input type="text" name="search-friend" class="form-control" placeholder="Search for people">
						                <button type="submit" class="btn searchBtn">Search</button>
						            </div>
						        </div>
					        </div>
							<ul class="names-list"></ul>
						</div>
					</div>
					<?php if ($__env->exists('chat.chatbox')) echo $__env->make('chat.chatbox', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				</div>
				<div class="no-chat-connection">
					<?php if ($__env->exists('chat.placeholder')) echo $__env->make('chat.placeholder', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				</div>
			</div>
	    </div>
	</div>
	<div class="modal fade upload-modal-box" id="report-abuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <h3 class="modal-title bottom-margin-10px"><?php echo e(trans('website.W0350')); ?></h3>
            <div class="modal-body bg-white">
                <div>
                	<p><b data-target="reported-user"></b></p>
	                <p class="heading-grey"><?php echo sprintf(trans('website.W0801'),url('page/terms-and-conditions')); ?></p>
	                <br>
	                <form role="report-abuse" action="<?php echo e(url(sprintf('%s/report-abuse',$user['type']))); ?>" method="POST" accept-charset="utf-8">
	                    <?php echo e(csrf_field()); ?>

	                    <div class="form-group">
	                        <textarea rows="10" class="form-control" name="reason" placeholder="<?php echo e(trans('website.W0354')); ?>"></textarea>
	                    	<input type="hidden" name="message">
	                    </div>

	                    <input type="hidden" name="receiver_id" data-target="receiver-id">
	                </form>
                </div>
            </div>
            <div class="report-modal-footer">
	            <div class="button-group">
                    <button type="button" class="button-line" data-dismiss="modal"><?php echo e(trans('website.W0355')); ?></button>
                    <button type="button" style="padding: 10px 20px;" class="button" data-request="report-abuse"><?php echo e(trans('website.W0350')); ?></button>
	            </div>
            </div>
        </div>
      </div>
    </div><!-- /.Modal Window for Upload -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/jquery.mCustomScrollbar.min.css')); ?>" media="all" type="text/css" />
    <script src="<?php echo e(asset('js/jquery.mCustomScrollbar.min.js')); ?>" type="text/javascript"></script>
	<script>
		<?php if(!empty($request['receiver_id'])): ?>
			writeCookie('current_chat_window',"<?php echo e(___decrypt($request['receiver_id'])); ?>");
		<?php endif; ?>
		var current_time    = moment.tz(new Date(), "<?php echo e(date_default_timezone_get()); ?>");
		var timezone 		= current_time.clone().tz("<?php echo e(___current_timezone()); ?>");
		var socket						= new io.connect(
			'<?php echo env('SOCKET_CONNECTION_URL'); ?>:<?php echo env('SOCKET_CONNECTION_POST'); ?>', {
			'reconnection': true,
			'reconnectionDelay': 2000,
			'reconnectionDelayMax' : 5000,
			'secure':false
		});

		var chat_save_url 				= '<?php echo e(url(sprintf("%s/chat-save","chat"))); ?>';
		var chat_history_url			= '<?php echo e(url(sprintf("%s/chat-history","chat"))); ?>';
		var chat_users_list_url 		= '<?php echo e(url(sprintf("%s/chat-list","chat"))); ?>';
		var sender_details				= <?php echo json_encode(['sender' => $user['sender'], 'sender_picture' => $user['sender_picture'], 'type' => $user['type'], 'id_user' => $user['id_user'], 'sender_id' => $user['sender_id']]); ?>;
		var chat_left_box				= '#chat-left-box';
		var chat_right_box				= '#chat-right-box';
		
		var message_box 				= 'ul.messages';
		var profile_text 				= '<?php echo e(trans("general.M0268")); ?>';
		var message_box_text 			= '<?php echo e(trans("general.M0269")); ?>';
		var chat_accept_button_text 	= '<?php echo e(trans("general.M0272")); ?>';
		var chat_reject_button_text 	= '<?php echo e(trans("general.M0273")); ?>';
		var chat_request_postfix 		= '<?php echo e(trans("general.M0274")); ?>';
		var chat_request_prefix 		= '<?php echo e(trans("general.M0275")); ?>';
		var chat_not_available 			= '<?php echo e(trans("general.M0276")); ?>';
		var sender_tag 					= '<?php echo e(trans("general.M0277")); ?>';
		var new_message_tag 			= '<?php echo e(trans("general.M0278")); ?>';
		var connecting_text 			= '<?php echo e(trans("general.M0279")); ?>';
		var report_abuse_text 			= '<?php echo e(trans("website.W0350")); ?>';
		var report_abuse_company_text 	= '<?php echo e(trans("website.W0802")); ?>';
		var delete_all_text 			= '<?php echo e(trans("website.W0351")); ?>';
		var terminate_chat_text 		= '<?php echo e(trans("website.terminate_chat")); ?>';
		var reported_abuse_text 		= '<?php echo e(trans("website.W0352")); ?>';
		var image_text 					= '<?php echo e(trans("website.W0423")); ?>';
		var support_chat_user_id 		= '<?php echo e(SUPPORT_CHAT_USER_ID); ?>';
		var help_desk_tagline 			= '<?php echo e(trans("website.W0425")); ?>';
		var chat_box_title 				= '<?php echo e(trans("website.W0434")); ?>';
		var terminate_message_text 		= '<?php echo e(trans("website.terminate_message")); ?>';
		var delete_message_text 		= '<?php echo e(trans("website.W0620")); ?>';
		var attachement_text       		= '<?php echo e(trans("website.W0621")); ?>';
		var job_title_text       		= '<?php echo e(trans("website.jobid")); ?>';

		<?php if($user['type'] == EMPLOYER_ROLE_TYPE): ?>
		var no_chat_list 			= "<?php echo sprintf(NO_CHAT_CONNECTION_TEMPLATE,trans('website.W0559'),trans('website.W0560'),url(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE)),trans('website.W0558'),trans('website.W0342')); ?>";
		<?php elseif($user['type'] == TALENT_ROLE_TYPE): ?>
		var no_chat_list 			= "<?php echo sprintf(NO_CHAT_CONNECTION_TEMPLATE,trans('website.W0559'),trans('website.W0747'),url(sprintf('%s/find-jobs',TALENT_ROLE_TYPE)),trans('website.W0500'),trans('website.W0342')); ?>";
		<?php else: ?>
		var no_chat_list 			= '<?php echo e(trans("website.W0297")); ?>';
		<?php endif; ?>


		var chat = new chat({
			'socket'					: socket,
			'chat_box_title'			: chat_box_title,
			'chat_save_url'				: chat_save_url,
			'sender_details'			: sender_details,
			'chat_left_box'				: chat_left_box,
			'chat_right_box'			: chat_right_box,
			'profile_text'				: profile_text,
			'message_box'				: message_box,
			'message_box_text'			: message_box_text,
			'chat_accept_button_text'	: chat_accept_button_text,
			'chat_reject_button_text'	: chat_reject_button_text,
			'chat_request_postfix'		: chat_request_postfix,
			'chat_request_prefix'		: chat_request_prefix,
			'chat_not_available'		: chat_not_available,
			'sender_tag'				: sender_tag,
			'new_message_tag'			: new_message_tag,
			'connecting_text'			: connecting_text,
			'no_chat_list'				: no_chat_list,
			'report_abuse_text'			: report_abuse_text,
			'report_abuse_company_text'	: report_abuse_company_text,
			'delete_all_text'			: delete_all_text,
			'terminate_chat_text'		: terminate_chat_text,
			'reported_abuse_text'		: reported_abuse_text,
			'image_text'				: image_text,
			'timezone'					: timezone,
			'support_chat_user_id'		: support_chat_user_id,
			'help_desk_tagline'			: help_desk_tagline,
			'attachement_text'			: attachement_text,
			'job_title_text'			: job_title_text,
			'terminate_message_text'	: terminate_message_text,
			'delete_message_text'		: delete_message_text
		});

		/* INITIATING CHAT APPLICATION */
		chat.initiate();
	</script>
<?php $__env->stopPush(); ?>