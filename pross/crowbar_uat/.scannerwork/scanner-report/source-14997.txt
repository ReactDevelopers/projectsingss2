@extends($extends)

{{-- ******INCLUDE CSS PAGE-WISE****** --}}
@section('requirecss')
@endsection
{{-- ******INCLUDE CSS PAGE-WISE****** --}}

{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
@section('inlinecss')
{{-- CODE WILL GO HERE --}}
@endsection
{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

{{-- ******INCLUDE JS PAGE-WISE****** --}}
@section('requirejs')
	<script type="text/javascript">
		function addReply(id_reply){
		$('#text-reply-area-'+id_reply).toggle();
		$("#reply-area-"+id_reply).hide();
			// $(".ask-question").toggle();
		}
		function insertReply(id_reply){
			var add_reply_url = $('#add-reply').val();
			var answer_description = $('#answer_description_'+id_reply).val();
			var answer_type = $('select[name=answer_type_' + id_reply + '] option:selected').val();
			
			if(answer_description.length <= 0){
				$('#text-reply-area2-'+id_reply).addClass('has-error');
				$('#text-reply-error-area-'+id_reply).html('<div class="help-block">Enter your answer.</div>');
			}else{
				$('#text-reply-area2-'+id_reply).removeClass('has-error');
				$('#text-reply-error-area-'+id_reply).html('');
			}

			if(id_reply > 0 && answer_description.length > 0){
				$.ajax({
					method: "PUT",
					url: add_reply_url,
					data: { id_parent: id_reply, answer_description: answer_description, type: answer_type}
				}).done(function(data) {
					$('#text-reply-area-'+id_reply).toggle();
					$('#answer_description_'+id_reply).val('');
					$('#add-reply-response-'+id_reply).html(data.message);
					$('#add-reply-response-'+id_reply).fadeIn('slow');
					$('#add-reply-response-'+id_reply).fadeOut(9000);
					setTimeout(function(){
						location.reload();
					},2000);
				});
			}
		}
		function loadReply(id_reply){
			var reply_list_url = $('#list-reply').val();
			if(id_reply > 0){
				$.ajax({
					method: "POST",
					url: reply_list_url,
					data: { id_reply: id_reply}
				})
				.done(function(data) {
					$("#reply-area-"+id_reply).html(data);
					$("#reply-area-"+id_reply).show();
					$('#text-reply-area-'+id_reply).hide();
				});
			}
		}
		function closeReplyArea(id_reply){
			$('#text-reply-area-'+id_reply).hide();
		}

		$(document).on('click','[data-request="up-vote-answer"]',function(){
			$('#popup').show(); 
			var $this   = $(this);
			var $url    = $this.data('url');

			$.ajax({
				url: $url, 
				cache: false, 
				contentType: false, 
				processData: false, 
				type: 'get',
				success: function($response){
					$('#popup').hide();
					if($this.hasClass('active')){
						$this.removeClass('active');
					}else{
						$this.addClass('active');
						var down_vote_class = $this.next().hasClass('active');
						if(down_vote_class){
							$this.next().removeClass('active');
						}
					}

					$('#upvote_count_'+$response.answer_id).html($response.data.upvote_count);
					$('#downvote_count_'+$response.answer_id).html($response.data.downvote_count);

				},error: function(error){
					$('#popup').hide();
				}
			}); 
		});

		$(document).on('click','[data-request="down-vote-answer"]',function(){
			$('#popup').show(); 
			var $this   = $(this);
			var $url    = $this.data('url');

			$.ajax({
				url: $url, 
				cache: false, 
				contentType: false, 
				processData: false, 
				type: 'get',
				success: function($response){
					$('#popup').hide();
					if($this.hasClass('active')){
						$this.removeClass('active');
					}else{
						$this.addClass('active');
						var up_vote_class = $this.prev().hasClass('active');
						if(up_vote_class){
							$this.prev().removeClass('active');
						}
					}

					$('#upvote_count_'+$response.answer_id).html($response.data.upvote_count);
					$('#downvote_count_'+$response.answer_id).html($response.data.downvote_count);

				},error: function(error){
					$('#popup').hide();
				}
			}); 
		});

		$('#give_answer').on('click',function(){
			if($('#ask_main_answer').hasClass('none')){
				$('#ask_main_answer').show();
				$('#ask_main_answer').removeClass('none');
				$('#ask_main_answer').addClass('show');
			}else{
				$('#ask_main_answer').hide();
				$('#ask_main_answer').removeClass('show');
				$('#ask_main_answer').addClass('none');
			}
		});

        $(document).on('click','[data-request="follow-post"]',function(){
            $('#popup').show(); 
            var $this = $(this);
            var $url    = $this.data('url');
            $.ajax({
                url: $url, 
                cache: false, 
                contentType: false, 
                processData: false, 
                type: 'get',
                success: function($response){
                    $('#popup').hide();
                    if( $this.hasClass('active')){
                        $this.removeClass('active');
                        $this.html($response.data);
                    }else {
                        $this.addClass('active');
                        $this.html($response.data);
                    }
                },error: function(error){
                    $('#popup').hide();
                }
            });
        });

	</script>
@endsection

	@section('content')
	<!-- Banner Section -->
	@if(Request::get('stream') != 'mobile')
	<div class="static-heading-sec">
		<div class="container-fluid">
			<div class="static Heading">                    
				<h1>Question Details</h1>                        
			</div>                    
		</div>
	</div>
	@endif
	<!-- /Banner Section -->
	<!-- Main Content -->
	<div class="contentWrapper">
		<section class="aboutSection questions-listing">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-sm-8 col-xs-12">
						<div class="left-question-section question-details">
							<div class="details">
								<ul class="general-questions-list">
									<li>
										<div class="question-wrap question-wrap-detail">
											<h5>{{$question['question_description']}}</h5>
											<div class="question-author question-detail-author question-listing">
												<div class="posted-on">
													<div class="question-author-action">
														<label class="posted-label">Posted <span class="posted-date"> {{___ago($question['approve_date'])}}</span></label>
													</div>
												</div>
											</div>
											<div class="row shared-row">
												<div class="col-md-3 col-sm-3 col-xs-12">
													<div class="count-wrap">
														<h6 class="reply-counts">{{$question['total_reply']}} {{str_plural('Reply',$question['total_reply'])}}</h6>
													</div>
												</div>
												<div class="col-md-9 col-sm-9 col-xs-12">
													<div class="listing-dropdown text-right">
														<ul>
															<li>
																@if(!empty(\Auth::user()) && \Auth::user()->id_user != $question['id_user'])
																@php
																if($question['is_ques_following'] == 1){
																	$is_question_following = 'active';
																	$follow_text_ques = 'Following this Question';
																}else{
																	$is_question_following = '';
																	$follow_text_ques = 'Follow this Question';
																}
																@endphp
																<a href="javascript:void(0);" class="follow-icon {{$is_question_following}}" data-request="follow-post" data-url="{{url(sprintf('/mynetworks/community/follow-post?post_id=%s&section=%s',$question['id_question'],'question'))}}">{{$follow_text_ques}}
																</a>
																@endif
															</li>
															<li>
																<div class="dropdown socialShareDropdown">
																	<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans("website.W0908")}}</a>
																	<ul class="dropdown-menu">
																		@php
																		$question_share_url = url("/network/community/forum/question/".___decrypt($id_question));
																		@endphp
																		<li>
																			<a href="javascript:void(0);" class="linkdin_icon">
																				<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
																				<script type="IN/Share" data-url="{{$question_share_url}}"></script>
																				<img src="{{asset('images/linkedin.png')}}">
																			</a>
																		</li>
																		<li>
																			<a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{$question_share_url}}" target="_blank">
																				<img src="{{asset('images/facebook.png')}}">
																			</a>
																		</li>
																		<li>
																			<a href="https://web.whatsapp.com/send?text={{$question_share_url}}" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="{{asset('images/whatsapp-logo.png')}}"></a>
																		</li>
																	</ul>
																</div>   
															</li>
															<li>
																<div class="post-answer">
																	<a id="give_answer" class="reply-answer">Post answer</a>
																</div>
															</li>
														</ul>
													</div> 
												</div>
											</div>    
										</div>
									</li>                               
								</ul>
								@if(!empty(\Auth::user()))
								<div class="ask-question none" id="ask_main_answer" style="display:none;">
									<form role="add-talent" action="{{url('/network/community/forum/answer/add/'.$id_question)}}" method="POST" class="question-form">
										<input type="hidden" name="_method" value="PUT">
										{{ csrf_field() }}
										<input type="hidden" name="id_parent" value="0">
										<div class="questionform-box">
											<p>Post Your Answer</p>
											<div class="form-element form-group big-textarea">
												<textarea name="answer_description" class="form-control" placeholder="Enter Your Answer"></textarea>
											</div>
											@if($company_profile != 'individual')
											<div class="form-group form-element">
												<div>
													<select name="type">
														<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
														<option value="firm">Post as firm</option>
													</select>                                               
												</div>
											</div>
											@else
											<div class="form-group form-element" style="display:none;">
												<div>
													<select name="type">
														<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
													</select>                                               
												</div>
											</div>
											@endif
											<div class="form-group button-group">
												<div class="form-btn-set submit-solution">
													<input data-request="ajax-submit" data-target='[role="add-talent"]' type="button" class="button" value="{{trans('website.W0393')}}" />
												</div>
											</div>                                
										</div>
									</form>
								</div>
								@endif
							</div>
							<div class="answers-list answer-list-wrapper">
								<div class="question-detail-head">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6>All Answers ({{count($answer)}})</h6>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12" style="float:left;">
											<div class="form-group form-element">
												<div class="text-right sort-dropdown">
													<select id="order" name="order" onchange="window.location='?order='+this.value;">
														<option>Sort by</option>
														<option value="ASC" @if($orderBy == 'ASC') selected="selected" @endif>Posted Date (ASC)</option>
														<option value="DESC" @if($orderBy == 'DESC') selected="selected" @endif>Posted Date (DESC)</option>
														<option value="Upvote" @if($orderBy == 'Upvote') selected="selected" @endif>Most Upvoted</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<ul class="answer-chat">
									@foreach($answer as $a)
									<li class="promoted-answer">
										<div class="answer-wrapper">
											<div class="answer-level">
												<p> {{$a['answer_description']}}</p>
												<div class="question-author listing-author-wrapper question-listing">
													<div class="flex-cell answer-cell">
														@if(!empty($a['filename']))
														<img src="{{asset($a['filename'])}}" alt="image" class="question-author-image">
														@else
														<img src="{{asset('images/sdf.png')}}" alt="image" class="question-author-image">
														@endif
														<span class="question-author-action">
															@if($a['type'] == 'individual')
															<h4>{{$a['person_name']}}</h4>
															@else
															<h4>{{$a['firm_name']}}</h4>
															@endif
															<span>{{___ago($a['approve_date'])}}</span>
														</span>
													</div>
													<div class="post-link">
														@if(!empty(\Auth::user()))
														<a href="javascript:;" onclick="addReply({{$a['id_answer']}});" class="reply-answer">Post Answer</a>
														@endif
													</div>
													<div class="dnt-touch">
														@php
														$upvote = '';
														$downvote = '';
														if($a['saved_answer_vote'] == 'upvote'){
															$upvote = 'active';
														}elseif($a['saved_answer_vote'] == 'downvote'){
															$downvote = 'active';
														}else{
															$upvote = '';
															$downvote = '';
														}
														@endphp
														{{-- For any change in below links, change JS code also. --}}
														<a href="javascript:void(0)" class="upvote {{$upvote}}"  data-request="up-vote-answer" data-url="{{ url(sprintf('/mynetworks/upvote_answer?answer_id=%s',$a['id_answer']))}}">Upvote <span id="upvote_count_{{$a['id_answer']}}">{{$a['answer_upvote_count']}}</span>
														</a>
														<a href="javascript:void(0)" class="downvote {{$downvote}}"  data-request="down-vote-answer" data-url="{{ url(sprintf('/mynetworks/downvote_answer?answer_id=%s',$a['id_answer']))}}">Downvote <span id="downvote_count_{{$a['id_answer']}}">{{$a['answer_downvote_count']}}</span>
														</a>
													</div>
													{{--Hire follow/following link as user will not follow himself--}}
													@if(\Auth::user() && \Auth::user()->id_user != $a['id_user'])
													<div class="forum-follow-detail">
														@php
														if($a['is_following'] == 1){
															$comment_is_following = 'active';
															$comment_follow_text  = 'Following';
														}else{
															$comment_is_following = '';
															$comment_follow_text  = 'Follow';
														}
														@endphp
														<a href="javascript:void(0);" class="follow-icon follow_user_{{$a['id_user'].' '.$comment_is_following}}" data-user_id="{{$a['id_user']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$a['id_user']))}}">{{$comment_follow_text}}
														</a>
													</div>
													@endif                                            
												</span>
											</div>

											{{--Sub comment listing--}}
											@if($a['has_child'] == 1)
											<ul class="subcomment-wrapper answer-chat">
												@foreach($a['has_child_answer'] as $key=>$value)
												<li class="subcomment">
													<div class="answer-wrapper question-listing">
														<div class="answer-level">
															<p> {{$value['answer_description']}}</p>
															<div class="question-author listing-author-wrapper question-listing">
																<div class="flex-cell">
																	@if(!empty($value['filename']))
																	<img src="{{asset($value['filename'])}}" alt="image" class="question-author-image">
																	@else
																	<img src="{{asset('images/sdf.png')}}" alt="image" class="question-author-image">
																	@endif
																	<span class="question-author-action">
																		@if($value['type'] == 'individual')
																		<h4>{{$value['person_name']}}</h4>
																		@else
																		<h4>{{$value['firm_name']}}</h4>
																		@endif
																		<span>{{___ago($value['created'])}}</span>
																	</span>
																</div>
																<div class="dnt-touch">
																	@php
																	$sub_upvote = '';
																	$sub_downvote = '';
																	if($value['saved_answer_vote'] == 'upvote'){
																		$sub_upvote = 'active';
																	}elseif($value['saved_answer_vote'] == 'downvote'){
																		$sub_downvote = 'active';
																	}else{
																		$sub_upvote = '';
																		$sub_downvote = '';
																	}
																	@endphp
																	{{-- For any change in links, change JS code also. --}}
																	<a href="javascript:void(0)" class="upvote {{$sub_upvote}}"  data-request="up-vote-answer" data-url="{{ url(sprintf('/mynetworks/upvote_answer?answer_id=%s',$value['id_answer']))}}">Upvote <span id="upvote_count_{{$value['id_answer']}}">{{$value['answer_upvote_count']}}</span>
																	</a>
																	<a href="javascript:void(0)" class="downvote {{$sub_downvote}}"  data-request="down-vote-answer" data-url="{{ url(sprintf('/mynetworks/downvote_answer?answer_id=%s',$value['id_answer']))}}">Downvote <span id="downvote_count_{{$value['id_answer']}}">{{$value['answer_downvote_count']}}</span>
																	</a>
																</div>
																@if(\Auth::user() && \Auth::user()->id_user != $value['id_user'])
																<div class="forum-follow-detail">
																	@php
																	if($value['is_following'] == 1){
																		$sub_comment_is_following = 'active';
																		$sub_comment_follow_text  = 'Following';
																	}else{
																		$sub_comment_is_following = '';
																		$sub_comment_follow_text  = 'Follow';
																	}
																	@endphp
																	<a href="javascript:void(0);" class="follow-icon follow_user_{{$value['id_user'].' '.$sub_comment_is_following}}" data-user_id="{{$value['id_user']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$value['id_user']))}}">{{$sub_comment_follow_text}}
																	</a>
																</div>
																@endif
															</div>
														</div>
													</div>
												</li>            
												@endforeach
											</ul>
											<a href="javascript:;" onclick="loadReply({{$a['id_answer']}})" class="reply-answer" style="display:none;">| View reply</a>
											@endif
										</div>
										<div id="add-reply-response-{{$a['id_answer']}}"></div>
										<div id="reply-area-{{$a['id_answer']}}"></div>
									</li>
									@if(!empty(\Auth::user()))
									<div id="text-reply-area-{{$a['id_answer']}}" style="display: none;">
										<div class="questionform-box">
											<h2 class="form-heading">{{trans('website.W0451')}}</h2>
											<div class="form-element form-group big-textarea" id="text-reply-area2-{{$a['id_answer']}}">
												<textarea id="answer_description_{{$a['id_answer']}}" name="answer_description_{{$a['id_answer']}}" class="form-control" placeholder="Enter Your Answer"></textarea>
												<span id="text-reply-error-area-{{$a['id_answer']}}"></span>
											</div>
											@if($company_profile != 'individual')
											<div class="form-group form-element">
												<div>
													<select name="answer_type_{{$a['id_answer']}}">
														<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
														<option value="firm">Post as firm</option>
													</select>                                               
												</div>
											</div>
											@else
											<div class="form-group form-element" style="display:none;">
												<div>
													<select name="answer_type_{{$a['id_answer']}}">
														<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
													</select>                                               
												</div>
											</div>
											@endif
											<div class="row form-group button-group">
												<div class="col-md-5 col-sm-5 col-xs-6 form-btn-set submit-solution">
													<input onclick="insertReply({{$a['id_answer']}});" type="button" class="button" value="{{ trans('website.W0393') }}" />
												</div>
											</div>
										</div>
									</div>
									<br/>
									@endif
									@endforeach
								</ul>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="related-questions">
							<div class="search-question-form">
								<h3 class="form-heading top-margin-20px">{{trans('website.W0949')}}</h3>
								<form method="get" action="{{url('network/community/forum')}}" class="form-inline align-center">
										<!-- <div class="form-group custom-class">
											<input type="text" name="search_question" class="form-control custom-box" placeholder="Enter to search">
											<button type="submit" class="btn btn-default">Search</button>
										</div> -->
										<div class="search-wrapper detail-search-wrapper">
											<input type="search" name="search_question" class="form-control" placeholder="Enter to search">
											<buttton class="btn button">
												<img src="{{asset('images/white-search-icon.png')}}">
											</buttton>
										</div>
									</form>           
								</div>
								@if(!empty(\Auth::user()))
								<div class="first-question-section">
									<p>{{trans('website.W0963')}}</p>
									<a href="{{url('/network/community/forum/question/ask')}}" class="button bottom-margin-10px inline">{{trans('website.W0953')}}</a>
								</div>
								@endif
								<div class="other-question-section">
									@if(!empty($related_question))
									<h3 class="form-heading">{{trans('website.W0954')}}</h3>
									<ul>
										@foreach($related_question as $r)
										<li>
											<a href="{{url('network/community/forum/question/'.___encrypt($r['id_question']))}}"><h4>{{$r['question_description']}}</h4>
											</a>
										</li>
										@endforeach
									</ul>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</section> 
		</div>
		<!-- /Main Content -->
		<input type="hidden" id="add-reply" value="{{url('/network/community/forum/answer/add/'.$id_question)}}" />
		<input type="hidden" id="list-reply" value="{{url('/network/community/forum/load/answer/'.$id_question)}}" />
		@endsection