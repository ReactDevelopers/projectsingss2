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
        <link href="{{ asset('css/hidePassword.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/hideShowPassword.js') }}"></script>
        <script type="text/javascript">$('[name="password"]').hidePassword(true);</script>    
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
                @if($link_status !== 'expired')
                    <form autocomplete="off" role="form" method="POST" action="{{ url(sprintf('/%s/%s',ADMIN_FOLDER,'reset-password?token='.$token)) }}">
                        {{ csrf_field() }}
                        {{ ___alert((!empty($alert))?$alert:'') }}
                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input type="password" name="password" class="form-control" value="" placeholder="Password" autocomplete="off">
                            <span class="glyphicon form-control-feedback{{ $errors->has('password') ? ' text-red' : '' }}"></span>
                            @if ($errors->has('password'))
                                <span class="help-block">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-default btn-flat btn-block">Reset Password</button>
                            </div>
                        </div>
                    </form>
                    <a href="{{url('administrator/login')}}">Or sign in as a different user</a>
                    <br>
                @else
                    <div class="text-center">
                        <h4 class="form-heading blue-text">
                            {!! $message !!}
                        </h4>
                    </div>
                @endif
            </div>
        </div>
    @endsection  
