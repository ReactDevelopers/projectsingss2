<div class="post-block recent-article-section">
	<div class="post-header">
		<span class="before-image"></span>
		<div class="post-header-content">
			<h3><?php echo e($article['title']); ?></h3>
			<label>Posted <span><?php echo e(___ago($article['created'])); ?></span></label>
		</div>
	</div>
	<div class="post-content">
		<?php if($article['article_img'] != 'none'): ?>
			<img src="<?php echo e(asset($article['article_img'])); ?>" alt="Article Img">
			<br/>
		<?php endif; ?>
		<p>
			<?php echo str_limit($article['description'],25); ?> 
		</p>
			<a href="<?php echo e(url('network/article/detail/').'/'.___encrypt($article['article_id'])); ?>">Read More</a>
	</div>
	<div class="post-footer">
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="comment-author">
					<img src="<?php echo e(asset($article['user_img'])); ?>" class="author-image">
					<h4><?php echo e($article['user_name']); ?></h4>
					<?php if(\Auth::user() && \Auth::user()->id_user != $article['id_user']): ?>
						<div>
						    <?php 
						        if($article['is_following'] == 1){
						            $comment_is_following = 'active';
						            $comment_follow_text  = 'Following';
						        }else{
						            $comment_is_following = '';
						            $comment_follow_text  = 'Follow';
						        }
						     ?>
						    <a href="javascript:void(0);" class="follow-icon follow_user_<?php echo e($article['id_user'].' '.$comment_is_following); ?>" data-user_id="<?php echo e($article['id_user']); ?>" data-request="home-follow-user" data-url="<?php echo e(url(sprintf('/mynetworks/community/follow-user?user_id=%s',$article['id_user']))); ?>"><?php echo e($comment_follow_text); ?>

						    </a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<div class="social-listing">
					<ul class="social-listing-links text-right">
						<li class="socialShareDropdown comment-links">
							<a href="javascript:void(0);"><?php echo e($article['total_reply']); ?> <?php echo e(str_plural('Comment',$article['total_reply'] )); ?></a>
						</li>
						<li>
							<?php 
                                // Article share detail url
                                $article_url = url('/network/article/detail').'/'.$article['article_id'];
                             ?>
							<div class="dropdown socialShareDropdown">
	                            <a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false"><?php echo e(trans("website.W0908")); ?></a>
	                            <ul class="dropdown-menu">
	                                <li>
	                                    <a href="javascript:void(0);" class="linkdin_icon">
	                                        <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
	                                        <script type="IN/Share" data-url="<?php echo e($article_url); ?>"></script>
	                                        <img src="<?php echo e(asset('images/linkedin.png')); ?>">
	                                    </a>
	                                </li>
	                                <li>
	                                    <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($article_url); ?>" target="_blank">
	                                        <img src="<?php echo e(asset('images/facebook.png')); ?>">
	                                    </a>
	                                </li>
	                                <li>
	                                    <a href="https://web.whatsapp.com/send?text=<?php echo e($article_url); ?>" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="<?php echo e(asset('images/whatsapp-logo.png')); ?>"></a>
	                                </li>
	                            </ul>
	                        </div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php if(!empty($article_last_comment)): ?>
		<div class="recent-comment-block">
			<h3>Recent Comment</h3>
			<p><?php echo e($article_last_comment['answer_desp']); ?></p>
		</div>
	<?php endif; ?>
</div>