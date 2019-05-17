function notification(options){
	var $debug 					= false;
	var $this 					= this;
	
	this.user 					= options.user;
	this.user_type 				= options.user_type;
	this.notification_text 		= options.notification_text;
	this.no_notification_text 	= options.no_notification_text;
	this.socket 				= options.socket;

	this.initiate = function($box_element,$counter_element){
		$this.socket.emit('notification.list', $this.user, function($response){
			$this.print($response,"NOTIFICATION LIST FETCHED");

			if($response.data){
				$this.print($response.data.total_unread_notifications,"NOTIFICATION COUNT");

				if($response.data.total_unread_notifications){
					var notification_counter = parseInt($response.data.total_unread_notifications);

					if(notification_counter < 100){
						$($counter_element).html($response.data.total_unread_notifications);
					}else{
						$($counter_element).html('99+');
					}
					$($counter_element).show();
				}
			}

		});	
	}

	$this.socket.on('chat.message.'+$this.user, function($response){
		$this.unread_messages('[data-target="chat-count"]');
	});

	$this.socket.on('chat.message.marked.readall', function($response){
		$this.print($response,"MARK READ ACKNOWLEDGED");

		$this.unread_messages('[data-target="chat-count"]');
	});


	$this.socket.on('notification.sent.'+$userID, function($response){
		$this.print($response,"NOTIFICATION TRIGERED");

		$this.initiate('[data-target="notification-list"]','[data-target="notification-count"]');
	});

	this.unread_messages = function($counter_element){
		$this.socket.emit('chat.total_unread_messages', $this.user, function($response){
			$this.print($response,"TOTAL UNREAD MESSAGES");

			if($response.data){
				if($response.data.count){
					var chat_counter = parseInt($response.data.count);

					if(chat_counter < 100){
						$($counter_element).html($response.data.count);
					}else{
						$($counter_element).html('99+');
					}
					$($counter_element).show();
				}else{
					$($counter_element).html('');
					$($counter_element).hide();
				}
			}
		});	
	}

	this.print  = function($data,$heading){
		if($debug){
    		console.log('_______________________________\n\n '+$heading+'\n_______________________________'+'\n\n'+ JSON.stringify($data,null,2)+'\n\n_______________________________'+'\n\n DEVELOPED BY: AMAN VERMA\n_______________________________');
    	}
	}
}
