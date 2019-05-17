@extends('layouts.backend.default')

    {{-- ******INCLUDE CSS PAGE-WISE****** --}}
    @section('requirecss')
        <link href="{{ asset('backend/plugins/iCheck/square/square.css') }}" rel="stylesheet">
    @endsection
    {{-- ******INCLUDE CSS PAGE-WISE****** --}}

    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinecss')
        {{-- CODE WILL GO HERE --}}
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    @section('requirejs')
        <script src="{{ asset('backend/plugins/iCheck/icheck.min.js') }}"></script>
    @endsection
    {{-- ******INCLUDE JS PAGE-WISE****** --}}
    
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}
    @section('inlinejs')
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square',
                    radioClass: 'iradio_square',
                    increaseArea: '20%'
                }); 
            });
        </script>
    @endsection
    {{-- ******INCLUDE INLINE-JS PAGE-WISE****** --}}

    @section('content')
        <div class="login-box">
            <div class="login-logo">
                <a href="{{url('/'.ADMIN_FOLDER.'/login')}}"><img src="{{ asset('images/splashLogo.png') }}"></a>
            </div>
            <div class="login-box-body">
                <form autocomplete="off" role="form" method="POST" action="{{ url(sprintf('/%s/%s',ADMIN_FOLDER,'forgot-password')) }}">
                    {{ csrf_field() }}
                    {{ ___alert((!empty($alert))?$alert:'') }}
                    <div class="form-group has-feedback{{ $errors->has(LOGIN_EMAIL) ? ' has-error' : '' }}">
                        <input type="text" class="form-control" name="{{LOGIN_EMAIL}}" value="{{ old(LOGIN_EMAIL,@${LOGIN_EMAIL}) }}" placeholder="Email Address" autocomplete="off">
                        <span class="glyphicon glyphicon-envelope form-control-feedback{{ $errors->has(LOGIN_EMAIL) ? ' text-red' : '' }}"></span>
                        @if ($errors->has(LOGIN_EMAIL))
                            <span class="help-block">{{ $errors->first(LOGIN_EMAIL) }}</span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-default btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                <br>
            </div>
        </div>
    @endsection
