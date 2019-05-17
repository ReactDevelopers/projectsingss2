<div class="post-block questionaire-block">
	<div class="post-header">
		<span class="before-image"></span>
		<div class="post-header-content">
			<a href="{{url('/network/community/forum/question/'.___encrypt($question['id_question']))}}">
				<h3>{{$question['question_description']}}</h3>
			</a>
			<label>Posted <span></span>{{ ___ago($question['created']) }}</label>
		</div>
	</div>
	<div class="post-footer">
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="comment-author">
					<img src="{{asset($question['filename'])}}" class="author-image">
					<h4>{{$question['person_name']}}</h4>
					<br/>
					@if(\Auth::user() && \Auth::user()->id_user != $question['id_user'])
					    <div class="forum-follow-detail">
					        @php
					            if($question['is_following'] == 1){
					                $comment_is_following = 'active';
					                $comment_follow_text  = 'Following';
					            }else{
					                $comment_is_following = '';
					                $comment_follow_text  = 'Follow';
					            }
					        @endphp
					        <a href="javascript:void(0);" class="follow-icon follow_user_{{$question['id_user'].' '.$comment_is_following}}" data-user_id="{{$question['id_user']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$question['id_user']))}}">{{$comment_follow_text}}
					        </a>
					    </div>
					@endif
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<div class="social-listing">
					<ul class="social-listing-links text-right">
						<li class="socialShareDropdown comment-links">
							<a href="javascript:void(0)">{{ $question['total_reply'] }} {{str_plural('Comment',$question['total_reply'] )}}</a>
						</li>
						<li>
							<div class="dropdown socialShareDropdown">
                                <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans("website.W0908")}}</a>
                                <ul class="dropdown-menu">
                                @php
                                 $question_share_url = url("/network/community/forum/question/".$question['id_question']);
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
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>