@extends('layouts.talent.main')

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
                        <h1>{{$title}}</h1>                        
                    </div>                    
                </div>
            </div>
        @endif
        <!-- /Banner Section -->
        <!-- Main Content -->
        <div class="contentWrapper">
            <section class="aboutSection questions-listing ask-question-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="left-question-section">
                                @if(!empty(\Auth::user()))
                                    <form role="add-talent" action="{{url('/mynetworks/community/forum/question/add')}}" method="POST" class="question-form">
                                        <input type="hidden" name="_method" value="PUT">
                                        {{ csrf_field() }}
                                        <div class="questionform-box">
                                            <h2 class="form-heading">{{trans('website.W0955')}}</h2>
                                            <label>{{trans('website.W0956')}}</label>
                                            <div class="form-group form-element">
                                                <textarea name="question_description" class="form-control" placeholder="{{trans('website.W0957')}}"></textarea>
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
	                                        <div class="row form-group ask-question-btns button-group">
	                                            <div class="col-md-12 col-sm-12 col-xs-12">
	                                                <div class="row form-btn-set inner-btns">
	                                                    <div class="col-md-7 col-sm-7 col-xs-6">
	                                                        <a href="{{url('/network/community/forum')}}" class="greybutton-line" value="{{trans('website.W0196')}}">
	                                                            {{trans('website.W0355')}}
	                                                        </a>
	                                                    </div>
	                                                    <div class="col-md-5 col-sm-5 col-xs-6">
	                                                        <input data-request="ajax-submit" data-target='[role="add-talent"]' type="button" class="button" value="{{ trans('website.W0393') }}" />
	                                                    </div>
	                                                </div>
	                                            </div>
	                                        </div>                                
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="related-questions ask-related-question grey-bg">
                                <h3 class="form-heading top-margin-20px">{{trans('website.W0958')}}</h3>
                                <div class="ask-question-list">
                                	<label>{{trans('website.W0959')}}</label>
                                	<p>{{trans('website.W0960')}}</p>
                                </div>
                                <div class="ask-question-list">
                                	<label>{{trans('website.W0961')}}</label>
                                	<p>{{trans('website.W0962')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    @endsection

    @push('inlinescript')
    @endpush