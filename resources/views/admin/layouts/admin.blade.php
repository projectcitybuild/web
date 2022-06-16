<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Panel - @yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ mix('assets/admin/css/admin-light.css') }}" media="screen and (prefers-color-scheme: light)">
    <link rel="stylesheet" href="{{ mix('assets/admin/css/admin-dark.css') }}" media="screen and (prefers-color-scheme: dark)">

    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/brands.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/solid.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/fontawesome.js"></script>

    <script defer src="{{ mix('assets/js/manifest.js') }}"></script>
    <script defer src="{{ mix('assets/admin/js/admin-vendor.js') }}"></script>
    <script defer src="{{ mix('assets/admin/js/admin.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.6/purify.min.js"></script>
</head>
<body>
<div id="app">

    <header class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('front.panel.index') }}">
            <img src="/assets/images/logo.png" alt="Project City Build" height="30">
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
{{--        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">--}}
{{--        <ul class="navbar-nav px-3">--}}
{{--            <li class="nav-item text-nowrap">--}}
{{--                <a class="nav-link" href="#">Sign out</a>--}}
{{--            </li>--}}
{{--        </ul>--}}
    </header>

    <div class="container-fluid">
        <div class="row">
            <x-panel-side-bar />

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield("title")</h1>


                    @yield("toolbar")
                </div>
                @if(Session::has('message'))
                    <div class="alert alert-{{ Session::get('message_type', 'danger') }}">
                        {{ Session::get('message') }}
                    </div>
                @endif

                @yield("body")
            </main>
        </div>
    </div>
</div>
</body>
</html>
