<div class="post-wrapper-section">
	<div class="post-block">
		<div class="post-header">
			<span class="before-image"></span>
			<div class="{{-- post-header-content --}}">
				<h3>{{$article['title']}}</h3>
				<label class="posted-label">Posted <span class="posted-date">{{___ago($article['updated_at'])}}</span></label>
			</div>
		</div>
		<div class="post-image">
				@if($article['article_img'] != 'none')
                <span>
                	<img src="{{$article['article_img']}}" alt="Article image">
                </span>
                @endif
		</div>
		<div class="post-content">
			<p>
				{!! str_limit($article['description'],25)  !!} 
			</p>
				<a href="{{url('network/article/detail/'.___encrypt($article['article_id']))}}">Read More</a>
		</div>
		<div class="post-footer">
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="comment-author">
	                	<img src="{{$article['user_img']}}" class="author-image">
	                	@if($article['type'] == 'firm')
							<h4>{{$article['firm_name']}}</h4>
	                	@else
							<h4>{{$article['name']}}</h4>
	                	@endif
						@if(!empty(\Auth::user()) && \Auth::user()->id_user != $article['id_user'])
							<div class="user-follow-sec">
	                       		@php
	                                if($article['is_following'] == 1){
	                                    $is_following = 'active';
	                                    $follow_text = 'Following';
	                                }else{
	                                    $is_following = '';
	                                    $follow_text = 'Follow';
	                                }
	                            @endphp
	                            <a href="javascript:void(0);" class="follow-icon follow_user_{{$article['id_user'].' '.$is_following}}" data-request="home-follow-user" data-user_id="{{$article['id_user']}}" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$article['id_user']))}}">{{$follow_text}}
	                            </a>
	                		</div>
						@endif
					</div>
				</div>

				<div class="col-md-8 col-sm-8 col-xs-12">
					<div class="social-listing">
						<ul class="social-listing-links text-right">
							<li class="socialShareDropdown comment-links">
								<a href="javascript:void(0);">{{$article['total_reply']}} Comments</a>
							</li>
							<li>
								<div class="dropdown socialShareDropdown">
										<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">Share</a>
										<ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:void(0);" class="linkdin_icon">
                                                    <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                                    <script type="IN/Share" data-url="{{url('/network/article/detail/').'/'.$article['article_id']}}"></script>
                                                    <img src="{{asset('images/linkedin.png')}}">
                                                </a>
                                            </li>
                                            <li>
                                                <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u={{url('/network/article/detail/').'/'.$article['article_id']}}" target="_blank">
                                                    <img src="{{asset('images/facebook.png')}}">
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://web.whatsapp.com/send?text={{url('/network/article/detail/').'/'.$article['article_id']}}" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="{{asset('images/whatsapp-logo.png')}}"></a>
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
</div>