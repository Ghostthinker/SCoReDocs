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
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/assets/images/score-logo.png" style="height: 40px;" alt="score">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <help></help>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <help :user="{ id: '{{Auth::user()->id}}', email: '{{Auth::user()->email}}' }"></help>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                @if(Auth::getUser()->hasRole('Admin'))
                                    <a class="dropdown-item" href="{{ route('user-administration') }}">
                                        Benutzerverwaltung
                                    </a>
                                @endif
                                @if(Auth::getUser()->hasPermissionTo('can view the assessment docs overview'))
                                    <a class="dropdown-item" href="{{ route('assessment-overview') }}">
                                        Assessmentübersicht
                                    </a>
                                @endif
                                @if(Auth::getUser()->hasPermissionTo('edit templates'))
                                    <a class="dropdown-item" href="{{ route('template') }}">
                                        Vorlagen
                                    </a>
                                @endif
                                <a class="dropdown-item" href="{{ route('archive') }}">
                                    Archiv
                                </a>
                                @if(Auth::getUser()->hasPermissionTo('can export data'))
                                    <a class="dropdown-item" href="{{ route('data-export') }}">
                                        Daten Export
                                    </a>
                                @endif
                                @if(Auth::user()->assessment_doc_id !== null)
                                    <router-link
                                        class="dropdown-item"
                                        :to="{ name: 'project', params: { projectId: {{Auth::getUser()->assessment_doc_id}} } }"
                                    >
                                        Assessment
                                    </router-link>
                                @endif
                                @if(Auth::getUser()->hasPermissionTo('can download agreements data processing'))
                                    <a class="dropdown-item" href="{{ route('download-agreements-data-processing') }}" target="_blank">
                                        Erklärungen zur Datenverarbeitung
                                    </a>
                                @endif
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-4 full-height" style="overflow:scroll; height: 100vH; padding-bottom: 100px!important;">
        @yield('content')
    </main>
    </div>
</body>
</html>
@include('cookieConsent::index')
