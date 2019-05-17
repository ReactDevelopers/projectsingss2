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
        <script src="{{ asset('js/owl.carousel.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
        <script type="text/javascript">
        </script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <!-- Banner Section -->
            <div class="static-heading-sec">
                <div class="container-fluid">
                    <div class="static Heading">                    
                        <h1>Updates</h1>                        
                    </div>                    
                </div>
            </div>
        <!-- /Banner Section -->
        <!-- Main Content -->
        <div class="contentWrapper">
            <section class="aboutSection questions-listing">
                <div class="container">
                	@includeIf($view)
                </div>
            </section> 
        </div>
        <div class="modal fade upload-modal-box add-payment-cards" id="add-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    @endsection
    @push('inlinescript')
    @endpush