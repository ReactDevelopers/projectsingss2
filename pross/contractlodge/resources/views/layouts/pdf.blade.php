<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style type="text/css" media="screen">
@php
echo file_get_contents('https://fonts.googleapis.com/css?family=Titillium+Web:300,400,600,800');
echo file_get_contents(asset('css/app.css'));
@endphp

    body {
        font-family: 'Titillium Web', sans-serif;
        background: none;
        color: black;
        font-size: 0.7rem;
        letter-spacing: 0.2px
    }
    h1, h2, h3, h4, h5, h6 {
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    #page {
        width: 100%;
        margin: 0; padding: 0;
        background: none;
    }
    #header, #menu-bar, #sidebar, h2#postcomment, form#commentform, #footer {
        display: none;
    }
    .entry a:after {
        content: " [" attr(href) "] ";
    }
    #printed-article {
        border: 1px solid #666;
        padding: 10px;
    }
    .red {
        color: #ff1801;
    }
    footer {
        margin-top: 25px;
    }
    dl {
        width: 100%;
    }
    dt {
        float: left;
        width: 40%;
    }
    dd {
        float: left;
        width: 60%;
    }
    table, th, td {
        border: 1px solid #eee;
        border-collapse: collapse;
    }
    th, td {
        padding: 3px 10px;
    }
    th {
        text-align: center;
        background-color: #eee;
        border-color: #ddd;
    }
    tfoot td {
        text-align: left;
        background-color: transparent;
    }

    </style>
</head>
<body>
    @yield('content')
</body>
</html>
