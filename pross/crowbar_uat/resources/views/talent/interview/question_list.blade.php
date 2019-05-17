<!-- Main Content -->
<div class="contentWrapper">
    <div class="afterlogin-section interview-section">
        <div class="container">
            <div class="interview-heading-section">
                <h2>{{trans('website.W0390')}}</h2>
                {{--<h5 class="">Total Points:<span> 69/125</span></h5>--}}
            </div>
            <p class="interview-sub-description">{{trans('website.W0391')}}</p>
            {!!Form::open(['url'=>[sprintf('%s/interview', TALENT_ROLE_TYPE)],'method'=>'POST'])!!}

            @php
            $scriptCode = '';
            $counter = 0;
            @endphp

            {{ ___alert(null) }}
            <div class="accordian-wrapper">
				@foreach($questionList as $pp)
                <div class="interview-accordian active">
                    <h5 class="accordian-tab">{{$pp->question_type}}<span class="accordian-count">{{--11/25--}}</span></h5>
                    <ul class="accordian-content" style="display:block;">
                        @if(!empty($pp->question))
							@foreach($pp->question as $ques)
	                        <li>
	                            <div class="interview-question">
	                                <h6>{{$ques->question}}</h6>
	                            </div>
	                            @php
                                $question_rate = 0;
                                $hidden_question_rate = '';
                                if(!empty(old('res_rate.'.$ques->id))){
                                	$hidden_question_rate = $question_rate = old('res_rate.'.$ques->id);
                            	}
                                elseif(isset($ques->question_rate)){
                                	$hidden_question_rate = $question_rate = $ques->question_rate;
                            	}
                            	$question_comment = '';
                                if(!empty(old('res_comment.'.$ques->id))){
                                	$question_comment = old('res_comment.'.$ques->id);
                            	}
                                elseif(isset($ques->question_comment)){
                                	$question_comment = $ques->question_comment;
                            	}
                                @endphp
	                            <div class="question-rating">
	                                <div class="ratings-container">
	                                    <div class="rating-box" active-till="{{$question_rate}}">
                                            <span class="star-stars" id="star_rate_{{$ques->id}}">
                                                <span class="star-icon pull-left" data-starvalue="1"></span>
                                                <span class="star-icon pull-left" data-starvalue="2"></span>
                                                <span class="star-icon pull-left" data-starvalue="3"></span>
                                                <span class="star-icon pull-left" data-starvalue="4"></span>
                                                <span class="star-icon pull-left" data-starvalue="5"></span>
                                            </span>
                                            <span id="review-value">{{$question_rate}}</span>
                                            <input type="hidden" name="res_rate[{{$ques->id}}]" id="review-value-text" class="rate-response" value="{{$hidden_question_rate}}" />
                                        </div>
	                                </div>
                                    @php
                                        $scriptCode .= "loadStar('star_rate_".$ques->id."', ".$question_rate.");";
                                    @endphp
	                            </div>
	                            <div class="question-comment">
	                                <input type="text" name="res_comment[{{$ques->id}}]" value="{{$question_comment}}" class="form-control" maxlength="250" placeholder="Write your comments">

	                            </div>
	                        </li>
	                        @endforeach
	                    @else

						@endif
                    </ul>
                </div>
                @endforeach
            </div>            
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-7 col-sm-7 col-xs-6">
                            <a href="{{$skip_url}}">
                                <button type="button" class="greybutton-line" value="{{trans('website.W0392')}}">{{trans('website.W0392')}}</button>
                            </a>
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-6">
                            <button type="submit" class="button" value="{{trans('website.W0393')}}">{{trans('website.W0393')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            {!!Form::close()!!}
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

    function starFunction(element){
	    var rating_obj = $(element).closest('.rating-box');
	    var e = $(element).attr("data-starValue");
	    rating_obj.find('.star-icon').removeClass('active-star');

	    for(var i=1; i<= e; i++){
	        rating_obj.find('.star-icon[data-starValue="'+i+'"]').addClass('active-star');
	    }
	    rating_obj.closest('.rating-box').attr('active-till',e);
	}

	$(".star-icon").on("mouseover", function() {
	    starFunction(this);
	}).on("mouseleave", function() {
	    if (!$(this).parent().hasClass('clicked')) {
	        $(this).removeClass("active-star").removeAttr('style');
	    }
	}).on("click", function() {
	    $(this).parent().addClass('clicked');
	    starFunction(this);
	    var e = $(this).attr('data-starValue');
	    $(this).closest('.rating-box').find("#review-value").text(e);
        $(this).closest('.rating-box').find("#review-value-text").val(e);
	})

	$('.rating-box').on("mouseleave", function() {
	    var selected_val = $(this).find('#review-value').text();
	    console.log(selected_val);
	    if(selected_val ==0){
	        $(this).removeAttr('active-till');
	        $(this).find('.star-icon').removeClass('active-star');
	    }else{
	        var e = $(this).find('.star-icon[data-starvalue="'+selected_val+'"]');
	        starFunction(e);
	    }
	});
    {!! $scriptCode !!}
	</script>
@endpush
