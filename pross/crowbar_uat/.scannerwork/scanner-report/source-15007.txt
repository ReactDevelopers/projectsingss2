@extends($extends)

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('css/jquery.easyselect.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
        <link href="{{ asset('css/easy-responsive-tabs.css') }}" rel="stylesheet">
        <link href="{{ asset('css/jquery.nstSlider.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery-ui.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/easyResponsiveTabs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery.nstSlider.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
        <script type="text/javascript">
            /*$(document).on('click','[data-request="follow-question"]',function(){
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
            });*/
        </script>
    @endsection
    
    @section('content')
        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{$title}}</h4>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="contentWrapper">
            <div class="afterlogin-section viewProfile">
                @includeIf($view)
            </div>
        </div>
    @endsection