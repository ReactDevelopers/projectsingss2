<div class="post-block recent-article-section">
	<div class="post-header">
		<span class="before-image"></span>
		<div class="post-header-content">
			<h3>{{$article['title']}}</h3>
			<label>Posted <span>{{ ___ago($article['created']) }}</span></label>
		</div>
	</div>
	<div class="post-content">
		@if($article['article_img'] != 'none')
			<img src="{{asset($article['article_img'])}}" alt="Article Img">
			<br/>
		@endif
		<p>
			{!! str_limit($article['description'],25)  !!} 
		</p>
			<a href="{{url('network/article/detail/').'/'.___encrypt($article['article_id'])}}">Read More</a>
	</div>
	<div class="post-footer">
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="comment-author">
					<img src="{{asset($article['user_img'])}}" class="author-image">
					<h4>{{ $article['user_name'] }}</h4>
					@if(\Auth::user() && \Auth::user()->id_user != $article['id_user'])
						<div>
						    @php
						        if($article['is_following'] == 1){
						            $comment_is_following = 'active';
						            $comment_follow_text  = 'Following';
						        }else{
						            $comment_is_following = '';
						            $comment_follow_text  = 'Follow';
						        }
						    @endphp
						    <a href="javascript:void(0);" class="follow-icon follow_user_{{$article['id_user'].' '.$comment_is_following}}" data-user_id="{{$article['id_user']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$article['id_user']))}}">{{$comment_follow_text}}
						    </a>
						</div>
					@endif
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<div class="social-listing">
					<ul class="social-listing-links text-right">
						<li class="socialShareDropdown comment-links">
							<a href="javascript:void(0);">{{ $article['total_reply'] }} {{str_plural('Comment',$article['total_reply'] )}}</a>
						</li>
						<li>
							@php
                                // Article share detail url
                                $article_url = url('/network/article/detail').'/'.$article['article_id'];
                            @endphp
							<div class="dropdown socialShareDropdown">
	                            <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">{{trans("website.W0908")}}</a>
	                            <ul class="dropdown-menu">
	                                <li>
	                                    <a href="javascript:void(0);" class="linkdin_icon">
	                                        <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
	                                        <script type="IN/Share" data-url="{{$article_url}}"></script>
	                                        <img src="{{asset('images/linkedin.png')}}">
	                                    </a>
	                                </li>
	                                <li>
	                                    <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{$article_url}}" target="_blank">
	                                        <img src="{{asset('images/facebook.png')}}">
	                                    </a>
	                                </li>
	                                <li>
	                                    <a href="https://web.whatsapp.com/send?text={{$article_url}}" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="{{asset('images/whatsapp-logo.png')}}"></a>
	                                </li>
	                            </ul>
	                        </div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	@if(!empty($article_last_comment))
		<div class="recent-comment-block">
			<h3>Recent Comment</h3>
			<p>{{$article_last_comment['answer_desp']}}</p>
		</div>
	@endif
</div>