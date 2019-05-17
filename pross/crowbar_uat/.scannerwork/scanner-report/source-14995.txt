<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="_token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
        <link href="{{ asset('favicon.ico') }}" rel="icon">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>{{ !empty($title) ? $title.' | '.SITE_TITLE : SITE_TITLE }}</title>
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=cyrillic,greek,latin-ext" rel="stylesheet">
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        @yield('requirecss')
        <link href="{{ asset('/bower_components/sweetalert2/dist/sweetalert2.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/loader.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="https://use.fontawesome.com/e26fdfdad2.js"></script>
        {!! \Cache::get('configuration')['google_analytics_code'] !!}
        @yield('inlinecss')
    </head>

    <body>
        <div class="wrapper">
			<div class="web-view-table">
				<table class="table" border="1" cellpadding="2" cellspacing="0">
					<thead>
						<tr>
							<th>Currency</th>
							<th>Rate</th>
						</tr>
					</thead>
					<tbody>
						@php
                            array_walk($currency, function($item){
                                echo sprintf("<tr><td>{$item['iso_code']}</td><td>".str_replace('$', $item['sign'].' ', ___formatdoller($item['conversion_rate'],true,true))."</td></tr>");
                            });
						@endphp
					</tbody>
				</table>	
			</div>
        </div>
        <div id="popup" class="popup">
            <div class="loader">
                <div class="spinning">
                    <img src="{{ asset('images/loading.png') }}" style="border-radius: 30px;"/>
                    <span class="loader-text">{!! trans('website.W0672') !!}</span>
                </div>
            </div>
            <div class="popup_align"></div>
        </div>
        <script src="{{ asset('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    </body>
</html>