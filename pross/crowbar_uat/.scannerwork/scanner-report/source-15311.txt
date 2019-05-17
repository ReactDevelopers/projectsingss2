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
                <form autocomplete="off" role="form" method="POST" action="{{ url(sprintf('/%s/%s',ADMIN_FOLDER,'authenticate')) }}">
                    {{ csrf_field() }}
                    {{ ___alert((!empty($alert))?$alert:'') }}
                    <div class="form-group has-feedback{{ $errors->has(LOGIN_EMAIL) ? ' has-error' : '' }}">
                        <input type="text" class="form-control" name="{{LOGIN_EMAIL}}" value="{{ old(LOGIN_EMAIL,${LOGIN_EMAIL}) }}" placeholder="Email Address" autocomplete="off">
                        <span class="glyphicon glyphicon-envelope form-control-feedback{{ $errors->has(LOGIN_EMAIL) ? ' text-red' : '' }}"></span>
                        @if ($errors->has(LOGIN_EMAIL))
                            <span class="help-block">{{ $errors->first(LOGIN_EMAIL) }}</span>
                        @endif
                    </div>
                    <div class="form-group has-feedback{{ $errors->has(LOGIN_PASSWORD) ? ' has-error' : '' }}">
                        <input type="password" name="{{LOGIN_PASSWORD}}" class="form-control" value="{{ old(LOGIN_PASSWORD,${LOGIN_PASSWORD}) }}" placeholder="Password" autocomplete="off">
                        <span class="glyphicon glyphicon-lock form-control-feedback{{ $errors->has(LOGIN_PASSWORD) ? ' text-red' : '' }}"></span>
                        @if ($errors->has(LOGIN_PASSWORD))
                            <span class="help-block">{{ $errors->first(LOGIN_PASSWORD) }}</span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" name="{{LOGIN_REMEMBER}}" value="1" @if(!empty(${LOGIN_PASSWORD})) checked @endif> Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-default btn-block">Sign In</button>
                        </div>
                    </div>
                </form>    
                <a href="{{ url('administrator/forgot-password') }}">I forgot my password</a>
                <br>
            </div>
        </div>
    @endsection
