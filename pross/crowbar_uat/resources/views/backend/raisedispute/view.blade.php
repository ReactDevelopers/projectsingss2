@extends('layouts.backend.dashboard')   

@section('requirecss')
	@if(0)
    	<link href="{{url('css/placeholder.min.css')}}" rel="stylesheet" />
	@endif
@endsection
@section('inlinejs-top')
    <script src="{{ asset('js/chat/socket.io') }}.js"></script>
    <script src="{{ asset('js/chat/slimscroll.js') }}"></script>
    <script src="{{ asset('js/chat/moment.js') }}"></script>
    <script src="{{ asset('js/chat/moment-timezone.js') }}"></script>
    <script src="{{ asset('js/chat/moment-timezone-with-data-2012-2022.js') }}"></script>
    <script src="{{ asset('js/chat/livestamp.js') }}"></script>
@endsection
@section('content')
	<section class="content">
		<div class="row">
			<div class="col-md-3">
				<div class="panel">
					<div class="panel-body">
						<img class="profile-user-img img-responsive img-circle" src="{{ url($raisedispute['project']['employer']['company_logo']) }}" />
						<br>
						<ul class="list-group list-group-unbordered">
							<li class="list-group-item">
								<b>Dispute By</b> <span class="pull-right ellipsis_text" title="{{$raisedispute['sender']['name']}}">{{$raisedispute['sender']['name']}}</span>
							</li>
							<li class="list-group-item">
								<b>Dispute Status</b> <span class="pull-right">{{ucfirst($raisedispute['status'])}}</span>
							</li>
							<li class="list-group-item">
								<b>Date</b> <span class="pull-right">{{___d($raisedispute['created'])}}</span>
							</li>
							@if(!empty($raisedispute['admin_name']))
								<li class="list-group-item">
									<b>Closed By</b> <span class="pull-right">{{$raisedispute['admin_name']}}</span>
								</li>
								<li class="list-group-item">
									<b>Date</b> <span class="pull-right">{{___d($raisedispute['dispute_closed_date'])}}</span>
								</li>
							@endif
						</ul>

						@if(0)
							<h3 class="profile-username text-center">{{$raisedispute['project']['employer']['name']}}</h3>
							<p class="text-muted text-center">{{$raisedispute['project']['employer']['name']}}</p>
						@endif
						<ul class="raise-dispute-steps">
				            <li @if(!empty($raisedispute) && (!empty($raisedispute['step']) && $raisedispute['step'] > 1)) class="previous" @endif>
				                <span class="step">1</span>
				                <span class="step-detail">{{trans('website.W0822')}}</span>
				            </li>
				            <li  @if(!empty($raisedispute) && (!empty($raisedispute['step']) && $raisedispute['step'] > 2)) class="previous" @endif>
				                <span class="step">2</span>
				                <span class="step-detail">{{trans('website.W0824')}}</span>
				            </li>
				            <li  @if(!empty($raisedispute) && (!empty($raisedispute['step']) && $raisedispute['step'] > 3)) class="previous" @endif>
				                <span class="step">3</span>
				                <span class="step-detail">{{trans('website.W0825')}}</span>
				            </li>
				            <li  @if(!empty($raisedispute) && (!empty($raisedispute['step']) && $raisedispute['step'] > 4)) class="previous" @endif>
				                <span class="step">4</span>
				                <span class="step-detail">{{trans('website.W0826')}}</span>
				            </li>
				            <li  @if(!empty($raisedispute) && (!empty($raisedispute['step']) && $raisedispute['step'] > 5)) class="previous" @endif>
				                <span class="step">5</span>
				                <span class="step-detail">{{trans('website.W0827')}}</span>
				            </li>
				        </ul>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="<?php echo ($page == 'detail')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=detail'); ?>">Dispute Detail</a></li>
						<li class="<?php echo ($page == 'project')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=project'); ?>">Job Detail</a></li>
						<li class="<?php echo ($page == 'proposal')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=proposal'); ?>">Accepted Proposal</a></li>
						<li class="<?php echo ($page == 'payments')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=payments'); ?>">Payouts</a></li>
						<li class="<?php echo ($page == 'payments-due')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=payments-due'); ?>">Due Payments</a></li>
						<li class="<?php echo ($page == 'disputed-payment')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=disputed-payment'); ?>">Disputed Payments</a></li>
						@if(0)
							<li class="<?php echo ($page == 'chat')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'&page=chat'); ?>">Chat</a></li>
						@endif
					</ul> 
					<div class="tab-content">
						@if($page == 'detail')
							<br>
							<div class="tab-pane<?php echo ($page == 'detail')?' active':''; ?>" style="margin-left: -10px;margin-right: -10px;margin-bottom: -10px;">
								<div class="panel-body">
									@if(!empty($raisedispute) && $raisedispute['comments'])
						                @foreach($raisedispute['comments'] as $item)
						                    <div class="white-wrapper m-b-15">
						                        <div class="raise-dispute-box">
						                            <h4>{{ucfirst($item['sender']['type'])}}: {{$item['sender']['name']}}</h4>
						                            <div class="raise-dispute-message">{!!nl2br($item['comment'])!!}</div>
						                            <div class="raise-dispute-time">
						                                {{___d($item['created'])}}
						                            </div>
						                            <div class="raise-dispute-files">
						                                @foreach($item['files'] as $file)
						                                    @includeIf('employer.jobdetail.includes.attachment',['file' => $file, 'delete' => true])
						                                @endforeach
						                            </div>
						                        </div>
						                    </div>
						                @endforeach
						            @endif
					            </div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-md-2">
											<a href="{{ $backurl }}" class="btn btn-primary btn-block">Back</a>
											<a href="#" class="btn btn-primary btn-block hide">
												<b>Resolve & Notify</b>
											</a>
										</div>
										@if($raisedispute['status'] == 'open' && count($raisedispute['comments']) > 3)
											<div class="col-md-4"></div>
											<div class="col-md-3">
												<button 
													class="btn btn-primary btn-block" 
													data-url="{{ url(sprintf('%s/payment/pay?project_id=%s',ADMIN_FOLDER,___encrypt($raisedispute['project']['id_project']))) }}" 
													data-request="ajax-confirm" 
													data-ask_title="{{ ADMIN_CONFIRM_TITLE }}" 
													data-ask="This will transfer payment to talent and also will close job permanently.">
													<b>Pay ({{PRICE_UNIT.___format($payment['talent_payment'])}})</b>
												</button>
											</div>
											
											<div class="col-md-3">
												<button class="btn btn-primary btn-block" data-url="{{ url(sprintf('%s/payment/refund?project_id=%s',ADMIN_FOLDER,___encrypt($raisedispute['project']['id_project']))) }}" data-request="ajax-confirm" data-ask_title="{{ ADMIN_CONFIRM_TITLE }}" data-ask="This will refund payment to employer and also will close job permanently." class="btn btn-primary btn-block">
													<b>Refund ({{PRICE_UNIT.___format($payment['employer_refundable_amount'])}})</b>
												</button>
											</div>
										@endif	                            
									</div>
		                        </div>
							</div>
						@elseif($page == 'project')
							<div class="tab-pane<?php echo ($page == 'project')?' active':''; ?>">
								<div class="mailbox-read-info">
									<h3>{{$raisedispute['project']['title']}}</h3>
									<h5>Posted By: {{$raisedispute['project']['employer']['name']}}
										<span class="mailbox-read-time pull-right">{{___d($raisedispute['project']['created'])}}</span>
									</h5>
								</div>
								<div class="mailbox-read-message">
									<div class="form-group"><b>Job Type</b> <span>{{employment_types('post_job',$raisedispute['project']['employment'])}}</span> </div>
								    <div class="form-group"><b>Industry</b>
								        <ul>
								            @php
								                array_walk($raisedispute['project']['industries'], function($item){
								                    echo '<li>'.$item['industries']['name'].'</li>';
								                });
								            @endphp
								        </ul>
								    </div>
								    <div class="form-group"><b>Subindustry</b>
								        <ul>
								            @php
								                array_walk($raisedispute['project']['subindustries'], function($item){
								                    echo '<li>'.$item['subindustries']['name'].'</li>';
								                });
								            @endphp
								        </ul>
								    </div>
									<div class="form-group"><b>Expertise Level</b> <span class="pull-right">{{expertise_levels($raisedispute['project']['expertise'])}}</span></div>
									<div class="form-group">
										<b>Timeline</b> 
										<span class="pull-right">
											{{___date_difference($raisedispute['project']['startdate'],$raisedispute['project']['enddate'])}}
										</span>
									</div>
									<br>
									<b>Description</b>
									<p>{!!nl2br($raisedispute['project']['description'])!!}</p>				                  	
									<br>
								    <b>Skills</b>
								    <p>
								        @if(!empty($project_detail['skills']))
								            @php
								                array_walk($project_detail['skills'],function($item){
								                    echo '<span class="label label-default">'.$item['skills']['skill_name'].'</span>';
								                });
								            @endphp
								        @else
								            {{N_A}}
								        @endif
								    </p>
									<br>
								</div>
							</div>
						@elseif($page == 'proposal')
							<div class="tab-pane<?php echo ($page == 'proposal')?' active':''; ?>">
								<div class="mailbox-read-info">
									<h3>{{$raisedispute['sender']['name']}}</h3>
									<h5>
										<b>Quoted Price</b> {{___cache('currencies')[$raisedispute['project']['proposal']['price_unit']].___format($raisedispute['project']['proposal']['quoted_price'])}} &nbsp;&nbsp;
										<b>Job Type</b> {{ucfirst($raisedispute['project']['employment'])}} 
										<span class="mailbox-read-time pull-right">{{___d($raisedispute['project']['proposal']['created'])}}</span>
									</h5>
								</div>
								<div class="mailbox-read-info">
									<h5>Purposed Hours : {!! $raisedispute['project']['proposal']['working_hours'] !!}</h5>
								</div>
								<div class="mailbox-read-message">
									<b>Proposal</b>
									@if(!empty($raisedispute['project']['proposal']['comments']))
										<p>{!!nl2br($raisedispute['project']['proposal']['comments'])!!}</p>
									@else
										<p>{{N_A}}</p>
									@endif
								</div>
								<div class="box-footer">
									@if(!empty($raisedispute['project']['proposal']['file']['filename']))
										<ul class="mailbox-attachments clearfix">
											<li>
												<span class="mailbox-attachment-icon"><i class="fa fa-file-o"></i></span>
												<div class="mailbox-attachment-info">
													<a href="{{url(sprintf('/download/file?file_id=%s',___encrypt($raisedispute['project']['proposal']['file']['id_file'])))}}" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{$raisedispute['project']['proposal']['file']['filename']}}</a>
													<span class="mailbox-attachment-size"> {{$raisedispute['project']['proposal']['file']['extension']}}
														<a href="{{url(sprintf('/download/file?file_id=%s',___encrypt($raisedispute['project']['proposal']['file']['id_file'])))}}" class="btn btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
													</span>
												</div>
											</li>
										</ul>
									@else
										<p>{{N_A}}</p>
									@endif
								</div>
							</div>
						@elseif($page == 'payments')
							<div class="tab-pane<?php echo ($page == 'payments')?' active':''; ?>">
								{!! $html->table(); !!}
							</div>
						@elseif($page == 'payments-due')
							<div class="tab-pane<?php echo ($page == 'payments-due')?' active':''; ?>">
								{!! $html->table(); !!}
							</div>
						@elseif($page == 'disputed-payment')
							<div class="tab-pane<?php echo ($page == 'disputed-payment')?' active':''; ?>">
								{!! $html->table(); !!}
							</div>
						@elseif($page == 'chat')
							<div class="tab-pane<?php echo ($page == 'chat')?' active':''; ?>">
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
										@includeIf('chat.chatbox')
									</div>
									<div class="no-chat-connection">
										@includeIf('chat.placeholder')
									</div>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@section('requirejs')
    <script src="{{ asset('js/chat/chat.js') }}"></script>
    <script src="{{ asset('js/chat/notification.js') }}"></script>
@endsection
@push('inlinescript')
	@if(0)
		<script>
			var current_time    = moment.tz(new Date(), "{{ date_default_timezone_get() }}");
			var timezone 		= current_time.clone().tz("{{ ___current_timezone() }}");
			var socket						= new io.connect(
				'{!!env('SOCKET_CONNECTION_URL')!!}:{!!env('SOCKET_CONNECTION_POST')!!}', {
				'reconnection': true,
				'reconnectionDelay': 2000,
				'reconnectionDelayMax' : 5000,
				'secure':false
			});

			var chat_save_url 				= '{{ url(sprintf("%s/chat-save","chat")) }}';
			var chat_history_url			= '{{ url(sprintf("%s/chat-history","chat")) }}';
			var chat_users_list_url 		= '{{ url(sprintf("%s/chat-list","chat")) }}';
			var sender_details				= {!! json_encode(['sender' => $user['sender'], 'sender_picture' => $user['sender_picture'], 'type' => $user['type'], 'id_user' => $user['id_user'], 'sender_id' => $user['sender_id']]) !!};
			var chat_left_box				= '#chat-left-box';
			var chat_right_box				= '#chat-right-box';
			
			var message_box 				= 'ul.messages';
			var profile_text 				= '{{ trans("general.M0268") }}';
			var message_box_text 			= '{{ trans("general.M0269") }}';
			var chat_accept_button_text 	= '{{ trans("general.M0272") }}';
			var chat_reject_button_text 	= '{{ trans("general.M0273") }}';
			var chat_request_postfix 		= '{{ trans("general.M0274") }}';
			var chat_request_prefix 		= '{{ trans("general.M0275") }}';
			var chat_not_available 			= '{{ trans("general.M0276") }}';
			var sender_tag 					= '{{ trans("general.M0277") }}';
			var new_message_tag 			= '{{ trans("general.M0278") }}';
			var connecting_text 			= '{{ trans("general.M0279") }}';
			var no_chat_list 				= '{{ trans("website.W0297") }}';
			var report_abuse_text 			= '{{ trans("website.W0350") }}';
			var delete_all_text 			= '{{ trans("website.W0351") }}';
			var reported_abuse_text 		= '{{ trans("website.W0352") }}';
			var image_text 					= '{{ trans("website.W0423") }}';
			var support_chat_user_id 		= '{{ SUPPORT_CHAT_USER_ID }}';
			var help_desk_tagline 			= '{{ trans("website.W0424") }}';
			var chat_box_title 				= '{{ trans("website.W0434") }}';
			
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
				'delete_all_text'			: delete_all_text,
				'reported_abuse_text'		: reported_abuse_text,
				'image_text'				: image_text,
				'timezone'					: timezone,
				'support_chat_user_id'		: support_chat_user_id,
				'help_desk_tagline'			: help_desk_tagline
			});

			/* INITIATING CHAT APPLICATION */
			chat.initiate();

			$(function(){
				$('body').addClass('slide-chat');
			});
		</script>
	@endif

	@if($page == 'payments' || $page == 'payments-due' || $page == 'disputed-payment' )
		{!! $html->scripts(); !!}
	@endif
@endpush
