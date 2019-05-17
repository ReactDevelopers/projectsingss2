function chat(options){
	var render_user_limit 			= 1;
	var $debug 						= true;
	var $this 						= this;
	var $chat_init 					= false;
	var $loading 					= false;
	var $api_success_response   	= {'status': true,'data': [],"message": "M0000","error": "The records has been retrieved successfully.","error_code": "","status_code": "200"};
			
	this.connected_users 			= [];
	this.online_users 				= [];
	this.offline_users 				= [];

	this.socket 					= options.socket;
	this.chat_save_url 				= options.chat_save_url;
	this.show_online_users 			= (options.show_online_users)?options.show_online_users:false;
	this.sender_details 			= options.sender_details;
	
	this.chat_left_box 				= ($(options.chat_left_box).length > 0)?$(options.chat_left_box):false;
	this.chat_right_box 			= ($(options.chat_right_box).length > 0)?$(options.chat_right_box):false;
	
	this.timezone					= options.timezone;
	this.message_box 				= options.message_box;
	this.profile_text 				= options.profile_text;
	this.message_box_text 			= options.message_box_text;
	this.chat_accept_button_text	= options.chat_accept_button_text;
	this.chat_reject_button_text	= options.chat_reject_button_text;
	this.chat_request_postfix		= options.chat_request_postfix;
	this.chat_request_prefix		= options.chat_request_prefix;
	this.chat_not_available			= options.chat_not_available;
	this.sender_tag					= options.sender_tag;
	this.new_message_tag			= options.new_message_tag;
	this.connecting_text			= options.connecting_text;
	this.no_chat_list				= options.no_chat_list;
	this.report_abuse_text			= options.report_abuse_text;
	this.report_abuse_company_text	= options.report_abuse_company_text;
	this.delete_all_text			= options.delete_all_text;
	this.terminate_chat_text		= options.terminate_chat_text;
	this.reported_abuse_text		= options.reported_abuse_text;
	this.image_text					= options.image_text;
	this.support_chat_user_id		= options.support_chat_user_id;
	this.help_desk_tagline			= options.help_desk_tagline;
	this.terminate_message_text		= options.terminate_message_text;
	this.delete_message_text		= options.delete_message_text;
	this.chat_box_title				= options.chat_box_title;
	this.attachement_text			= options.attachement_text;
	this.job_title_text				= options.job_title_text;
	
	this.init_chat_box = function($group_id,$receiver_id,$receiver,$receiver_email,$receiver_image,$receiver_profile_link,$receiver_profile_text,$message_box_text,$is_receiver_online,$request_status){
		var $this = this;
		var $html = "";

		$html += '<div class="chat-box" data-chat-box="'+$group_id+'">';
			$html += '<div class="chat-header">';
				$html += '<div class="hidden-backend">';
					$html += '<span class="chat-image">';
						$html += '<img src="'+$receiver_image+'" width="80" />';
					$html += '</span>';
					$html += '<span class="chat-item">';
						$html += '<span class="chat-title">';
							$html += '<span class="chat-user-name">'+$receiver+'</span>';
							$html += '<span class="chat-current-status '+$is_receiver_online+'"></span><br>';
							$html += '<small class="chat-user-email">'+$receiver_email+'</small>';
							$html += '<div class="clearfix"></div>';
							
							if($receiver_id != this.support_chat_user_id){
								$html += '<span class="chat-profile-link" data-toggle="modal" data-target="#report-abuse" data-receiver-id="'+$receiver_id+'" data-receiver="'+$receiver+'">'+$this.report_abuse_text+'</span>';
								$html += '<span class="chat-text-seperator">&nbsp;|&nbsp;</span>';
								$html += '<span class="chat-profile-link" data-request="delete-all" data-receiver_id="'+$receiver_id+'" data-group_id="'+$group_id+'">'+$this.delete_all_text+'</span>';
								$html += '<span class="chat-text-seperator">&nbsp;|&nbsp;</span>';
								
								if($this.sender_details.type == 'employer'){
									$html += '<span class="chat-profile-link" data-request="terminate-chat" data-receiver_id="'+$receiver_id+'" data-group_id="'+$group_id+'">'+$this.terminate_chat_text+'</span>';
								}
								
								if($receiver_profile_link){
									$html += '<a target="_blank" class="chat-profile-link" href="'+$receiver_profile_link+'">'+$receiver_profile_text+'</a>';
								}
							}else{
								$html += '<span class="chat-profile-link">'+this.help_desk_tagline+'</span>';
							}

						$html += '</span>';
					$html += '</span>';
				$html += '</div>';
				$html += '<div class="hidden-frontend">';
					$html += '<span class="chat-header-title pull-left">';
						$html += $this.chat_box_title;
					$html += '</span>';
					$html += '<span class="pull-right">';
						$html += '<span id="list-toggle-btn"><i class="fa fa-bars"></i></span>';
					$html += '</span>';
				$html += '</div>';
			$html += '</div>';
			if($request_status === 'reported'){
				$html += '<div class="request-status-box-wrapper"><ul class="messages" id="messages"></ul></div>';
				$html += '<input type="hidden" name="receiver" value="'+$receiver+'">';
				$html += '<input type="hidden" name="group_id" value="'+$group_id+'">';
				$html += '<input type="hidden" name="receiver_id" value="'+$receiver_id+'">';
				$html += '<input type="hidden" name="sender" value="'+$this.sender_details.sender+'">';
				$html += '<input type="hidden" name="sender_id" value="'+$this.sender_details.sender_id+'">';
				$html += '<input type="hidden" name="sender_picture" value="'+$this.sender_details.sender_picture+'">';
				$html += '<div class="send-message-request">';
					$html += $this.chat_not_available;
				$html += '</div>';
			}else if($request_status == 'accepted'){
				$html += '<div class="request-status-box-wrapper">';
					$html += '<ul class="messages" id="messages"></ul>';
					$html += '<input type="hidden" name="receiver" value="'+$receiver+'">';
					$html += '<input type="hidden" name="group_id" value="'+$group_id+'">';
					$html += '<input type="hidden" name="receiver_id" value="'+$receiver_id+'">';
					$html += '<input type="hidden" name="sender" value="'+$this.sender_details.sender+'">';
					$html += '<input type="hidden" name="sender_id" value="'+$this.sender_details.sender_id+'">';
					$html += '<input type="hidden" name="sender_picture" value="'+$this.sender_details.sender_picture+'">';
					
					$html += '<input type="hidden" name="mcs_top">';
					$html += '<input type="hidden" name="mcs_dragger_top">';
					$html += '<input type="hidden" name="mcs_top_pct">';
					$html += '<input type="hidden" name="mcs_direction">';
					$html += '<input type="hidden" name="mcs_total_scroll_offset">';
					$html += '<input type="hidden" name="mcs_total_scroll_back_offset">';
					
					$html += '<div class="send-message-box">';
						$html += '<textarea resize="false" class="message-box" maxlength="500" data-request="send-message" placeholder="'+$message_box_text+'"></textarea>';
						
						$html += '<button type="button" class="send-message" data-request="send-message-button">';
							$html += 'Send';
						$html += '</button>';

						$html += '<form title="'+$this.attachement_text+'" role="upload" action="'+asset_url+'api/v1/chat/chat-upload-images" method="post" enctype="multipart/form-data">';					
							$html += '<label class="send-message" data-request="send-message-button">';
								$html += '<i class="fa fa-camera" aria-hidden="true"></i>';
								$html += '<input type="file" class="hide" name="file" data-request="upload" data-width="150" data-height="150" data-target="[role=\'upload\']"/>';
							$html += '</label>';
						$html += '</form>';
					$html += '</div>';
				$html += '</div>';
			}else if($this.sender_details.type === 'employer'){
				$html += '<div class="request-status-box-wrapper">';
					$html += '<div class="request-status-box" style="min-height: 420px;">';
						$html += '<div class="request-status-message text-center">'+$receiver+' '+$this.chat_request_postfix+'</div>';
						$html += '<div class="col-md-4 col-xs-4 col-sm-4"></div>';
						$html += '<div class="col-md-4 col-xs-4 col-sm-4">';
							$html += '<div class="row request-status-buttons">';
								$html += '<div class="col-md-6 col-sm-6 top-margin-10px">';
									$html += '<button data-request="reject-chat" class="greybutton-line">'+$this.chat_reject_button_text+'</button>';
								$html += '</div>';
								$html += '<div class="col-md-6 col-sm-6 top-margin-10px">';
									$html += '<button data-request="accept-chat" class="button">'+$this.chat_accept_button_text+'</button>';
								$html += '</div>';
								$html += '<input type="hidden" name="group_id" value="'+$group_id+'">';
								$html += '<input type="hidden" name="receiver_id" value="'+$receiver_id+'">';
								$html += '<input type="hidden" name="sender_id" value="'+$this.sender_details.sender_id+'">';
							$html += '</div>';
						$html += '</div>';
						$html += '<div class="col-md-4 col-xs-4 col-sm-4"></div>';
					$html += '</div>';
					$html += '<div class="send-message-request hide">';
						$html += $this.chat_not_available;
					$html += '</div>';
				$html += '</div>';
			}else{
				$html += '<div class="request-status-box-wrapper">';
					$html += '<div class="request-status-box">';
						$html += '<div class="request-status-message text-center">'+$receiver+' '+$this.chat_request_prefix+'</div>';
					$html += '</div>';
					$html += '<div class="send-message-request">';
						$html += $this.chat_not_available;
					$html += '</div>';
				$html += '</div>';
			}
		$html += '</div>';
		
		$this.chat_right_box.html($html);	
	}

	this.render_user_list_html = function($chat_list_user,$clear){
		var $html 	= '';
		var $this 	= this; 
		var $json 	= [];

		if($clear){
			$this.print({},"CLEARING CHAT LIST");
			
			if($this.chat_left_box.find('ul').find('.mCSB_container').length > 0){
				$this.chat_left_box.find('ul').find('.mCSB_container').html('');
			}else{
				$this.chat_left_box.find('ul').html('');
			}
		}

		if($this.chat_left_box){
			$.each($chat_list_user,function($index,$item){
				var $class = "";

				$this.print($item.receiver_id,"RENDERING USER CHAT LIST");
				$json[$item.receiver_id] = $item;
				
				if($item.unread_messages > 0){
					$class = " unread-list";
				}else{
					$item.unread_messages = "";
				}

				if($item.id_chat_request == readCookie('current_chat_window')){
					$class += " active";
				}
				
				if($('li[data-user-id="'+$item.receiver_id+'"]').length < 1){
					$this.print($item.receiver_id,"CHAT LIST TRAVERSING");
					$html += '<li class="chat-item-list chat-list-'+$item.id_chat_request+$class+'" data-request="init-chat" data-group-id="'+$item.id_chat_request+'" data-user-id="'+$item.receiver_id+'" data-user-picture="'+$item.receiver_picture+'" data-user="'+(($item.receiver_name).trim())+'" data-email="'+$item.receiver_email+'" data-profile-link="'+$item.profile_link+'" data-message-box-text="'+$this.message_box_text+'" data-profile-text="'+$this.profile_text+'" data-request-status="'+$item.request_status+'">';
						$html += '<p href="javascript:void(0);" class="user-item-list-'+$item.timelog+'">';
							$html += '<span class="chat-image">';
								$html += '<img src="'+$item.receiver_picture+'" width="50" />';
								$html += '<span class="chat-bubbles">'+$item.unread_messages+'</span>';
							$html += '</span>';
							$html += '<span class="chat-item">';
								$html += '<span class="chat-title" title="'+$item.receiver_name+'">';
									$html += $item.receiver_name;
									$html += '<span class="chat-current-status '+$item.status+'"></span>';
									$html += '<span class="chat-timestamp">'+$item.ago+'</span>';
								$html += '</span>';
								$html += '<span class="chat-project-title">';
									$html += $this.job_title_text+' - '+$item.project_id;
								$html += '</span>';
								$html += '<span class="chat-line-message">';
									$html += $this.sanatize($item.last_message,'not_nl2br');
								$html += '</span>';
							$html += '</span>';
						$html += '</p>';
					$html += '</li>';
				}else{
					$this.print($item.last_message,"UPDATING LAST MESSAGE");

					if($('[data-chat-box="'+$item.id_chat_request+'"]').length < 1){
						$('[data-group-id="'+$item.id_chat_request+'"]').find('.chat-bubbles').html($item.unread_messages);
						$('[data-group-id="'+$item.id_chat_request+'"]').addClass('unread-list');
					}
				
					$('[data-group-id="'+$item.id_chat_request+'"]').find('p').removeAttr('class');
					$('[data-group-id="'+$item.id_chat_request+'"]').find('p').addClass('user-item-list-'+$item.timelog);
					$('[data-group-id="'+$item.id_chat_request+'"]').find('.chat-line-message').html($this.sanatize($item.last_message,'not_nl2br'));
					$('[data-group-id="'+$item.id_chat_request+'"]').find('.chat-timestamp').html($item.ago);	
				}
			});

			if($this.chat_left_box.find('ul').find('.mCSB_container').length > 0){
				$this.chat_left_box.find('ul').find('.mCSB_container').append($html);
				$this.chat_left_box.find('ul').mCustomScrollbar('update');
			}else{
				$this.chat_left_box.find('ul').append($html);

				setTimeout(function(){
					$this.chat_left_box.find('ul').mCustomScrollbar({
						scrollInertia: 400,
			            advanced: {
			                updateOnContentResize: true
			            },
						callbacks:{
							onScrollStart:function(){ myCallback(this,"#onScrollStart") },
							onScroll:function(){ myCallback(this,"#onScroll") },
							onTotalScroll:function(){ myCallback(this,"#onTotalScroll") },
							onTotalScrollOffset:60,
							onTotalScrollBack:function(){ myCallback(this,"#onTotalScrollBack") },
							onTotalScrollBackOffset:50,
							whileScrolling:function(){ 
								myCallback(this,"#whileScrolling"); 
								$('[name="mcs_top"]').val(this.mcs.top);
								$('[name="mcs_dragger_top"]').val(this.mcs.draggerTop);
								$('[name="mcs_top_pct"]').val(this.mcs.topPct+"%");
								$('[name="mcs_direction"]').val(this.mcs.direction);
								$('[name="mcs_total_scroll_offset"]').val("60");
								$('[name="mcs_total_scroll_back_offset"]').val("50");

								setTimeout(function(){
									$this.print(parseInt($('[name="mcs_top_pct"]').val()),"SCROLL TOP");
									if(parseInt($('[name="mcs_top_pct"]').val()) < 10){
										$this.print({},"SCROLLING FOR LOAD MORE DONE");
										$('[data-request="load-more"]').trigger('click');
									}else{
										$this.print({},"SCROLLING FOR LOAD MORE NOT DONE");
									}
								},2000);
							},
							alwaysTriggerOffsets:false
						}
					});
				},1000);
			}
		}

		this.connected_users = $json;		
	};

	this.render_self_message_chat_list = function($group_id,$receiver_id,$message,$timestamp,$ago){
		if($message.match(/\.(jpeg|jpg|gif|png)$/) != null){
			$message = $this.image_text;
		}
		
		$this.print($message,"RENDERING MY MESSAGE TO CHAT LIST");
		
		$('[data-group-id="'+$group_id+'"]').find('p').removeAttr('class');
		$('[data-group-id="'+$group_id+'"]').find('p').addClass('user-item-list-'+$timestamp);
		$('[data-group-id="'+$group_id+'"]').find('.chat-line-message').html($this.sanatize($message,'not_nl2br'));	
		$('[data-group-id="'+$group_id+'"]').find('.chat-timestamp').html($ago);	

		$this.reorder_chat_list();
	}

	this.update_sender_online_status = function(){
		/* CHECK RECEIVER ONLINE STATUS*/
		$('.chat-list-'+$this.online_users).find('.chat-current-status').addClass('online');

		if($('[data-chat-box="'+$this.online_users+'"]').length > 0){
			$('[data-chat-box="'+$this.online_users+'"]').find('.chat-current-status').addClass('online');
		}
	
		this.reorder_chat_list();
	};

	this.update_sender_offline_status = function(){
		$this = this;
		/* CHECK RECEIVER OFFLINE STATUS*/
		$('.chat-list-'+$this.offline_users).find('.chat-current-status').removeClass('online');
		
		if($('[data-chat-box="'+$this.offline_users+'"]').length > 0){
			$('[data-chat-box="'+$this.offline_users+'"]').find('.chat-current-status').removeClass('online');
		}
	
		setTimeout(function(){
			$this.reorder_chat_list();
		},200);
	};

	/* CHAT INITIATE */
	this.initiate = function(){
		var $this = this;
		
		this.socket.on('connect', function (user) {
			$('.connecting-tag').css({'background':'#6ad215'});

			setTimeout(function(){
				$('.connecting-tag').slideUp(function(){
					$('.connecting-tag').remove();
				});

				$('[data-request="send-message"]').removeAttr('readonly');
			},1000);

			$this.socket.emit('join', $this.sender_details, function($response){
				$this.print($response,"JOIN ACKNOWLEDGE");
				$this.get_chat_list($this.sender_details.sender_id);
			});
		});

		this.socket.on('disconnect', function (user) {
			$this.print({},"DISCONNECTED");
			$this.show_connecting();
		});

		this.socket.on('chat.user.online', function ($users) {
			$this.online_users = $users;
     		$this.update_sender_online_status();
		});

		this.socket.on('chat.user.offline', function ($users) {
     		$this.offline_users = $users;
     		$this.update_sender_offline_status();
		});
		
		this.socket.on('chat.message.marked.readall', function ($response) {
			$('[data-group-id="'+$response.id_chat_request+'"]').removeClass('unread-list');
			$('[data-group-id="'+$response.id_chat_request+'"]').find('.chat-bubbles').html('');
		});

		this.socket.on('chat.message.'+$this.sender_details.sender_id, function($response){
			$this.print($response,"CHAT SAVED");
			$this.render_message($response.data,false,true,'append',false);
			$this.get_chat_list($this.sender_details.sender_id);
			
			if($('[data-chat-box="'+$response.data.group_id+'"]').length > 0){
				$this.socket.emit('chat.message.acknowledge',{'chat_id': $response.data.id_chat,'seen_status': 'read'},function($response){
					$this.print($response,"CHAT MARKED READ");
				});
			}else{
				$this.socket.emit('chat.message.acknowledge',{'chat_id': $response.data.id_chat,'seen_status': 'delivered'},function($response){
					$this.print($response,"CHAT MAKED DELIVERED");
				});
			}
		});
	}

	this.socket.on('chat.message.self.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT SAVED");
		//$this.get_chat_list($this.sender_details.sender_id);
		
		// console.log($this.sender_details.sender_id);
		// console.log($response.data.receiver_id);
		// console.log($response.data.sender_id);
		// if(($('[data-chat-box="'+$response.data.group_id+'"]').length > 0) && ($('[name="sender_id"').val() == $this.sender_details.sender_id)){
		// 	$this.render_message($response.data,false,false,'append',false);
		// }
	});

	this.socket.on('chat.accepted.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT ACCEPT ACKNOWLEDGED");
		
		$this.get_chat_list($this.sender_details.sender_id,true);

		setTimeout(function(){$('.chat-list-'+$response.data.sender_id).trigger('click');},1000);
	});

	this.socket.on('chat.deleted.all.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT DELETE ACKNOWLEDGED");
		$($this.message_box).html("");
	});

	this.socket.on('chat.rejected.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT REJECT ACKNOWLEDGED");
		
		$this.get_chat_list($this.sender_details.sender_id,true);

		setTimeout(function(){$('.chat-item-list:first').trigger('click');},1000);
	});

	this.socket.on('chat.receiver.terminate.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT BOX TERMINATION");
		$this.get_chat_list($this.sender_details.sender_id,true);

		setTimeout(function(){
			if($('.chat-item-list:first').length > 0){
				$('.chat-item-list:first').trigger('click');
			}else{
				window.location = window.location;
			}
		},1000);
	});

	this.socket.on('chat.sender.terminate.'+$this.sender_details.sender_id, function($response){
		$this.print($response,"CHAT BOX TERMINATION");
		$this.get_chat_list($this.sender_details.sender_id,true);

		setTimeout(function(){
			if($('.chat-item-list:first').length > 0){
				$('.chat-item-list:first').trigger('click');
			}else{
				window.location = window.location;
			}
		},1000);
	});

	this.show_connecting = function(){
		$('[data-request="send-message"]').attr('readonly','readonly');

		$($this.message_box).before('<div class="connecting-tag" style="display:none;">'+$this.connecting_text+'</div>')
		$('.connecting-tag').slideDown();
	}

	this.get_chat_list = function($sender_id,$clear,$search){
		if(!$search){$search = '';}
		$this.socket.emit('chat.list', $sender_id, $search, function($response){
			if($response.data && $response.data.length > 0){
				$('.no-chat-connection').remove();
				$('.chat-holder').show();
				
				$this.print($response,"CHAT LIST FETCHED");
				
				/* RENDERING CHAT LIST HTML */
				$this.render_user_list_html($response.data,$clear);

				/* MAKING CURRENT USER (SENDER) ONILINE */
				$this.update_sender_online_status();

				/* ACTIVATING FIRST ONE */
				if($('.chat-box').length < 1){
					if($('[data-request="init-chat"]').length > 0){
						if($('.chat-item-list.active').length > 0){
							$('.chat-item-list.active').trigger('click');	
						}else{
							$('[data-request="init-chat"]:first').trigger('click');
						}
					}
				}
			}else{
				if($('.no-chat-connection').length > 0){
					$('.heading-inbox').hide();
					$('.no-chat-connection').html($this.no_chat_list);
				}else if($('ul.names-list').find('.mCSB_container').length > 0){
					$('ul.names-list').find('.mCSB_container').html('');
				}else{
					window.location = window.location;	
				}
			}
		});
	}

	this.markread_all_messages = function($group_id,$sender_id,$receiver_id){
		$this.socket.emit('chat.readall.messages',$group_id, $sender_id, $receiver_id,function($response){
			$this.print({'group_id': $group_id, 'receiver_id': $sender_id, 'sender_id': $receiver_id},"READ ALL MESSAGES");

			$('[data-group-id="'+$group_id+'"]').removeClass('unread-list');
			$('[data-group-id="'+$group_id+'"]').find('.chat-bubbles').html('');
		});
	}
	
	this.send_message = function($data) {
		/* RENDERING DATA TO SELF END */
		$this.render_message($data,true,false,'append',false);
		
		/* ACK MESSAGE TO NEXT END */
		$this.socket.emit('chat.send.message',$data, function($response){
			$this.markread_all_messages($data.group_id,$data.sender_id,$data.receiver_id);
			$this.print($response,"MESSAGE SENT SUCCESSEFULLY");
			if($response.status !== false){
				setTimeout(function(){
					$('[data-chat-id="'+$response.data.timestamp+'"]').find('.chat-message').html($this.sanatize($response.data.message));
					$('[data-chat-id="'+$response.data.timestamp+'"]').attr('data-chat-id',$response.data.id_chat);
				},800);

				$this.render_self_message_chat_list($response.data.group_id,$response.data.receiver_id,$response.data.message,$response.data.timelog,$response.data.ago);
			}else{
				if($response.data.sender_status !== 'active'){
					window.location = window.location;
				}else{
					$this.get_chat_list($this.sender_details.sender_id,true);
					setTimeout(function(){
						$('[data-request="init-chat"]:first').trigger('click');
					},2000);
				}
			}
		});
	}
	
	this.delete_all_messages = function($group_id,$receiver_id) {
		/* DELETING MESSAGES FROM SELF */
		$this.socket.emit('chat.delete.all',$group_id, $this.sender_details.sender_id, $receiver_id, function($response){
			$this.print($response,"DELETED ALL MESSAGES");
			$($this.message_box).find(".mCSB_container").html("");
		});
	}

	this.terminate_chat = function($receiver_id,$group_id) {
		/* DELETING MESSAGES FROM SELF */
		$this.socket.emit('chat.terminate',$this.sender_details.sender_id, $receiver_id, $group_id, function($response){
			$this.print($response,"TERMINATING CHAT");
		});
	}

	this.toLocalTime = function(time){

		var sent_time = moment.tz(time, "America/Scoresbysund");
		return sent_time.tz(moment.tz.guess()).format('LT');
	}
	
	this.render_message = function($response,$scoll,$new_message,$method,$loader) {

		this.print($response, "Message RENDERING....");
		var $html = "";
		var $unread_messages_element = $('.unread-message-item').length;
		
		if(($response.sender_id == $this.sender_details.id_user) || ($('[data-chat-box="'+$response.group_id+'"]').length > 0)){
			$this.print($response,"RENDER MESSGAE");
			if($response.sender_id == $this.sender_details.id_user){
				var $class = 'text-right';
			}else{
				var $class = 'text-left';
			}
			
			if(!$response.timestamp){
				var $chat_id = $response.id_chat;
			}else{
				var $chat_id = $response.timestamp;
			}

			if($new_message === true){
				/*$($this.message_box).prop('scrollHeight') > ($($this.message_box).scrollTop()+1000)*/
				if(parseInt($('[name="mcs_top_pct"]').val()) < 50){
					if($('.unread-messages').length < 1){
						$class += ' unread-message-item';
						//$html += '<li class="seperator unread-messages"><span><span class="past-day">1 '+$this.new_message_tag+'</span></span></li>';
					}else{
						if($('.unread-message-item').length < 1){
							$class += ' unread-message-item';
							$('.unread-messages:first').remove();
							//$html += '<li class="seperator unread-messages"><span><span class="past-day">1 '+$this.new_message_tag+'</span></span></li>';
						}else{
							$class += ' unread-message-item';
							$unread_messages_element += 1;
							$('.unread-messages').html('<span><span class="past-day">'+($unread_messages_element)+' '+$this.new_message_tag+'</span></span>');
						}
					}
				}
			}

			if($response.message_type == 'text' || $response.message_type == 'image'){
				$html += '<li data-item="chat-item" data-chat-id="'+$chat_id+'" class="'+$class+'">';
					$html += '<div  class="chat-inner-msg">';
						$html += '<span class="chat-image">';
							$html += '<img title="'+$response.sender+'" src="'+$response.sender_picture+'" width="50">';
						$html += '</span>';
						$html += '<span class="chat-item">';
							$html += '<span class="chat-message">';
								$html += $this.sanatize($response.message);
							$html += '</span>';
						$html += '</span>';

						if($response.ago){

							$html += '<span class="message-timing">'+$this.toLocalTime($response.created)+'</span>';
						}else{
							$html += '<span class="message-timing">'+moment().format('LT')+'</span>';
						}
						
					$html += '</div>';
				$html += '</li>';
			}else if($response.message_type == 'raise-dispute'){
				$html += '<li data-item="chat-item" data-chat-id="'+$chat_id+'" class="system-message">';
					$html += '<div  class="chat-inner-msg">';
						if($response.ago){
							$timelog = '<span class="message-timing">'+$this.toLocalTime($response.created)+'</span>';
						}else{
							$timelog = '<span class="message-timing">'+moment().format('LT')+'</span>';
						}

						$html += '<span class="chat-item">';
							$html += '<span class="chat-message">';
								$html += $response.message+" on "+$timelog;
							$html += '</span>';
						$html += '</span>';
												
					$html += '</div>';
				$html += '</li>';
			}else if($response.message_type == 'report-abuse'){
				$html += '<li data-item="chat-item" data-chat-id="'+$chat_id+'" class="system-message">';
					$html += '<div  class="chat-inner-msg">';
						if($response.ago){
							$timelog = '<span class="message-timing">'+$this.toLocalTime($response.created)+'</span>';
						}else{
							$timelog = '<span class="message-timing">'+moment().format('LT')+'</span>';
						}

						$html += '<span class="chat-item">';
							$html += '<span class="chat-message">';
								if($response.sender_id == $this.sender_details.id_user){
									$html += "<span class='tag-user-name'>You</span> have reported <span class='tag-user-name'>"+$response.receiver+"</span> for abusive behaviour - '"+$response.message+"' ";
								}else{
									$html += "<span class='tag-user-name'>"+$response.sender+"</span> has reported your comment for abusive behaviour - '"+$response.message+"' ";
								}
								$html += "on "+$timelog+". ";
								$html += $this.report_abuse_company_text;
							$html += '</span>';
						$html += '</span>';			
					$html += '</div>';
				$html += '</li>';
			}else if($response.message_type == 'report-abuse-resolved'){
				$html += '<li data-item="chat-item" data-chat-id="'+$chat_id+'" class="system-message bg-light-gray">';
					$html += '<div  class="chat-inner-msg">';
						if($response.ago){
							$timelog = '<span class="message-timing">'+$this.toLocalTime($response.created)+'</span>';
						}else{
							$timelog = '<span class="message-timing">'+moment().format('LT')+'</span>';
						}

						$html += '<span class="chat-item">';
							$html += '<span class="chat-message">';
								if($response.sender_id == $this.sender_details.id_user){
									$html += ($response.message).replace("%s","<span class='tag-user-name'>You</span>");
								}else{
									$html += ($response.message).replace("%s","<span class='tag-user-name'>"+$response.sender+"</span>");
								}
								$html += "on "+$timelog;
							$html += '</span>';
						$html += '</span>';			
					$html += '</div>';
				$html += '</li>';
			}

			if($method == 'append'){
				if($this.chat_right_box.find($this.message_box).find('.mCSB_container').length > 0){
					$this.chat_right_box.find($this.message_box).find('.mCSB_container').append($html);
					$this.chat_right_box.find($this.message_box).mCustomScrollbar('update');
		        }else{
					$this.chat_right_box.find($this.message_box).append($html);
					setTimeout(function(){
						$this.chat_right_box.find($this.message_box).mCustomScrollbar({
							scrollInertia: 400,
				            advanced: {
				                updateOnContentResize: true
				            },
							callbacks:{
								onScrollStart:function(){ myCallback(this,"#onScrollStart") },
								onScroll:function(){ myCallback(this,"#onScroll") },
								onTotalScroll:function(){ myCallback(this,"#onTotalScroll") },
								onTotalScrollOffset:60,
								onTotalScrollBack:function(){ myCallback(this,"#onTotalScrollBack") },
								onTotalScrollBackOffset:50,
								whileScrolling:function(){ 
									myCallback(this,"#whileScrolling"); 
									$('[name="mcs_top"]').val(this.mcs.top);
									$('[name="mcs_dragger_top"]').val(this.mcs.draggerTop);
									$('[name="mcs_top_pct"]').val(this.mcs.topPct+"%");
									$('[name="mcs_direction"]').val(this.mcs.direction);
									$('[name="mcs_total_scroll_offset"]').val("60");
									$('[name="mcs_total_scroll_back_offset"]').val("50");

									setTimeout(function(){
										$this.print(parseInt($('[name="mcs_top_pct"]').val()),"SCROLL TOP");
										if(parseInt($('[name="mcs_top_pct"]').val()) < 10){
											$this.print({},"SCROLLING FOR LOAD MORE DONE");
											$('[data-request="load-more"]').trigger('click');
										}else{
											$this.print({},"SCROLLING FOR LOAD MORE NOT DONE");
										}
									},2000);
								},
								alwaysTriggerOffsets:false
							}
						});
					},1000);
		        }
			}else{
				if($this.chat_right_box.find($this.message_box).find('.mCSB_container').length > 0){
					$this.chat_right_box.find($this.message_box).find('.mCSB_container').prepend($html);
					$this.chat_right_box.find($this.message_box).mCustomScrollbar('update');
		        }else{
					$this.chat_right_box.find($this.message_box).prepend($html);
					setTimeout(function(){
						$this.chat_right_box.find($this.message_box).mCustomScrollbar({
							scrollInertia: 400,
				            advanced: {
				                updateOnContentResize: true
				            },
							callbacks:{
								onScrollStart:function(){ myCallback(this,"#onScrollStart") },
								onScroll:function(){ myCallback(this,"#onScroll") },
								onTotalScroll:function(){ myCallback(this,"#onTotalScroll") },
								onTotalScrollOffset:60,
								onTotalScrollBack:function(){ myCallback(this,"#onTotalScrollBack") },
								onTotalScrollBackOffset:50,
								whileScrolling:function(){ 
									myCallback(this,"#whileScrolling"); 
									$('[name="mcs_top"]').val(this.mcs.top);
									$('[name="mcs_dragger_top"]').val(this.mcs.draggerTop);
									$('[name="mcs_top_pct"]').val(this.mcs.topPct+"%");
									$('[name="mcs_direction"]').val(this.mcs.direction);
									$('[name="mcs_total_scroll_offset"]').val("60");
									$('[name="mcs_total_scroll_back_offset"]').val("50");

									setTimeout(function(){
										$this.print(parseInt($('[name="mcs_top_pct"]').val()),"SCROLL TOP");
										if(parseInt($('[name="mcs_top_pct"]').val()) < 10){
											$this.print({},"SCROLLING FOR LOAD MORE DONE");
											$('[data-request="load-more"]').trigger('click');
										}else{
											$this.print({},"SCROLLING FOR LOAD MORE NOT DONE");
										}
									},2000);
								},
								alwaysTriggerOffsets:false
							}
						});
					},1000);
		        }
			}
		}else{
			$this.print({},"UNABLE TO RENDER MESSGAE");
		}

		if($scoll === true){
			$this.scroll_chat_box();
		}

		if($new_message === true){
			$('.new-message-tag').remove();

			/*$($this.message_box).prop('scrollHeight') > ($($this.message_box).scrollTop()+1000)*/
			if(parseInt($('[name="mcs_top_pct"]').val()) < 50){
				$($this.message_box).parent().append('<span class="new-message-tag"><img src="'+base_url+'/images/arrow-down-icon.gif" width="15" /></span>');
			}else{
				$('.unread-messages').remove();
				$this.scroll_chat_box();
			}
		}else if(!$unread_messages_element){
			$('.unread-messages').remove();

			if($loader === false){
				$this.scroll_chat_box(true);
			}
		}
	};

	this.scroll_chat_box  = function($no_delay){
		/*if(!$no_delay){
			$this.chat_right_box.find($this.message_box).animate({scrollTop: ($this.chat_right_box.find($this.message_box).prop('scrollHeight'))}, 500);
		}else{
			$this.chat_right_box.find($this.message_box).animate({scrollTop: ($this.chat_right_box.find($this.message_box).prop('scrollHeight'))},0);
		}*/

		setTimeout(function(){
			$this.chat_right_box.find($this.message_box).mCustomScrollbar("scrollTo","bottom");
		},100);

		setTimeout(function(){
			$this.chat_right_box.find($this.message_box).mCustomScrollbar("scrollTo","bottom");
		},1000);
	}

	this.focus_chat_box  = function(){
		$('[name="search-friend"]').val('').trigger('keyup');
		
		if($is_mobile_device == 'no'){
			if($('[data-request="send-message"]').length > 0){
				$('body').animate({scrollTop: ($('[data-request="send-message"]').prop('scrollHeight')+180)}, 500);
				$('[data-request="send-message"]').focus();
			}else{
				$('body').animate({scrollTop: ($('.send-message-request').prop('scrollHeight')+180)}, 500);
			}
		}		
	}

	this.prepend_previous_messages  = function($group_id, $sender_id,$receiver_id,$page,$chat_id){
		var $direction = 'up';
		if(!$chat_id){
			$chat_id = 0;
		}

		$this.socket.emit('chat.history', $group_id, $sender_id, $receiver_id, $page, $chat_id, $direction, function($response){
			if($response.data){
				$this.print($response,"CHAT HISTORY LOADING");
				var $chat_id = null;
				var $counter = null;

				$.each($response.data,function($intex,$item){
					$chat_id = $item.id_chat;
					
					if($page === 1){
						$this.render_message($item,false,false,'prepend',false);	
					}else{
						$this.render_message($item,false,false,'prepend',true);	
					}

					$counter++;
				});

				if($counter === $response.data.length){
					$loading = false;
				}

				$this.pager($group_id, $sender_id, $receiver_id, $chat_id,$page);
			}else{
				$this.print($response,"CHAT HISTORY NOT LOADED");
			}
		});
	}

	this.reorder_chat_list  = function(){
		if($("ul.names-list").find('.mCSB_container').length > 0){
			$("ul.names-list").find('.mCSB_container').append($("ul.names-list li").get().sort(function (a, b) {
		       	return $(b).find('p').attr('class').match(/\d+/) - $(a).find('p').attr('class').match(/\d+/);
		   	}));
		}else{
			$("ul.names-list").append($("ul.names-list li").get().sort(function (a, b) {
		       	return $(b).find('p').attr('class').match(/\d+/) - $(a).find('p').attr('class').match(/\d+/);
		   	}));
		}
	}

	this.remove_new_message_seperator  = function(){
		$($this.message_box).scroll(function(e){
			if(parseInt($('[name="mcs_top_pct"]').val()) > 50){
				$('.new-message-tag').trigger('click');
			}
		});
	}

	this.pager  = function($group_id, $sender_id, $receiver_id, $chat_id,$page){
		if($('[data-request="load-more"]').length < 1){
			$($this.message_box).parent().prepend('<button class="hide" data-request="load-more" data-page="1" data-chat-id="'+$chat_id+'" data-sender-id="'+$sender_id+'" data-receiver-id="'+$receiver_id+'" data-group-id="'+$group_id+'">Load More</button>');
		}else{
			$('[data-request="load-more"]').attr('data-page',(parseInt($page)+1));
			$('[data-request="load-more"]').attr('data-chat-id',parseInt($('[data-item="chat-item"]:first').not('.seperator').attr('data-chat-id')));
		}
	}

	this.is_valid_url = function(s) {
	   var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	   return regexp.test(s);
	}

	this.linkify = function(s) {
	    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i;
	    return s.replace(exp,"<a target='_blank' href='$1'>$1</a>"); 
	}

	this.emailify = function(s) {
	    var exp = /([a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/g;
	    return s.replace(exp,"<a target='_blank' href='mailto:$1' class='email-text'>$1</a>"); 
	}

	this.strip_tags = function(s) {
	    var rex = /(<([^>]+)>)/ig;
	   	return s.replace(rex , "");
	}

	this.html_encode = function (e){
	 	return $('<div/>').text(e).html().replace(/&/g,'&amp;');
	}

	this.html_decode = function(e){
	 	return $('<div/>').html(e).text();
	}

	this.sanatize = function(e,not_nl2br){
		if(not_nl2br){
			/*if(e.match(/\.(jpeg|jpg|gif|png)$/) != null){
				return $this.image_text;
			}else*/{
	 			return $this.emailify($this.linkify($this.html_decode(e)));
			}
		}else if(this.is_valid_url(e)){
			return $this.emailify($this.linkify($this.html_decode(e))).replace(/(\r\n|\n|\r)/gm,"<br>");
		}else if(e.match(/\.(jpeg|jpg|gif|png|JPEG|JPG|GIF|PNG)$/) != null){	
	 		return '<a class="fancybox" href="'+asset_url+'uploads/chat/'+e+'"><img href="'+asset_url+'uploads/chat/'+e+'" src="'+asset_url+'uploads/chat/thumbnail/'+e+'" style="max-width: 100px" /></a>';
		}else{	
	 		return $this.emailify($this.linkify($this.html_decode(e))).replace(/(\r\n|\n|\r)/gm,"<br>");
		}
	}

	this.print  = function($data,$heading){
		if($debug){
    		console.log('_______________________________\n\n '+$heading+'\n_______________________________'+'\n\n'+ JSON.stringify($data,null,2)+'\n\n_______________________________'+'\n\n DEVELOPED BY: AMAN VERMA\n_______________________________');
    	}
	}

	$(document).on('click','.new-message-tag',function(){
		$this.scroll_chat_box();
		$(this).fadeOut();
		$(this).remove();
		$('.unread-message-item').removeClass('unread-message-item');
	});
	
	$(document).on('click','[data-request="init-chat"]',function(){
		if(!$(this).hasClass('active') || $('[data-chat-box="'+$(this).data('group-id')+'"]').length < 1){
			var $group_id 				= $(this).data('group-id');
			var $receiver_id 			= $(this).data('user-id');
			var $receiver 				= $(this).data('user');
			var $receiver_email 		= $(this).data('email');
			var $receiver_image 		= $(this).find('.chat-image img').attr('src');
			var $receiver_profile_link 	= $(this).data('profile-link');
			var $receiver_profile_text 	= $(this).data('profile-text');
			var $message_box_text 		= $(this).data('message-box-text');
			var $request_status 		= $(this).data('request-status');
			

			$is_receiver_online 		= '';
			if($(this).find('.chat-current-status').hasClass('online')){
				$is_receiver_online 	= 'online';
			}
			
			/*CLEARING SEARCH BOX*/
			$('[name="search-friend"]').val('').trigger('keyup');

			/* REMOVE AND MAKE CURRENT USER ACTIVE WITH RESPECT TO CURRENT CHAT POPUP */
			$('[data-request="init-chat"].active').removeClass('active');
			$(this).addClass('active');

			/* OPENING CHAT POPUP */
			/*if($('[data-chat-box="'+$receiver_id+'"]').length < 1){*/
				$this.init_chat_box($group_id,$receiver_id,$receiver,$receiver_email,$receiver_image,$receiver_profile_link,$receiver_profile_text,$message_box_text,$is_receiver_online,$request_status);
				
				/* LOADING CHAT HISTORY */
				$this.prepend_previous_messages($group_id,$this.sender_details.sender_id,$receiver_id,1); /* FOR PAGE 1 */

				$this.markread_all_messages($group_id,$this.sender_details.sender_id,$receiver_id);
			/*}*/

			$this.focus_chat_box();

			setTimeout(function(){
				$this.scroll_chat_box();
			},1000);
			/* SAVING CURRENT CHAT WINDOW */
			writeCookie('current_chat_window',$group_id);
		}
	});

	$(document).on('keydown','[data-request="send-message"]',function(e){
	   	if (e.keyCode == 13 && !e.shiftKey){
		    e.preventDefault();
			var $message = $this.html_encode($(this).val()).trim();

			if(!$message){ return false;}
			
			var date = new Date();
			var components = [
			    date.getYear(),
			    date.getMonth(),
			    date.getDate(),
			    date.getHours(),
			    date.getMinutes(),
			    date.getSeconds(),
			    date.getMilliseconds()
			];

			var $data = {
				'timestamp'			: components.join(""),
				'local_chat_id'		: components.join(""),
				'sender'			: $('[name="sender"]').val(),
				'sender_id'			: $('[name="sender_id"]').val(),
				'sender_picture'	: $('[name="sender_picture"]').val(),
				'receiver'			: $('[name="receiver"]').val(),
				'receiver_id'		: $('[name="receiver_id"]').val(),
				'group_id'			: $('[name="group_id"]').val(),
				'message'			: $message,
				'message_type'		: 'text',
				'created'			: moment().format('YYYY-MM-DD HH:mm:ss')
			};

			/* REMOVING TEXT OF THE SCREEN */
			$(this).val("");

			$this.send_message($data);			
		}
	});

	$(document).on('click','[data-request="send-message-button"]',function(e){
        $('[data-request="send-message"]').trigger($.Event( "keydown",{ keyCode:13}));
    });

	$(document).on('click','[data-request="load-more"]',function(e){
		$('.loader').remove();

		var $_this 			= $(this);
		var $group_id 		= $_this.data('group-id');
		var $sender_id 		= $_this.data('sender-id');
		var $receiver_id 	= $_this.data('receiver-id');
		var $chat_id 		= $_this.attr('data-chat-id');
		var $page 			= $_this.attr('data-page');
		
		$($this.message_box).find('.mCSB_container').prepend('<li class="seperator loader"><img src="'+asset_url+'/images/load.gif" width="20"></li>');
		
		if($loading === false){
			$loading = true;
			
			//$this.chat_right_box.find($this.message_box).animate({scrollTop: 50}, 800);

			$this.print($loading,"LOADING");
			$this.prepend_previous_messages($group_id, $sender_id,$receiver_id,$page,$chat_id);
			$this.pager($group_id, $sender_id, $receiver_id, $chat_id,$page);
			
		}else{
			$this.print($loading,"LOADING");
		}
		
		setTimeout(function(){$('.loader').remove();},5000);
	});

	// setTimeout(function(){
	// 	$($this.message_box).scroll(function(e){
	// 		e.preventDefault();
	// 		$this.print(parseInt($('[name="mcs_top_pct"]').val()),"SCROLL TOP");
	// 		console.log(parseInt($('[name="mcs_top_pct"]').val()));
	// 		if(parseInt($('[name="mcs_top_pct"]').val()) < 10){
	// 			$this.print({},"SCROLLING FOR LOAD MORE DONE");
	// 			$('[data-request="load-more"]').trigger('click');
	// 		}else{
	// 			$this.print({},"SCROLLING FOR LOAD MORE NOT DONE");
	// 		}

	// 		/* BIDING CURRENT DIV SCROLL AND PREVENTING PARENT DIV SCROLL*/
	// 		$(this).on('mousewheel DOMMouseScroll', function ( e ) {
	// 		    var e0 = e.originalEvent,
	// 		        delta = e0.wheelDelta || -e0.detail;

	// 		    this.scrollTop += ( delta < 0 ? 1 : -1 ) * 5;
	// 		    e.preventDefault();
	// 		});
	// 	});
	// },5000);

	$(document).on('click','[data-request="accept-chat"]',function(e){
		$('#popup').show();
		var $_this 			= $(this);
		var $group_id 		= $('[name="group_id"]').val();
		var $sender_id 		= $('[name="sender_id"]').val();
		var $receiver_id 	= $('[name="receiver_id"]').val();

		$this.socket.emit('chat.accept', $group_id, $sender_id, $receiver_id, function($response){
			$this.print($response,"CHAT ACCEPT ACKNOWLEDGED");

			$this.get_chat_list($this.sender_details.sender_id,true);

			setTimeout(function(){
				$('.chat-item-list.active').trigger('click');
				$('#popup').hide();
				location.reload();
			},1000);
		});
	});
	
	$(document).on('keyup','[name="search-friend"]',function(e){
		$this.get_chat_list($this.sender_details.sender_id,true,$(this).val());
	});

	$(document).on('click','[data-request="reject-chat"]',function(e){
		$('#popup').show();
		var $_this 			= $(this);
		var $group_id 		= $('[name="group_id"]').val();
		var $sender_id 		= $('[name="sender_id"]').val();
		var $receiver_id 	= $('[name="receiver_id"]').val();

		$this.socket.emit('chat.reject', $group_id, $sender_id, $receiver_id, function($response){
			$this.print($response,"CHAT REJECT ACKNOWLEDGED");

			$this.get_chat_list($this.sender_details.sender_id,true);

			setTimeout(function(){
				if($('.chat-item-list').length > 0){
					$('.chat-item-list:first').trigger('click');
				}else{
					window.location = window.location;
				}
				$('#popup').hide();
			},1000);
		});
	});


	$(document).on('click','[data-request="report-abuse"]',function(e){
		$('#popup').show();  $('.alert').remove(); $(".has-error").removeClass('has-error');$('.help-block').remove();
		var $_this 			= $(this);
		var date = new Date();
		var components = [
		    date.getYear(),
		    date.getMonth(),
		    date.getDate(),
		    date.getHours(),
		    date.getMinutes(),
		    date.getSeconds(),
		    date.getMilliseconds()
		];

		var $data = {
			'timestamp'			: components.join(""),
			'local_chat_id'		: components.join(""),
			'group_id'			: $('[name="group_id"]').val(),
			'sender'			: $('[name="sender"]').val(),
			'sender_id'			: $('[name="sender_id"]').val(),
			'sender_picture'	: $('[name="sender_picture"]').val(),
			'receiver'			: $('[name="receiver"]').val(),
			'receiver_id'		: $('[name="receiver_id"]').val(),
			'message'			: $('[name="reason"]').val(),
			'message_type'		: 'report-abuse',
			'created'			: moment().format('YYYY-MM-DD HH:mm:ss')
		}
		
		$this.socket.emit('chat.report.abuse',$data, function($response){
			$('#popup').hide(); 
			$this.print($response,"REPORTED ABUSE");

			if($response.status === true){
				$('[name="reason"]').val('');
				$('[data-dismiss="modal"]').trigger('click');

				/* RENDERING DATA TO SELF END */
				$this.render_message($data,true,false,'append',false);
				
				setTimeout(function(){
					$('[data-item="chat-item"]:last').attr('data-chat-id',$response.data.id_chat);
				},800);

				$this.render_self_message_chat_list($response.data.group_id,$response.data.receiver_id,$response.data.message,$response.data.timelog,$response.data.ago);
			}else{
				show_validation_error($response.data);
			}
		});
	});

	$(document).on('change','[data-request="upload"]',function(e){
		var $_this = $(this);
        var $target         = $_this.data('target');
        var $url            = $($target).attr('action');
        var $method         = $($target).attr('method');
        var $data           = new FormData($($target)[0]);
        
		$.ajax({
            url  : $url,
            data : $data,
            cache : false,
            type : $method,
            dataType : 'json',
            contentType : false,
            processData : false,
            success : function($response){
                $this.print($response,"FILE UPLOADED");
				
				var date = new Date();
				var components = [
				    date.getYear(),
				    date.getMonth(),
				    date.getDate(),
				    date.getHours(),
				    date.getMinutes(),
				    date.getSeconds(),
				    date.getMilliseconds()
				];

				var $data = {
					'timestamp'			: components.join(""),
					'local_chat_id'		: components.join(""),
					'sender'			: $('[name="sender"]').val(),
					'sender_id'			: $('[name="sender_id"]').val(),
					'group_id'			: $('[name="group_id"]').val(),
					'sender_picture'	: $('[name="sender_picture"]').val(),
					'receiver'			: $('[name="receiver"]').val(),
					'receiver_id'		: $('[name="receiver_id"]').val(),
					'message'			: $response.data.filename,
					'message_type'		: 'image',
					'created'			: moment().format('YYYY-MM-DD HH:mm:ss')
				};

				/* REMOVING TEXT OF THE SCREEN */
				$(this).val("");

				$this.send_message($data);			
            }
        });
	});

	$(document).on('click','.messages',function(e){
		$this.focus_chat_box();
	});


	$(document).on('click','[data-request="delete-all"]',function(e){
		$receiver_id 	= $(this).data('receiver_id');
		$group_id 		= $(this).data('group_id');
		
		swal({
            title: $alert_message_text,
            html: $this.delete_message_text,
            showLoaderOnConfirm: true,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            confirmButtonText: $confirm_botton_text,
            cancelButtonText: $cancel_botton_text,
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                    	$this.delete_all_messages($group_id,$receiver_id);
                        resolve();
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
	});

	$(document).on('click','[data-request="terminate-chat"]',function(e){
		$receiver_id 	= $(this).data('receiver_id');
		$group_id 		= $(this).data('group_id');

		swal({
            title: $alert_message_text,
            html: $this.terminate_message_text,
            showLoaderOnConfirm: true,
            showCancelButton: true,
            showCloseButton: false,
            allowEscapeKey: false,
            allowOutsideClick:false,
            confirmButtonText: $confirm_botton_text,
            cancelButtonText: $cancel_botton_text,
            preConfirm: function (res) {
                return new Promise(function (resolve, reject) {
                    if (res === true) {
                    	$this.terminate_chat($receiver_id,$group_id);
                        resolve();
                    }
                })
            }
        }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
	});

	$("#report-abuse").on('shown.bs.modal', function(event){
	    var $receiver 				= $('[data-toggle="modal"]').data('receiver');
	    var $receiver_id 			= $('[data-toggle="modal"]').data('receiver-id');
	    var $reported_abuse_text 	= $this.reported_abuse_text.replace("%s",$receiver);

	    $('[data-target="reported-user"]').html($reported_abuse_text);
	    $('[data-target="receiver-id"]').val($receiver_id);
  	});
	
	if(1/*$(window).width() <= 767*/){
	  	$(document).on('click','#list-toggle-btn', function(){ $('body').toggleClass('slide-chat'); });
	  	$('html,body').on('click', function(e){
	  		var target = $(e.target);
	  		if (!target.is('#chat-left-box') && !target.is('#chat-left-box *') && target.is('#list-toggle-btn') ) {
	            if ($('#chat-left-box').is(':visible')) {
	                $('body').removeClass('slide-chat')
	            }
	        }
	        
	        $('.names-list').on('click', function(){
	        	if ($('#chat-left-box').is(':visible')) {
	                $('body').removeClass('slide-chat')
	            }
	        });
	  	});

	}
  	/*$("#report-abuse").on('hidden.bs.modal', function(event){
  		$('.send-message-box').html($this.chat_not_available);
  		$('.send-message-box').addClass('send-message-request').removeClass('send-message-box');	
  	});*/
  	
	/*$($this.message_box).scroll(function(e){
		//$($this.message_box).prop('scrollHeight') > ($($this.message_box).scrollTop()+1000)
	});*/

  	$(".fancybox").fancybox({
      	openEffect  : 'elastic',
      	closeEffect : 'elastic',
      	closeBtn    : true,
      	helpers : {
        	title : {type : 'inside'},
        	buttons : {},
        	overlay : {closeClick: false}
      	}
    });

	setTimeout(function(){
		if($('[data-request="init-chat"]').length > 0){
			if($('.chat-item-list.active').length > 0){
				$('.chat-item-list.active').trigger('click');	
			}else{
				$('[data-request="init-chat"]:first').trigger('click');
			}
		}
	},1);

	setTimeout(function(){
		$this.remove_new_message_seperator();
	},1000);
}

function myCallback(el,id){
	if($(id).css("opacity")<1){return;}
	var span=$(id).find("span");
	clearTimeout(timeout);
	span.addClass("on");
	var timeout=setTimeout(function(){span.removeClass("on")},350);
}
