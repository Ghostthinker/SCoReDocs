<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Current user -->
    <meta name="user-id" content="{{ optional(Auth::user())->id }}">
    <meta name="user-name" content="{{ optional(Auth::user())->name }}">
    <meta name="user-picture" content="/assets/images/default_user.png">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>
    <script>window.isEp5 = false;window.isEvoli = false;</script>
    @if(config('evoli.host'))
        <script>window.isEvoli = true;</script>
    @endif
    @if(config('services.ep5.cdn-url'))
        <script>window.isEp5 = true;</script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/backbone-min.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/underscore-min.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/edubreakplayer.min.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/edubreakplayer.de.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/three.min.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/OrbitControls.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/Tween.js"></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/DragControls.js" defer></script>
        <script src="{{ config('services.ep5.cdn-url') }}/dist/dat.gui.js" defer></script>
    @endif
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">


    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    @if(config('services.ep5.cdn-url'))
        <link href="{{ config('services.ep5.cdn-url') }}/dist/assets/css/edubreakplayer.min.css" rel="stylesheet">
    @endif
    <style>
    html, body {
        margin: 0;
    }

    .full-height {
        height: 100%;
    }

    .flex-column {
        display: flex;
        flex-direction: column;
    }
    </style>
</head>
<body>
<div id="app" class="full-height flex-column v-application v-application--is-ltr theme--light" data-app>
    <main class="full-height">
        @yield('content')
    </main>
    </div>
</body>
</html>
@include('cookieConsent::index')
