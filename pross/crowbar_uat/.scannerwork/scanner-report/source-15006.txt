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
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection
{{-- ******INCLUDE JS PAGE-WISE****** --}}

{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
@section('inlinejs')
{{-- CODE WILL GO HERE --}}
@endsection
{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

@section('content')
<!-- Banner Section -->
@if(Request::get('stream') != 'mobile')
<div class="static-heading-sec">
	<div class="container-fluid">
		<div class="static Heading">                    
			<h1>Article Details</h1>                        
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
										<h5>{{$article['title']}}</h5>
										<div class="article-section-detail">
											<div class="posted-on">
												<div class="question-author-action">
													<span>Posted {{___ago($article['created'])}}</span>
												</div>
												@if($article['article_img'] != 'none')
												<div class="article-detail-image">
													<img src="{{$article['article_img']}}" alt="Article image">
												</div>
												@endif
												<div class="article-description">
													<p>{!!$article['description']!!}
													</p>
												</div>          
											</div>
										</div>
										<div class="row shared-row">
											<div class="col-md-3 col-sm-3 col-xs-12">
												<div class="user-detail">
													<div class="comment-author">
														<img src="{{$article['user_img']}}" alt="User-Image" class="author-image">
														@if($article['type'] == 'firm')
														<h4>{{$article['firm_name']}}</h4>
														@else
														<h4>{{$article['user_name']}}</h4>
														@endif
													</div>
												</div>
											</div>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<div class="listing-dropdown text-right">
													<ul>
														<li>
															@if(!empty(\Auth::user()) && \Auth::user()->id_user != $article['id_user'])
															<div class="user-follow-sec">
																@php
																if($article['is_article_following'] == 1){
																	$is_article_following = 'active';
																	$follow_article_text = 'Following this Article';
																}else{
																	$is_article_following = '';
																	$follow_article_text = 'Follow this Article';
																}
																@endphp
																<a href="javascript:void(0);" class="follow-icon {{$is_article_following}}" data-request="follow-post" data-url="{{url(sprintf('/mynetworks/community/follow-post?post_id=%s&section=%s',$article['article_id'],'article'))}}">{{$follow_article_text}}
																</a>
															</div>
															@endif
														</li>
														<li>
															<div class="count-wrap">
																<h6 class="reply-counts">{{$article['total_reply']}} {{str_plural('Comment',$article['total_reply'])}}</h6>
															</div>
														</li>
														@php
																	// Article share detail url
														$article_url = url('/network/article/detail').'/'.$id_article;
														@endphp
														<li>
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
								</li>                               
							</ul>
							@if(!empty(\Auth::user()))
							<div class="ask-question none" id="ask_main_answer">
								<form role="add-talent" action="{{url('/mynetworks/article/answer/add/'.$id_article)}}" method="POST" class="question-form">
									<input type="hidden" name="_method" value="PUT">
									{{ csrf_field() }}
									<input type="hidden" name="id_parent" value="0">
									<div class="questionform-box">
										<p>Post Your Comment</p>
										<div class="form-element form-group">
											<textarea name="answer_description" class="form-control" placeholder="Enter Your Comment"></textarea>
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
							<div>
								<h6>All Comments ({{count($answer)}})</h6>
							</div>
							<ul class="answer-chat">
								@foreach($answer as $a)
								<li class="promoted-answer">
									<div class="answer-wrapper">
										<div class="answer-level">
											<p> {{$a['answer_desp']}}</p>
											<div class="question-author listing-author-wrapper question-listing">
												<div class="flex-cell answer-cell">
													@if(!empty($a['filename']))
													<img src="{{asset($a['filename'])}}" alt="image" class="question-author-image">
													@else
													<img src="{{asset('images/sdf.png')}}" alt="image" class="question-author-image">
													@endif
													<span class="question-author-action">
														@if($a['type'] == 'firm')
														<h4>{{$a['firm_name']}}</h4>
														@else
														<h4>{{$a['person_name']}}</h4>
														@endif
														<span>{{___ago($a['created'])}}</span>
													</span>
												</div>
												{{--Hire follow/following link as user will not follow himself--}}
												@if(!empty(\Auth::user()) && \Auth::user()->id_user != $a['user_id'])
												<div>
													@php
													if($a['is_following'] == 1){
														$comment_is_following = 'active';
														$comment_follow_text  = 'Following';
													}else{
														$comment_is_following = '';
														$comment_follow_text  = 'Follow';
													}
													@endphp
													<a href="javascript:void(0);" class="follow-icon follow_user_{{$a['user_id'].' '.$comment_is_following}}" data-user_id="{{$a['user_id']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$a['user_id']))}}">{{$comment_follow_text}}
													</a>
												</div>
												@endif
												<div class="post-link">
													@if(!empty(\Auth::user()))
													<a href="javascript:;" onclick="addReply({{$a['id_article_answer']}});" class="reply-answer">Post Comment</a>
													@endif
												</div>                                            
											</span>
										</div>

										{{--Sub comment listing--}}
										@if($a['has_child'] == 1)
										<ul class="subcomment-wrapper answer-chat">
											@foreach($a['has_child_answer'] as $key=>$value)
											<li class="subcomment subcomment-article-wrapper">
												<div class="answer-wrapper question-listing">
													<div class="answer-level">
														<p> {{$value['answer_desp']}}</p>
														<div class="question-author listing-author-wrapper question-listing">
															<div class="flex-cell">
																@if(!empty($value['filename']))
																<img src="{{asset($value['filename'])}}" alt="image" class="question-author-image">
																@else
																<img src="{{asset('images/sdf.png')}}" alt="image" class="question-author-image">
																@endif

																<span class="question-author-action">
																	@if($value['type'] == 'firm')
																	<h4>{{$value['firm_name']}}</h4>
																	@else
																	<h4>{{$value['person_name']}}</h4>
																	@endif
																	<span>{{___ago($value['created'])}}</span>
																</span>
															</div>
															<div class="forum-follow-detail">
																@if(!empty(\Auth::user()) && \Auth::user()->id_user != $value['user_id'])
																<div>
																	@php
																	if($value['is_following'] == 1){
																		$sub_comment_is_following = 'active';
																		$sub_comment_follow_text  = 'Following';
																	}else{
																		$sub_comment_is_following = '';
																		$sub_comment_follow_text  = 'Follow';
																	}
																	@endphp
																	<a href="javascript:void(0);" class="follow-icon follow_user_{{$value['user_id'].' '.$sub_comment_is_following}}" data-user_id="{{$a['user_id']}}" data-request="home-follow-user" data-url="{{url(sprintf('/mynetworks/community/follow-user?user_id=%s',$value['user_id']))}}">{{$sub_comment_follow_text}}</a>
																</div>
																@endif
															</div>
														</div>
													</div>
												</div>
											</li>            
											@endforeach
										</ul>
										<a href="javascript:;" onclick="loadReply({{$a['id_article_answer']}})" class="reply-answer" style="display:none;">| View reply</a>
										@endif
									</div>
									<div id="add-reply-response-{{$a['id_article_answer']}}"></div>
									<div id="reply-area-{{$a['id_article_answer']}}"></div>
								</li>
								@if(!empty(\Auth::user()))
								<div id="text-reply-area-{{$a['id_article_answer']}}" style="display: none;">
									<div class="questionform-box">
										<h2 class="form-heading">Post Your Comment</h2>
										<div class="form-element form-group" id="text-reply-area2-{{$a['id_article_answer']}}">
											<textarea id="answer_description_{{$a['id_article_answer']}}" name="answer_description_{{$a['id_article_answer']}}" class="form-control" placeholder="Enter Your Comment"></textarea>
											<span id="text-reply-error-area-{{$a['id_article_answer']}}"></span>
										</div>
										@if($company_profile != 'individual')
										<div class="form-group form-element">
											<div>
												<select name="answer_type_{{$a['id_article_answer']}}">
													<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
													<option value="firm">Post as firm</option>
												</select>                                               
											</div>
										</div>
										@else
										<div class="form-group form-element" style="display:none;">
											<div>
												<select name="answer_type_{{$a['id_article_answer']}}">
													<option value="individual" selected="selected">Post as {{\Auth::user()->name}}</option>
												</select>                                               
											</div>
										</div>
										@endif
										<div class="form-btn-set submit-solution">
											<input onclick="insertReply({{$a['id_article_answer']}});" type="button" class="button" value="{{ trans('website.W0393') }}" />
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
						@if(!empty(\Auth::user()))
						<div class="search-question-form">
							<h3 class="form-heading">Search Article</h3>
							<form method="get" action="{{url('network/article')}}" class="form-inline align-center">
								<div class="search-wrapper detail-search-wrapper">
									<input type="text" name="search_article" placeholder="Enter to search" class="form-control">
									<buttton class="btn button searching">
										<img src="{{asset('images/white-search-icon.png')}}">
									</buttton>
								</div>
							</form>           
						</div>
						@endif
						<div class="other-question-section most-viewed-section">
							@if(!empty($related_article))
							<h3 class="form-heading">Most Viewed Articles</h3>
							<div class="list-article-section">
								@foreach($related_article as $art)
								<div class="article-wrapper">
									<span class="article-image">
										<img src="{{$art['article_img']}}" alt="Articles">
									</span>
									<div class="article-title">
										<a href="{{url('network/article/detail/'.___encrypt($art['article_id']))}}">
											<h3 class="article-heading">{{$art['title']}}</h3>
										</a>
									</div>
									<label class="posted-label">Posted <span class="posted-date">{{___ago($art['created'])}}</span></label>
								</div>
								@endforeach
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section> 
</div>
<!-- /Main Content -->
<input type="hidden" id="add-reply" value="{{url('/mynetworks/article/answer/add/'.$id_article)}}" />
<input type="hidden" id="list-reply" value="{{url('/mynetworks/community/article/load/answer/'.$id_article)}}" />
@endsection


@push('inlinescript')
	<script type="text/javascript">
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
@endpush