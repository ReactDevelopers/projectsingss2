<form method="post" role="find-jobs" accept-charset="utf-8" class="form-horizontal" autocomplete="off" onkeypress="return event.keyCode != 13">
    <div class="contentWrapper job-listing-section">
        <div class="container">
            <div class="row mainContentWrapper">
                <a href="javascript:void(0);" class="sidebar-menu"><span></span> Filter</a>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <div id="article_listing" class="timeline timeline-inverse"></div>
                    <div class="pager text-center"><img src="{{ asset('images/loader.gif') }}"></div>
                    <div>
                       <div id="loadmore">
                           <button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="{{ url(sprintf('/_mynetworks/_article')) }}" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role='find-jobs']">{{ trans('website.W0254') }}</button>
                       </div>
                   </div>
                   <input type="hidden" name="page" value="1">
                </div> 
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="related-questions">
                        @if(!empty(\Auth::user()))
                            <div class="search-question-form box-heading">
                                <h3 class="form-heading">Share Your Views</h3>
                                <div class="form-group-wrapper custom-class">
                                    <a href="{{url('/network/article/add')}}" class="button">
                                        {{trans('website.W0968')}}
                                    </a>
                                </div>          
                            </div>
                        @endif
                        @if(!empty(\Auth::user()))
                            <div class="search-question-form box-heading">
                                <h3 class="form-heading">Search Article</h3>
                                <div class="search-wrapper detail-search-wrapper">
                                    <input type="text" value="{{ \Request::get('_search') }}" name="search" placeholder="Search" class="form-control" id="search_networks" data-request="search"/>
                                    <buttton class="btn button searching">
                                        <img src="{{asset('images/white-search-icon.png')}}">
                                    </button>
                                </div>           
                            </div>
                        @endif
                        <div class="other-question-section">
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
    </div>
</form>

@push('inlinescript')
    <link rel="stylesheet" href="{{ asset('css/jquery.mCustomScrollbar.min.css') }}" media="all" type="text/css" />
    <script src="{{ asset('js/jquery.mCustomScrollbar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('script/articlefilter.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(".searching").on('click',function(e) {
            isLoadMore = false;
            $('[name="page"]').val(1);
            $('[data-request="filter-paginate"]').trigger('click');
        });

        $(function() {
            var srch = '{{$search}}';
            if(srch){
                $('#search_networks').val(srch);
                $('[data-request="filter-paginate"]').trigger('click');
            }
        });
    </script>
@endpush
