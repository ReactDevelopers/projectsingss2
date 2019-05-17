<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>{{ $title or SITE_TITLE }}</title>
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic,greek,latin-ext" rel="stylesheet">
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="temp-login-window">
        <div class="wrapper">
            <div class="headerWrapper">
                <div class="splashHeader">
                    <div class="container-fluid">
                        <div class="col-md-4 col-sm-4 col-xs-12"></div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="login-inner-wrapper" style="padding: 30px;">
                                <div class="bannerContent">
                                    <div class="text-center">
                                        @if(0)<img src="{{ asset('images/splashLogo.png') }}"> <br><br>@endif
                                        <h6 style="padding-bottom: 15px; font-size: 24px; ">Coming Soon</h6> 
                                        <hr style="margin-top:0;">
                                    </div>
                                    {!! Form::open(['url'=>'templogin?redirect=' . $redirectTo]) !!}
                                        @if(Session::has('error'))
                                            <p class="alert alert-danger">{{ Session::get('error') }}</p>
                                        @endif
                                        <div>
                                            <div class="form-group @if($errors->has('email')) has-error @endif">
                                                <label class="control-label">Email Address</label>
                                                <input name="email" type="text" class="form-control">
                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group @if($errors->has('password')) has-error @endif">
                                                <label class="control-label">Password</label>
                                                <input name="password" type="password" class="form-control">
                                                @if ($errors->has('password'))
                                                    <span class="help-block">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                            <div class="">
                                                <button type="submit" class="btn btn-lg redShedBtn" style="margin-top: 10px; width:100%;font-size: 12px; height: 35px; text-transform: none; line-height: 10px; ">Submit</button>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(window).on('load resize', function(){
                $('.temp-login-window .splashHeader').height($(this).height());
            });
        </script>
        @yield('inlinejs')
    </body>
</html>

