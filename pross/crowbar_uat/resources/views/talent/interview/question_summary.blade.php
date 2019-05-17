<!-- Main Content -->
<div class="contentWrapper">
    <div class="afterlogin-section interview-section">
        <div class="container">
            <div class="interview-heading-section">
                <h2>{{trans('website.W0394')}}</h2>
                <h5 class="">{{trans('website.W0397')}}:<span> {{$optain}}/{{$total}}</span></h5>
            </div>
            <div class="interview-edit-section">
                <div class="row">
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <p class="interview-sub-description">{{trans('website.W0395')}}</p>

                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 text-right">
                        <a href="{{$interview_edit_url}}" class="interview-edit-link">
                            {{trans('website.W0396')}}
                        </a>
                    </div>
                </div>
            </div>
            @php
                $scriptCode = '';
                $counter=0;
            @endphp
            <div class="accordian-wrapper">
				@foreach($questionList as $pp)
                <div class="interview-accordian active">
                    <h5 class="accordian-tab">{{$pp->question_type}}<span class="accordian-count">{{$pp->response_total}}/{{$pp->ques_total}}</span></h5>
                    <ul class="accordian-content" style="display:block;">
                        @if(!empty($pp->question))
							@foreach($pp->question as $ques)
	                        <li>
	                            <div class="interview-question">
	                                <h6>{{$ques->question}}</h6>
	                            </div>
	                            <div class="question-rating">
	                                <div class="ratings-container">
	                                    <div class="rating-box" active-till="{{$ques->question_rate}}">
                                            <span class="star-stars" id="star_rate_{{$ques->id}}">
                                                <span class="star-icon pull-left" data-starvalue="1"></span>
                                                <span class="star-icon pull-left" data-starvalue="2"></span>
                                                <span class="star-icon pull-left" data-starvalue="3"></span>
                                                <span class="star-icon pull-left" data-starvalue="4"></span>
                                                <span class="star-icon pull-left" data-starvalue="5"></span>
                                            </span>
                                            <span id="review-value">{{$ques->question_rate}}</span>
                                        </div>
	                                </div>
                                    @php
                                        $scriptCode .= "loadStar('star_rate_".$ques->id."', ".$ques->question_rate.");";
                                    @endphp
	                            </div>
	                            <div class="question-comment answer-comment">
	                                @if(!empty($ques->question_comment))
                                        {{$ques->question_comment}}
                                    @else
                                        None
                                    @endif
	                            </div>
	                        </li>
	                        @endforeach
	                    @else

						@endif
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- /Main Content -->
@push('inlinescript')
    <script type="text/javascript">
    function loadStar(id, length){
        for(var i=1; i<= length; i++){
            $('#' + id).find('[data-starValue="'+i+'"]').addClass('active-star');
        }
    }
    {!! $scriptCode !!}
    </script>
@endpush