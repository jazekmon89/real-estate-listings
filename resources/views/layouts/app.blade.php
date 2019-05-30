<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <style type="text/css" src="{{ url('plugins/Bootstrap/bootstrap.min.css') }}"></style>
    <style type="text/css" src="{{ url('plugins/AnimateCSS/animate.css') }}"></style>
    @assets('css')
    @assets('cssblock')

    <style type="text/css">
        body {
          background-color: #eee;
        }

        body, h1, p {
          font-family: "Helvetica Neue", "Segoe UI", Segoe, Helvetica, Arial, "Lucida Grande", sans-serif;
          font-weight: normal;
          margin: 0;
          padding: 0;
          text-align: center;
        }
        /*
        .container {
          margin-left:  auto;
          margin-right:  auto;
          margin-top: 177px;
          max-width: 1170px;
          padding-right: 15px;
          padding-left: 15px;
        }*/

        .row:before, .row:after {
          display: table;
          content: " ";
        }

        .col-md-6 {
          width: 50%;
        }

        .col-md-push-3 {
          margin-left: 25%;
        }

        h1 {
          font-size: 48px;
          font-weight: 300;
          margin: 0 0 20px 0;
        }

        .lead {
          font-size: 21px;
          font-weight: 200;
          margin-bottom: 20px;
        }

        p {
          margin: 0 0 10px;
        }

        a {
          color: #3282e6;
          text-decoration: none;
        }

        .col-md-push-3.no-left {
          left: 0;
        }
        #app {
          background: #b0e0e6;
        }
        .navbar.navbar-default {
          margin-bottom: 0px;
        }
        #app > .container {
          padding-top: 22px;
          background: #fff;
          height: 93vh;
        }
        div.col-xs-11.col-sm-4.alert{
          width:16% !important;
        }
        </style>

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        @if(!$document->isEmptyGroupBlock('app-header'))
            @dynamicblock('app-header')
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ url('').'/js/app.js' }}" id="app"></script>
    <script type="text/javascript" src="plugins/BootstrapNotify/bootstrap-notify.min.js" id="bootstrap-notify"></script>
    @assets('js')
    @assets('jsblock')
</body>
</html>
