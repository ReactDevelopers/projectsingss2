<div class="post-wrapper-section">
	<div class="post-block">
		<div class="post-header">
			<span class="before-image"></span>
			<div class="">
				<h3><?php echo e($article['title']); ?></h3>
				<label class="posted-label">Posted <span class="posted-date"><?php echo e(___ago($article['updated_at'])); ?></span></label>
			</div>
		</div>
		<div class="post-image">
				<?php if($article['article_img'] != 'none'): ?>
                <span>
                	<img src="<?php echo e($article['article_img']); ?>" alt="Article image">
                </span>
                <?php endif; ?>
		</div>
		<div class="post-content">
			<p>
				<?php echo str_limit($article['description'],25); ?> 
			</p>
				<a href="<?php echo e(url('network/article/detail/'.___encrypt($article['article_id']))); ?>">Read More</a>
		</div>
		<div class="post-footer">
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="comment-author">
	                	<img src="<?php echo e($article['user_img']); ?>" class="author-image">
	                	<?php if($article['type'] == 'firm'): ?>
							<h4><?php echo e($article['firm_name']); ?></h4>
	                	<?php else: ?>
							<h4><?php echo e($article['name']); ?></h4>
	                	<?php endif; ?>
						<?php if(!empty(\Auth::user()) && \Auth::user()->id_user != $article['id_user']): ?>
							<div class="user-follow-sec">
	                       		<?php 
	                                if($article['is_following'] == 1){
	                                    $is_following = 'active';
	                                    $follow_text = 'Following';
	                                }else{
	                                    $is_following = '';
	                                    $follow_text = 'Follow';
	                                }
	                             ?>
	                            <a href="javascript:void(0);" class="follow-icon follow_user_<?php echo e($article['id_user'].' '.$is_following); ?>" data-request="home-follow-user" data-user_id="<?php echo e($article['id_user']); ?>" data-url="<?php echo e(url(sprintf('/mynetworks/community/follow-user?user_id=%s',$article['id_user']))); ?>"><?php echo e($follow_text); ?>

	                            </a>
	                		</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="col-md-8 col-sm-8 col-xs-12">
					<div class="social-listing">
						<ul class="social-listing-links text-right">
							<li class="socialShareDropdown comment-links">
								<a href="javascript:void(0);"><?php echo e($article['total_reply']); ?> Comments</a>
							</li>
							<li>
								<div class="dropdown socialShareDropdown">
										<a href="javascript:void(0);" data-toggle="dropdown" aria-expanded="false">Share</a>
										<ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:void(0);" class="linkdin_icon">
                                                    <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                                    <script type="IN/Share" data-url="<?php echo e(url('/network/article/detail/').'/'.$article['article_id']); ?>"></script>
                                                    <img src="<?php echo e(asset('images/linkedin.png')); ?>">
                                                </a>
                                            </li>
                                            <li>
                                                <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(url('/network/article/detail/').'/'.$article['article_id']); ?>" target="_blank">
                                                    <img src="<?php echo e(asset('images/facebook.png')); ?>">
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://web.whatsapp.com/send?text=<?php echo e(url('/network/article/detail/').'/'.$article['article_id']); ?>" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share"><img src="<?php echo e(asset('images/whatsapp-logo.png')); ?>"></a>
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