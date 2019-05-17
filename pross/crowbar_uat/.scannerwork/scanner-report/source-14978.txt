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
        <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
        <script type="text/javascript">
            $(document).on('click','[data-request="follow-question"]',function(){
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
                        if($this.hasClass('active')){
                            $this.removeClass('active');
                            $this.html($response.data);
                            $('.follow_user_'+$response.user_id).removeClass('active');
                            $('.follow_user_'+$response.user_id).html($response.data);
                        }else{
                            $this.addClass('active');
                            $this.html($response.data);
                            $('.follow_user_'+$response.user_id).addClass('active');
                            $('.follow_user_'+$response.user_id).html($response.data);
                        }
                    },error: function(error){
                        $('#popup').hide();
                    }
                });
            });
            
        </script>
        {!! $html->scripts() !!}
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
                        <h1>{{trans('website.W0447')}}</h1>                        
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
                        <div class="col-md-9 col-sm-8 col-xs-12">
                            <div class="left-question-section">
                                <div class="no-table datatable-listing general-questions-list ">
                                    {!! $html->table(); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="related-questions">
                                <div class="search-question-form box-heading">
                                    <h3 class="form-heading">{{trans('website.W0949')}}</h3>
                                    <div class="search-wrapper detail-search-wrapper">
                                        <input type="text" name="search" placeholder="Search" class="form-control" id="search_ques" data-request="search"/>
                                        <buttton class="btn button searching">
                                            <img src="{{asset('images/white-search-icon.png')}}">
                                        </button>
                                    </div>           
                                </div>
                                @if(!empty(\Auth::user()))
                                    <div class="first-question-section">
                                        <h3 class="form-heading top-margin-20px">{{trans('website.W0950')}}</h3>
                                        <ul>
                                            <ol>{{trans('website.W0951')}}</ol>
                                            <ol>{{trans('website.W0952')}}</ol>
                                        </ul>
                                        <a href="{{url('/network/community/forum/question/ask')}}" class="button bottom-margin-10px inline">{{trans('website.W0953')}}</a>
                                    </div>
                                @endif
                                <div class="clearfix"></div>
                                @if(!empty($latest_question))
                                    <div class="other-question-section">
                                        <h3 class="form-heading top-margin-20px">{{trans('website.W0954')}}</h3>
                                        <ul>
                                            @foreach($latest_question as $r)
                                                <li>
                                                    <a href="{{url('/network/community/forum/question/'.___encrypt($r['id_question']))}}"><h4>{{$r['question_description']}}</h4></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    @endsection
    @push('inlinescript')
    <script type="text/javascript">
        $('#search_ques').keyup(function(){
            var search = $(this).val() 
            LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function (e, settings, data) {
                data.search['value'] = search;
            }); 
            window.LaravelDataTables.dataTableBuilder.draw();
        });
    </script>
    @endpush