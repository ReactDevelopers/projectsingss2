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
        <!-- Main Content -->
        <div class="contentWrapper">  
            <section class="login-section">
                <div class="container top-margin-20px">                    
                    <div class="login-inner-wrapper top-margin-20px">                
                        <div class="row has-vr">
                            @if(!empty($email))
                                <div class="col-md-7 col-sm-8 col-xs-12">                            
                                    <h2 class="form-heading">{{trans('website.W0168')}}</h2>
                                    <div class="verifiedAccount">
                                        <p>{{ str_replace('\n',' ',sprintf(trans('general.M0021'),$email)) }}</p>
                                        <a href="{{ sprintf('%s?token=%s',url('/'),\Request::get('token'))}}">{{trans('website.W0169')}}</a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <h2 class="form-heading">
                                        {!! str_replace("×","",strip_tags($alert,'<br>')) !!}
                                    </h2>
                                    <div class="col-md-12">
                                        <a href="{{ url('/') }}">&larr; {{trans('website.W0170')}}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection