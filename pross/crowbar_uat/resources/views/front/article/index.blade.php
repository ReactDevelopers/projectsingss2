@extends($extends)

{{-- ******INCLUDE CSS PAGE-WISE****** --}}
@section('requirecss')
    <link href="{{ asset('css/cropper.min.css') }}" rel="stylesheet">
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
    <script src="{{ asset('js/article-cropper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>   
    <script src="{{ asset('js/article.js') }}" type="text/javascript"></script>
@endsection
{{-- ******INCLUDE JS PAGE-WISE****** --}}

{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
@section('inlinejs')
    <script type="text/javascript">

    </script>
@endsection
{{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

@section('content')
    <!-- Banner Section -->
    @if(Request::get('stream') != 'mobile')
        <div class="static-heading-sec article-heading">
            <div class="container-fluid">
                <div class="static Heading">                    
                    <h1>{{ !empty($page_title) ? $page_title : trans('website.W0964')}}</h1>                        
                </div>                    
            </div>
        </div>
    @endif
    <!-- /Banner Section -->
    <!-- Main Content -->
    <div class="contentWrapper">
        <section class="aboutSection questions-listing">
            <div class="container">
                @includeIf($view)
            </div>
        </section> 
    </div>
@endsection
@push('inlinescript')
@endpush