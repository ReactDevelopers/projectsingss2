<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    {{-- <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'> --}}
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link href="{{ mix(Spark::usesRightToLeftTheme() ? 'css/app-rtl.css' : 'css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @stack('scripts')

    <!-- Global Spark Object -->
    @include('partials.common.global-spark-object')
</head>
<body>
    <div class="alert alert-warning offline-notification">
        @lang('Not Connected! Offline Mode.')
    </div>
    <div id="spark-app" v-cloak>
        <!-- Navigation -->
        @if (Auth::check())
            @include('spark::nav.user')
        @else
            @include('spark::nav.guest')
        @endif

        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Application Level Modals -->
        @if (Auth::check())
            @include('spark::modals.notifications')
            @include('spark::modals.support')
            @include('spark::modals.session-expired')
        @endif
    </div>

    <!-- JavaScript -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="/js/sweetalert.min.js"></script>
    <!-- <script src="https://cdn.logrocket.io/LogRocket.min.js" crossorigin="anonymous"></script> -->
    <!-- <script>window.LogRocket && window.LogRocket.init('rzptha/contrat-lodge');</script> -->

    @if (Auth::check())
        <!-- <script>
            LogRocket.identify('rzptha:contrat-lodge:Ae0gApAz3EnwWQMvm9o3', {
                name: '<?php echo Auth::user()->name; ?>',
                email: '<?php echo Auth::user()->email; ?>',
            });
        </script> -->
    @endif

</body>
</html>
