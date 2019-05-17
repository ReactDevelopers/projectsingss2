@extends('layouts.front.main')

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
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
    <section class="greyBar-Heading">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>Help Center</h4>
                </div>
            </div>
        </div>
    </section>    
    @if(!empty($banner['help-center'][0]))
        <section class="about-banner-section">
            <img src="{{ asset("uploads/banner/".$banner['help-center'][0]->banner_image) }}">
        </section>
    @endif
    <section class="search-box-wrapper">
        <form action="" method="GET" class="form-horizontal">
            <div class="search-banner">
                <div class="container form-horizontal">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" value="{{ \Request::get('search') }}" name="search" placeholder="Search" class="form-control" />
                            <button type="submit" class="button">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <section class="help-blocks">
        <div class="container">
            <ul class="help-blocks-list grid">
                @if(!empty($faq))
                    @foreach($faq as $topicKey => $topicValue)
                        @if(!empty(array_filter(array_column($topicValue['topic_category'],'category_post'))))
                            <li class="grid-item">
                                <div class="help-block-inner">
                                    <h3 class="form-heading">{{ $topicValue['description']['title'] }}</h3>
                                    @foreach($topicValue['topic_category'] as $categoryKey => $categoryValue)
                                        @if(!empty($categoryValue['category_post']))
                                            <div class="accordian-block-item {{$categoryKey == 0 ? 'active' : ''}}">
                                                <h5 class="help-blocks-accordian-title">{{ $categoryValue['description']['title'] }}</h5>
                                                <ul class="help-blocks-accordian-content">
                                                    @foreach($categoryValue['category_post'] as $postKey => $postValue)
                                                        <li>
                                                            <a href="{{ url(sprintf('page/faq-detail?faq=%s',___encrypt($postValue['description']['faq_id'])))}}">{{$postValue['description']['title']}}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    @endforeach
                @endif
                <li class="grid-item">
                   <div class="help-block-inner">
                       <h3 class="form-heading">{{ trans('website.W0507') }}</h3>
                       <ul class="contact-listing">
                           <li>
                               <h6>{{ trans('website.W0054') }}</h6>
                               <p>{{\Cache::get('configuration')['office_address']}}</p>
                           </li>
                           <li>
                               <h6>{{ trans('website.W0733') }}</h6>
                               <ul class="contact-options">
                                   <li>
                                       {{trans('website.W0007')}}: <a href="tel:{{\Cache::get('configuration')['contact_number']}}">{{\Cache::get('configuration')['contact_number']}}</a>
                                   </li>
                                   <li>
                                       {{trans('website.W0008')}}: <a href="{{\Cache::get('configuration')['info_email']}}">{{\Cache::get('configuration')['info_email']}}</a>
                                   </li>
                               </ul>
                           </li>
                           <li>
                               <a href="{{ url('page/contact') }}" class="red-link">{{ trans('website.W0734') }}</a>
                           </li>
                       </ul>
                   </div>
                </li>                    
            </ul>
        </div>
    </section>
    @endsection
    @push('inlinescript')
    <script type="text/javascript" src="{{asset('js/masonry.min.js')}}"></script>
    <script type="text/javascript">
        $('.help-blocks-accordian-title').on('click', function(){
            $(this).closest('.help-block-inner').find('.help-blocks-accordian-content').slideUp(function(){
                changeLayout();
            });
            $(this).closest('.help-block-inner').find('.help-blocks-accordian-title').removeClass('active');

            if(!($(this).siblings('.help-blocks-accordian-content').is(':visible'))){
                $(this).addClass('active');
                $(this).siblings('.help-blocks-accordian-content').slideDown(function(){
                    changeLayout();
                });
            }
        });

        function changeLayout() {
            $grid.masonry('layout');

        }

        
        var $grid = $('.grid').masonry({
            columnWidth: '.grid-item',
            itemSelector: '.grid-item',
            percentPosition: true
        });


    </script>
    @endpush